<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Permit;
use App\Models\ReminderRule;
use Carbon\Carbon;

class TestReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:reminders {--days=3 : Days from now for expiry date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test data for reminder system testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::today()->addDays($days)->format('Y-m-d');

        $this->info("Creating test data for reminders expiring on: {$targetDate} (in {$days} days)");
        $this->newLine();

        // Create test employee
        $employee = Employee::create([
            'nip' => 'NIP' . rand(1000, 9999),
            'name' => 'Test Employee',
            'email' => 'test.employee' . rand(1000, 9999) . '@company.com',
            'position' => 'Software Developer',
            'dept' => 'IT',
            'location' => 'Jakarta',
            'is_permanent' => false,
            'contract_start' => Carbon::today()->subDays(365)->format('Y-m-d'),
            'contract_end' => $targetDate,
            'resign_date' => null,
        ]);

        $this->info("✅ Created test employee: {$employee->name} ({$employee->email})");
        $this->info("   Contract expires: {$targetDate}");

        // Create test permit
        $permit = Permit::create([
            'employee_id' => $employee->id,
            'type' => 'SIM',
            'number' => 'TEST-' . rand(1000, 9999),
            'holder' => $employee->name,
            'asset_location' => 'Jakarta',
            'issued_at' => Carbon::today()->subDays(365),
            'expires_at' => $targetDate,
            'pic' => 'coldh4rtd@gmail.com',
            'status' => 'active',
            'notes' => 'Test permit for reminder system',
        ]);

        $this->info("✅ Created test permit: {$permit->type} - {$permit->holder}");
        $this->info("   Expires: {$targetDate}");
        $this->info("   PIC: {$permit->pic}");

        $this->newLine();
        $this->info("Test data created successfully!");
        $this->info("Run 'php artisan reminders:run' to test the reminder system.");
        $this->info("Check your Mailtrap inbox to see the emails.");

        return self::SUCCESS;
    }
}