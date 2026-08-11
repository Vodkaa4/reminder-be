<x-filament-panels::page>
    {{-- Filter Form --}}
    <x-filament::section>
        <div>
            {{ $this->form }}

            <div class="mt-4 flex flex-wrap gap-3">

                <x-filament::button
                    wire:click="exportExcel"
                    color="success"
                    icon="heroicon-o-table-cells"
                    :disabled="!$searched || $records->isEmpty()"
                >
                    Export Excel
                </x-filament::button>

                <x-filament::button
                    wire:click="exportPdf"
                    color="danger"
                    icon="heroicon-o-document-arrow-down"
                    :disabled="!$searched || $records->isEmpty()"
                >
                    Export PDF
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    {{-- Results Table --}}
        {{-- Summary Badges --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Karyawan</p>
                    <p class="text-3xl font-bold text-primary-600 mt-1">{{ $records->count() }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tetap</p>
                    <p class="text-3xl font-bold text-success-600 mt-1">{{ $records->where('is_permanent', true)->count() }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kontrak</p>
                    <p class="text-3xl font-bold text-warning-600 mt-1">{{ $records->where('is_permanent', false)->count() }}</p>
                </div>
            </x-filament::section>
        </div>

        {{-- Data Table --}}
        <x-filament::section heading="Hasil Laporan Karyawan">
            @if ($records->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <x-heroicon-o-document-magnifying-glass class="mx-auto mb-3 opacity-40 text-gray-400" style="width: 48px; height: 48px;" />
                    <p class="font-medium">Tidak ada data ditemukan</p>
                    <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dept/Sect</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posisi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mulai Kontrak</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akhir Kontrak</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($records as $i => $employee)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-mono text-xs">{{ $employee->nip ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $employee->dept ?? '-' }} <br>
                                        <span class="text-xs text-gray-400">{{ $employee->sect ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $employee->position ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $employee->contract_start ? $employee->contract_start->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $employee->contract_end ? $employee->contract_end->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($employee->is_permanent)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300">
                                                TETAP
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300">
                                                KONTRAK
                                            </span>
                                        @endif
                                        @if ($employee->resign_date)
                                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300">
                                                RESIGN
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-gray-400 text-right">
                    Menampilkan {{ $records->count() }} data
                </p>
            @endif
        </x-filament::section>
</x-filament-panels::page>
