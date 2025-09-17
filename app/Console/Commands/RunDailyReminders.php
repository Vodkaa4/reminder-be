<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\ReminderRule;
use App\Models\ReminderLog;
use App\Mail\ReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RunDailyReminders extends Command
{
    protected $signature = 'reminders:run';
    protected $description = 'Kirim reminder kontrak H-xx';

    public function handle(): int
    {
        $today = Carbon::today();

        $rules = ReminderRule::where('active',1)->where('entity','contract')->get();
        if ($rules->isEmpty()) {
            $this->info('No active rules.'); return self::SUCCESS;
        }

        foreach ($rules as $rule) {
            $target = $today->copy()->addDays($rule->days_before)->toDateString();

            $employees = Employee::query()
                ->where('is_permanent', false)
                ->whereNull('resign_date')
                ->whereDate('contract_end', $target)
                ->get();

            foreach ($employees as $e) {
                $dupe = ReminderLog::where([
                    'entity'      => 'contract',
                    'entity_id'   => $e->id,
                    'target_date' => $target,
                    'rule_days'   => $rule->days_before,
                ])->exists();

                if ($dupe) continue;

                $recipient = $e->email;

                try {
                    if ($recipient) {
                        Mail::to($recipient)->send(new ReminderMail(
                            entity: 'contract',
                            title: $e->name,
                            targetDate: $target,
                            daysBefore: $rule->days_before,
                        ));
                    }

                    ReminderLog::create([
                        'entity'=>'contract','entity_id'=>$e->id,
                        'target_date'=>$target,'rule_days'=>$rule->days_before,
                        'recipient'=>$recipient,'channel'=>$rule->channel,'status'=>'sent',
                    ]);
                } catch (\Throwable $ex) {
                    ReminderLog::create([
                        'entity'=>'contract','entity_id'=>$e->id,
                        'target_date'=>$target,'rule_days'=>$rule->days_before,
                        'recipient'=>$recipient,'channel'=>$rule->channel,
                        'status'=>'failed','meta'=>$ex->getMessage(),
                    ]);
                }
            }
        }

        $this->info('Reminder run: '.$today->toDateString());
        return self::SUCCESS;
    }
}
