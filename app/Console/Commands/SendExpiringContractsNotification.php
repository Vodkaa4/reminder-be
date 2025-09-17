<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Employee;
use App\Notifications\ContractsExpiringSummaryNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendExpiringContractsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:send-expiring';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $expired = Employee::where('contract_end', '<', today())->get();

    $upcoming = Employee::whereBetween('contract_end', [
            today(), 
            today()->addDays(15)
        ])->get();


        if ($expired->isEmpty() && $upcoming->isEmpty()) {
            $this->info('Tidak ada pegawai yang kontraknya habis.');
            return;
        }

        // Kirim hanya 1 email berisi semua pegawai
        Notification::route('mail', 'jaddlyn@gmail.com')
            ->notify(new ContractsExpiringSummaryNotification($expired, $upcoming));

        $this->info('Summary email kontrak expiring berhasil dikirim!');
    }

}
