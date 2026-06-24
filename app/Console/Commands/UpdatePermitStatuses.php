<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permit;
use Carbon\Carbon;

class UpdatePermitStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permits:update-statuses {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all permit statuses based on their expiry dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $today = Carbon::today()->format('Y-m-d');
        $renewalThreshold = Carbon::today()->addDays(60)->format('Y-m-d');

        if ($dryRun) {
            $this->info('DRY RUN MODE - Querying what would change...');
            
            $expiredCount = Permit::where('status', '!=', 'expired')
                ->whereDate('expires_at', '<', $today)
                ->count();
                
            $renewalCount = Permit::where('status', '!=', 'renewal')
                ->whereDate('expires_at', '>=', $today)
                ->whereDate('expires_at', '<=', $renewalThreshold)
                ->count();
                
            $activeCount = Permit::where('status', '!=', 'active')
                ->where(function($query) use ($renewalThreshold) {
                    $query->whereNull('expires_at')
                          ->orWhereDate('expires_at', '>', $renewalThreshold);
                })
                ->count();
                
            $updatedCount = $expiredCount + $renewalCount + $activeCount;
            $this->newLine();
            $this->info("DRY RUN RESULTS:");
            $this->info("Would update: {$updatedCount} permits (Expired: {$expiredCount}, Renewal: {$renewalCount}, Active: {$activeCount})");
            return Command::SUCCESS;
        }

        $this->info("Updating permit statuses via bulk query...");

        // Update expired
        $expiredUpdated = Permit::where('status', '!=', 'expired')
            ->whereDate('expires_at', '<', $today)
            ->update(['status' => 'expired']);

        // Update renewal
        $renewalUpdated = Permit::where('status', '!=', 'renewal')
            ->whereDate('expires_at', '>=', $today)
            ->whereDate('expires_at', '<=', $renewalThreshold)
            ->update(['status' => 'renewal']);

        // Update active
        $activeUpdated = Permit::where('status', '!=', 'active')
            ->where(function($query) use ($renewalThreshold) {
                $query->whereNull('expires_at')
                      ->orWhereDate('expires_at', '>', $renewalThreshold);
            })
            ->update(['status' => 'active']);

        $totalUpdated = $expiredUpdated + $renewalUpdated + $activeUpdated;
        
        $this->newLine();
        $this->info("RESULTS:");
        $this->info("Updated: {$totalUpdated} permits (Expired: {$expiredUpdated}, Renewal: {$renewalUpdated}, Active: {$activeUpdated})");
        
        return Command::SUCCESS;
    }
}