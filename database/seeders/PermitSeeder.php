<?php

namespace Database\Seeders;

use App\Models\Permit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permits = [
            [
                'type' => 'SIM',
                'number' => 'SIM-001',
                'holder' => 'PT Andalan Logistik',
                'asset_location' => 'Jakarta',
                'issued_at' => Carbon::now()->subYears(1),
                'expires_at' => Carbon::now()->addDays(60),
                'status' => 'active',
                'pic' => 'sim.pic@example.com',
                'notes' => 'Perpanjang sebelum habis'
            ],
            [
                'type' => 'STNK',
                'number' => 'STNK-002',
                'holder' => 'PT Armada Jaya',
                'asset_location' => 'Bandung',
                'issued_at' => Carbon::now()->subYears(2),
                'expires_at' => Carbon::now()->addDays(30),
                'status' => 'active',
                'pic' => 'stnk.pic@example.com',
                'notes' => 'Perpanjangan dilakukan di Samsat Bandung'
            ],
            [
                'type' => 'KIR',
                'number' => 'KIR-003',
                'holder' => 'CV Transport Sejahtera',
                'asset_location' => 'Surabaya',
                'issued_at' => Carbon::now()->subMonths(6),
                'expires_at' => Carbon::now()->addDays(10),
                'status' => 'renewal',
                'pic' => 'kir.pic@example.com',
                'notes' => 'Harus dicek ulang'
            ],
            [
                'type' => 'IMB/HO',
                'number' => 'IMB-004',
                'holder' => 'PT Properti Mandiri',
                'asset_location' => 'Semarang',
                'issued_at' => Carbon::now()->subYears(5),
                'expires_at' => Carbon::now()->addDays(90),
                'status' => 'active',
                'pic' => 'imb.pic@example.com',
                'notes' => 'Pengajuan baru untuk ekspansi'
            ],
            [
                'type' => 'AMDAL',
                'number' => 'AMDAL-005',
                'holder' => 'PT Energi Bersih',
                'asset_location' => 'Medan',
                'issued_at' => Carbon::now()->subYears(3),
                'expires_at' => Carbon::now()->subDays(5),
                'status' => 'expired',
                'pic' => 'amdal.pic@example.com',
                'notes' => 'Sudah kadaluarsa, perlu diperbarui'
            ],
        ];

        foreach ($permits as $permit) {
            Permit::create($permit);
        }

        $this->command->info('Dummy data permits berhasil dibuat!');
    }
}
