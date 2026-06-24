<x-filament-panels::page>
    {{-- Filter Form --}}
    <x-filament::section>
        <form wire:submit="search">
            {{ $this->form }}

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Tampilkan Data
                </x-filament::button>

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
        </form>
    </x-filament::section>

    {{-- Results Table --}}
    @if ($searched)
        {{-- Summary Badges --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-3xl font-bold text-primary-600 mt-1">{{ $records->count() }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktif</p>
                    <p class="text-3xl font-bold text-success-600 mt-1">{{ $records->where('status', 'active')->count() }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perpanjangan</p>
                    <p class="text-3xl font-bold text-warning-600 mt-1">{{ $records->where('status', 'renewal')->count() }}</p>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kadaluarsa</p>
                    <p class="text-3xl font-bold text-danger-600 mt-1">{{ $records->where('status', 'expired')->count() }}</p>
                </div>
            </x-filament::section>
        </div>

        {{-- Data Table --}}
        <x-filament::section heading="Hasil Laporan Perizinan">
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pemegang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Diterbitkan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kadaluarsa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PIC</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($records as $i => $permit)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $permit->type }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $permit->number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $permit->holder ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $permit->asset_location ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $permit->issued_at ? $permit->issued_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $permit->expires_at ? $permit->expires_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $permit->pic ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $color = match($permit->status) {
                                                'active'  => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                                'renewal' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                                'expired' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                                default   => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                            {{ match($permit->status) {
                                                'active' => 'AKTIF',
                                                'renewal' => 'PERPANJANGAN',
                                                'expired' => 'KADALUARSA',
                                                default => strtoupper($permit->status)
                                            } }}
                                        </span>
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
    @endif
</x-filament-panels::page>
