<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('employees')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $employees = [
            // Permanent Employees - Production
            [
                'nip' => 'EMP-0001',
                'name' => 'Ahmad Supriadi',
                'email' => 'ahmad.supriadi@company.com',
                'supervisor' => 'Budi Santoso',
                'is_permanent' => true,
                'contract_start' => '2020-01-15',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'Produksi',
                'sect' => 'Assembly',
                'position' => 'Supervisor',
                'location' => 'Plant CILAMPENI',
            ],
            [
                'nip' => 'EMP-0002',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@company.com',
                'supervisor' => 'Ahmad Supriadi',
                'is_permanent' => true,
                'contract_start' => '2021-03-20',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'Produksi',
                'sect' => 'Assembly',
                'position' => 'Operator',
                'location' => 'Plant CILAMPENI',
            ],
            [
                'nip' => 'EMP-0003',
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@company.com',
                'supervisor' => 'Ahmad Supriadi',
                'is_permanent' => true,
                'contract_start' => '2021-06-10',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'Produksi',
                'sect' => 'Packaging',
                'position' => 'Operator',
                'location' => 'Plant CILAMPENI',
            ],

            // Contract Employees - Production
            [
                'nip' => 'EMP-0004',
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@company.com',
                'supervisor' => 'Ahmad Supriadi',
                'is_permanent' => false,
                'contract_start' => now()->subMonths(6)->format('Y-m-d'),
                'contract_end' => now()->addDays(30)->format('Y-m-d'),
                'resign_date' => null,
                'dept' => 'Produksi',
                'sect' => 'Quality Control',
                'position' => 'QC Inspector',
                'location' => 'Plant CILAMPENI',
            ],
            [
                'nip' => 'EMP-0005',
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@company.com',
                'supervisor' => 'Ahmad Supriadi',
                'is_permanent' => false,
                'contract_start' => now()->subMonths(6)->format('Y-m-d'),
                'contract_end' => now()->addDays(15)->format('Y-m-d'),
                'resign_date' => null,
                'dept' => 'Produksi',
                'sect' => 'Maintenance',
                'position' => 'Technician',
                'location' => 'Plant CILAMPENI',
            ],

            // HRD Department
            [
                'nip' => 'EMP-0006',
                'name' => 'Khadizah Aulia',
                'email' => 'sri.mulyani@company.com',
                'supervisor' => 'Budi Siregar',
                'is_permanent' => true,
                'contract_start' => '2019-08-01',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'HRD',
                'sect' => 'Recruitment',
                'position' => 'HR Manager',
                'location' => 'Kantor Pusat',
            ],
            [
                'nip' => 'EMP-0007',
                'name' => 'Bambang Brodjonegoro',
                'email' => 'bambang.brodjonegoro@company.com',
                'supervisor' => 'Sri Mulyani',
                'is_permanent' => true,
                'contract_start' => '2022-01-15',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'HRD',
                'sect' => 'Payroll',
                'position' => 'HR Staff',
                'location' => 'Kantor Pusat',
            ],

            // IT Department
            [
                'nip' => 'EMP-0008',
                'name' => 'Rini Soemarno',
                'email' => 'rini.soemarno@company.com',
                'supervisor' => 'Budi Santoso',
                'is_permanent' => true,
                'contract_start' => '2020-11-01',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'IT',
                'sect' => 'Development',
                'position' => 'IT Manager',
                'location' => 'Kantor Pusat',
            ],
            [
                'nip' => 'EMP-0009',
                'name' => 'Eko Putro',
                'email' => 'eko.putro@company.com',
                'supervisor' => 'Rini Soemarno',
                'is_permanent' => false,
                'contract_start' => now()->subMonths(6)->format('Y-m-d'),
                'contract_end' => now()->addDays(5)->format('Y-m-d'),
                'resign_date' => null,
                'dept' => 'IT',
                'sect' => 'Support',
                'position' => 'IT Support',
                'location' => 'Kantor Pusat',
            ],

            // Finance Department
            [
                'nip' => 'EMP-0010',
                'name' => 'Agus Martowardojo',
                'email' => 'agus.martowardojo@company.com',
                'supervisor' => 'Budi Santoso',
                'is_permanent' => true,
                'contract_start' => '2018-05-01',
                'contract_end' => null,
                'resign_date' => null,
                'dept' => 'Finance',
                'sect' => 'Accounting',
                'position' => 'Finance Manager',
                'location' => 'Kantor Pusat',
            ],

            // Resigned Employee
            [
                'nip' => 'EMP-0011',
                'name' => 'Susilo Bambang Yudhoyono',
                'email' => 'susilo.bambang@company.com',
                'supervisor' => 'Budi Santoso',
                'is_permanent' => false,
                'contract_start' => '2023-01-01',
                'contract_end' => '2023-12-31',
                'resign_date' => '2023-08-15',
                'dept' => 'Marketing',
                'sect' => 'Digital',
                'position' => 'Marketing Staff',
                'location' => 'Kantor Pusat',
            ],

            // Contract Expiring Soon
            [
                'nip' => 'EMP-0012',
                'name' => 'Megawati Soekarnoputri',
                'email' => 'megawati.soekarno@company.com',
                'supervisor' => 'Budi Santoso',
                'is_permanent' => false,
                'contract_start' => now()->subMonths(6)->format('Y-m-d'),
                'contract_end' => now()->subDays(1)->format('Y-m-d'),
                'resign_date' => null,
                'dept' => 'Marketing',
                'sect' => 'Brand',
                'position' => 'Brand Manager',
                'location' => 'Kantor Pusat',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['nip' => $employee['nip']],
                $employee
            );
        }

        $this->command->info('Employee data seeded successfully!');
    }
}
