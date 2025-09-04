<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Permit;
use App\Models\ReminderRule;
use App\Models\ReminderLog;
use App\Mail\ReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RunDailyReminders extends Command
{
    protected $signature = 'reminders:run';
    protected $description = 'Kirim reminder kontrak dan perizinan H-xx';

    public function handle(): int
    {
        $today = Carbon::today();
        $processed = 0;

        // Process contract reminders
        $contractRules = ReminderRule::where('active', 1)->where('entity', 'contract')->get();
        foreach ($contractRules as $rule) {
            $target = $today->copy()->addDays($rule->days_before)->toDateString();

            $employees = Employee::query()
                ->where('is_permanent', false)
                ->whereNull('resign_date')
                ->whereDate('contract_end', $target)
                ->get();

            foreach ($employees as $employee) {
                $this->processReminder(
                    entity: 'contract',
                    entityId: $employee->id,
                    targetDate: $target,
                    ruleDays: $rule->days_before,
                    recipient: $employee->email,
                    channel: $rule->channel,
                    title: $employee->name
                );
                $processed++;
            }
        }

        // Process permit reminders
        $permitRules = ReminderRule::where('active', 1)->where('entity', 'permit')->get();
        foreach ($permitRules as $rule) {
            $target = $today->copy()->addDays($rule->days_before)->toDateString();

            $permits = Permit::query()
                ->where('status', 'active')
                ->whereDate('expires_at', $target)
                ->get();

            foreach ($permits as $permit) {
                $this->processReminder(
                    entity: 'permit',
                    entityId: $permit->id,
                    targetDate: $target,
                    ruleDays: $rule->days_before,
                    recipient: $permit->pic,
                    channel: $rule->channel,
                    title: $permit->type . ' - ' . $permit->number
                );
                $processed++;
            }
        }

        $this->info("Reminder run: {$today->toDateString()} - Processed: {$processed}");
        return self::SUCCESS;
    }

    private function processReminder(
        string $entity,
        int $entityId,
        string $targetDate,
        int $ruleDays,
        ?string $recipient,
        string $channel,
        string $title
    ): void {
        // Check if already processed (idempotent)
        $exists = ReminderLog::where([
            'entity' => $entity,
            'entity_id' => $entityId,
            'target_date' => $targetDate,
            'rule_days' => $ruleDays,
        ])->exists();

        if ($exists) {
            return; // Skip if already processed
        }

        try {
            if ($recipient) {
                Mail::to($recipient)->send(new ReminderMail(
                    entity: $entity,
                    title: $title,
                    targetDate: $targetDate,
                    daysBefore: $ruleDays,
                ));
                
                ReminderLog::create([
                    'entity' => $entity,
                    'entity_id' => $entityId,
                    'target_date' => $targetDate,
                    'rule_days' => $ruleDays,
                    'recipient' => $recipient,
                    'channel' => $channel,
                    'status' => 'sent',
                    'meta' => null,
                ]);
            } else {
                // No recipient, mark as skipped
                ReminderLog::create([
                    'entity' => $entity,
                    'entity_id' => $entityId,
                    'target_date' => $targetDate,
                    'rule_days' => $ruleDays,
                    'recipient' => null,
                    'channel' => $channel,
                    'status' => 'skipped',
                    'meta' => 'No recipient email',
                ]);
            }
        } catch (\Throwable $ex) {
            ReminderLog::create([
                'entity' => $entity,
                'entity_id' => $entityId,
                'target_date' => $targetDate,
                'rule_days' => $ruleDays,
                'recipient' => $recipient,
                'channel' => $channel,
                'status' => 'failed',
                'meta' => $ex->getMessage(),
            ]);
        }
    }
}
