<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Permit;
use App\Models\ReminderRule;
use App\Models\ReminderLog;
use App\Mail\ReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RunDailyReminders extends Command
{
    protected $signature = 'reminders:run';
    protected $description = 'Kirim reminder kontrak dan permit H-xx';

    public function handle(): int
    {
        $today = Carbon::today();

        $rules = ReminderRule::where('active',1)->whereIn('entity', ['contract', 'permit'])->get();
        if ($rules->isEmpty()) {
            $this->info('No active rules.'); return self::SUCCESS;
        }

        $this->info("=== DAILY REMINDER RUN - {$today->toDateString()} ===");
        $this->newLine();

        $totalSent = 0;
        $totalSkipped = 0;

        foreach ($rules as $rule) {
            $target = $today->copy()->addDays($rule->days_before)->toDateString();

            $this->info("Processing {$rule->entity} reminders (H-{$rule->days_before} days - {$target})");
            $this->info("Channel: {$rule->channel}");

            // Handle contracts (employees)
            if ($rule->entity === 'contract') {
                $employees = Employee::query()
                    ->where('is_permanent', false)
                    ->whereNull('resign_date')
                    ->where('reminders_muted', false)
                    ->whereDate('contract_end', '<=', $target)
                    ->whereDate('contract_end', '>=', $today)
                    ->get();

                $hrdEmail = env('HRD_EMAIL', 'hrd@example.com');
                $this->info("Found {$employees->count()} contracts matching rule H-{$rule->days_before}");

                foreach ($employees as $e) {
                    $result = $this->processReminder($e, 'contract', $hrdEmail, $e->name, Carbon::parse($e->contract_end)->format('Y-m-d'), $rule);
                    if ($result === 'sent') $totalSent++;
                    if ($result === 'skipped') $totalSkipped++;
                }
            }

            // Handle permits
            if ($rule->entity === 'permit') {
                $permits = Permit::query()
                    ->whereDate('expires_at', '<=', $target)
                    ->whereDate('expires_at', '>=', $today)
                    ->where('status', '!=', 'expired')
                    ->where('reminders_muted', false)
                    ->whereNotNull('pic')
                    ->get();

                $legalEmail = env('LEGAL_EMAIL', 'legal@example.com');
                $this->info("Found {$permits->count()} permits matching rule H-{$rule->days_before}");

                foreach ($permits as $permit) {
                    $result = $this->processReminder($permit, 'permit', $permit->pic, $permit->type . ' - ' . $permit->holder, Carbon::parse($permit->expires_at)->format('Y-m-d'), $rule, $legalEmail);
                    if ($result === 'sent') $totalSent++;
                    if ($result === 'skipped') $totalSkipped++;
                }
            }

            $this->newLine();
        }

        $this->info("=== SUMMARY ===");
        $this->info("Total reminders sent: {$totalSent}");
        $this->info("Total reminders skipped: {$totalSkipped}");

        if ($totalSent > 0) {
            $this->info("✅ Emails sent successfully via configured SMTP!");
        } else {
            $this->warn("⚠️  No reminders were sent today. Check if there are any contracts/permits expiring soon.");
        }

        return self::SUCCESS;
    }

    /**
     * Process reminder for both contracts and permits
     */
    private function processReminder($entity, string $entityType, ?string $recipient, string $title, string $targetDate, $rule, ?string $ccRecipient = null): string
    {
        // Skip if already sent
        $query = ReminderLog::where([
            'entity' => $entityType,
            'entity_id' => $entity->id,
            'rule_days' => $rule->days_before,
            'target_date' => $targetDate,
            'status' => 'sent',
        ]);

        if ($rule->is_recurring && $rule->recurring_interval_days > 0) {
            $thresholdDate = Carbon::today()->subDays($rule->recurring_interval_days - 1);
            $query->whereDate('created_at', '>=', $thresholdDate);
        }

        if ($query->exists()) {
            $this->line("  ⏭️  Skipped: {$title} (already sent)");
            return 'skipped';
        }

        // Skip if no recipient
        if (!$recipient) {
            $this->writeLogOnce([
                'entity' => $entityType,
                'entity_id' => $entity->id,
                'target_date' => $targetDate,
                'rule_days' => $rule->days_before,
                'recipient' => null,
                'channel' => $rule->channel,
                'status' => 'skipped',
                'meta' => ['reason' => 'No email address']
            ], $rule->is_recurring);
            $this->warn("  ⚠️  Skipped: {$title} (no email address)");
            return 'skipped';
        }

        try {
            $mail = Mail::to($recipient);
            
            if ($ccRecipient) {
                $mail->cc($ccRecipient);
            }
            
            $mail->send(new ReminderMail(
                entity: $entityType,
                title: $title,
                targetDate: $targetDate,
                daysBefore: $rule->days_before,
            ));

            // Success - log as sent
            $this->writeLogOnce([
                'entity' => $entityType,
                'entity_id' => $entity->id,
                'target_date' => $targetDate,
                'rule_days' => $rule->days_before,
                'recipient' => $recipient,
                'channel' => $rule->channel,
                'status' => 'sent',
                'meta' => []
            ], $rule->is_recurring);

            $this->info("  ✅ Sent: {$title} -> {$recipient}");
            return 'sent';

        } catch (\Throwable $ex) {
            // Failed - log with error
            $this->writeLogOnce([
                'entity' => $entityType,
                'entity_id' => $entity->id,
                'target_date' => $targetDate,
                'rule_days' => $rule->days_before,
                'recipient' => $recipient,
                'channel' => $rule->channel,
                'status' => 'failed',
                'meta' => ['error' => $ex->getMessage()]
            ], $rule->is_recurring);

            $this->error("  ❌ Failed: {$title} -> {$recipient} ({$ex->getMessage()})");
            return 'failed';
        }
    }

    /**
     * Idempotent writer helper for reminder logs
     */
    private function writeLogOnce(array $data, bool $isRecurring = false): ReminderLog
    {
        return DB::transaction(function () use ($data, $isRecurring) {
            if ($isRecurring) {
                return ReminderLog::create([
                    'entity'      => $data['entity'],
                    'entity_id'   => $data['entity_id'],
                    'target_date' => $data['target_date'],
                    'rule_days'   => $data['rule_days'],
                    'recipient'   => $data['recipient'] ?? null,
                    'channel'     => $data['channel'] ?? 'email',
                    'status'      => $data['status'] ?? 'sent',
                    'meta'        => $data['meta'] ?? [],
                ]);
            }

            return ReminderLog::firstOrCreate(
                [
                    'entity'      => $data['entity'],      // 'contract'|'permit'
                    'entity_id'   => $data['entity_id'],
                    'target_date' => $data['target_date'], // Y-m-d
                    'rule_days'   => $data['rule_days'],   // 60/30/15...
                ],
                [
                    'recipient' => $data['recipient'] ?? null,
                    'channel'   => $data['channel'] ?? 'email',
                    'status'    => $data['status'] ?? 'sent', // 'sent'|'failed'|'skipped'
                    'meta'      => $data['meta'] ?? [],
                ]
            );
        });
    }
}
