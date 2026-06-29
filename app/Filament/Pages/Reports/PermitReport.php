<?php

namespace App\Filament\Pages\Reports;

use App\Exports\PermitsExport;
use App\Models\Permit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermitReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Perizinan';
    protected static ?string $title           = 'Laporan Perizinan';
    protected static ?int    $navigationSort  = 1;

    protected static string $view = 'filament.pages.reports.permit-report';

    // ── Form State ──────────────────────────────────────────
    public ?array $data = [];

    // Results
    public Collection $records;
    public bool $searched = false;

    public function mount(): void
    {
        $this->form->fill();
        $this->records = $this->buildQuery()->get();
        $this->searched = true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active'  => 'Aktif',
                                    'renewal' => 'Perpanjangan (≤60 hari)',
                                    'expired' => 'Kadaluarsa',
                                ])
                                ->placeholder('Semua Status')
                                ->native(false),

                            TextInput::make('type')
                                ->label('Jenis Izin')
                                ->placeholder('Cth: IMB, IPAL, dll...')
                                ->maxLength(100),

                            TextInput::make('holder')
                                ->label('Holder')
                                ->placeholder('Nama pemegang izin...')
                                ->maxLength(100),
                        ]),
                        Grid::make(3)->schema([
                            DatePicker::make('expires_from')
                                ->label('Tanggal Kadaluarsa (Dari)')
                                ->native(false),

                            DatePicker::make('expires_until')
                                ->label('Tanggal Kadaluarsa (Sampai)')
                                ->native(false),
                                
                            Select::make('rule_days')
                                ->label('Aturan Pengingat (H- Hari)')
                                ->options(function () {
                                    return \App\Models\ReminderRule::where('entity', 'permit')
                                        ->where('active', true)
                                        ->pluck('days_before', 'days_before')
                                        ->mapWithKeys(fn($day) => [$day => "H-{$day} Hari"])
                                        ->toArray();
                                })
                                ->placeholder('Pilih Aturan')
                                ->native(false),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    // ── Actions ─────────────────────────────────────────────

    public function search(): void
    {
        $this->records  = $this->buildQuery()->get();
        $this->searched = true;

        Notification::make()
            ->title("Ditemukan {$this->records->count()} data permit")
            ->success()
            ->send();
    }

    public function exportExcel()
    {
        $records = $this->buildQuery()->get();

        if ($records->isEmpty()) {
            Notification::make()
                ->title('Tidak ada data untuk diexport')
                ->warning()
                ->send();
        }

        $filename = 'Laporan_Permit_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PermitsExport($records), $filename);
    }

    public function exportPdf()
    {
        $params = array_filter($this->data);
        $url    = route('reports.permits.pdf', $params);

        return redirect($url);
    }

    // ── Query Builder ────────────────────────────────────────

    protected function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $data  = $this->data;
        $query = Permit::query();

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (!empty($data['type'])) {
            $query->where('type', 'like', '%' . $data['type'] . '%');
        }

        if (!empty($data['holder'])) {
            $query->where('holder', 'like', '%' . $data['holder'] . '%');
        }

        if (!empty($data['expires_from'])) {
            $query->whereDate('expires_at', '>=', $data['expires_from']);
        }

        if (!empty($data['expires_until'])) {
            $query->whereDate('expires_at', '<=', $data['expires_until']);
        }

        if (!empty($data['rule_days'])) {
            $days = (int) $data['rule_days'];
            $query->whereDate('expires_at', '>=', now())
                  ->whereDate('expires_at', '<=', now()->addDays($days));
        }

        return $query->orderBy('expires_at', 'asc');
    }
}
