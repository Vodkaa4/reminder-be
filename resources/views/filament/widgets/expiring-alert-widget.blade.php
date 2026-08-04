<x-filament-widgets::widget>
    @if($expiringPermitsCount > 0 || $expiringContractsCount > 0)
        <x-filament::section class="border-danger-600 ring-danger-600 bg-danger-50 dark:bg-danger-900/20">
            <div class="flex items-center gap-4">
                <div class="text-danger-600">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-danger-600">Peringatan Dokumen Segera Habis Masa Berlaku!</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                        Sistem mendeteksi ada dokumen yang membutuhkan perhatian Anda:
                    </p>
                    <ul class="list-disc pl-5 mt-2 text-sm text-gray-700 dark:text-gray-300">
                        @if($expiringPermitsCount > 0)
                            <li>Terdapat <strong>{{ $expiringPermitsCount }} Izin Operasional</strong> yang berada dalam masa perpanjangan (<= 60 hari).</li>
                        @endif
                        @if($expiringContractsCount > 0)
                            <li>Terdapat <strong>{{ $expiringContractsCount }} Kontrak Karyawan</strong> yang akan segera habis (<= 30 hari).</li>
                        @endif
                    </ul>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
