<?php

namespace App\Filament\Pages\Reports;

use App\Exports\ReminderLogsExport;
use App\Models\ReminderLog;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReminderLogReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Log Pengingat';
    protected static ?string $title           = 'Laporan Log Pengingat';
    protected static ?int    $navigationSort  = 2;

    protected static string $view = 'filament.pages.reports.reminder-log-report';

    // ── Form State ──────────────────────────────────────────
    public ?array $data = [];

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
                            Select::make('entity')
                                ->label('Entitas')
                                ->options([
                                    'permit'   => 'Perizinan',
                                    'contract' => 'Kontrak Karyawan',
                                ])
                                ->placeholder('Semua Entitas')
                                ->native(false),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'sent'    => 'Terkirim',
                                    'failed'  => 'Gagal',
                                    'skipped' => 'Dilewati',
                                ])
                                ->placeholder('Semua Status')
                                ->native(false),

                            Select::make('rule_days')
                                ->label('Aturan Pengingat (H- Hari)')
                                ->options([
                                    15 => '15 hari',
                                    30 => '30 hari',
                                    60 => '60 hari',
                                    90 => '90 hari',
                                ])
                                ->placeholder('Semua Aturan')
                                ->native(false),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('created_from')
                                ->label('Tanggal Kirim (Dari)')
                                ->native(false),

                            DatePicker::make('created_until')
                                ->label('Tanggal Kirim (Sampai)')
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
            ->title("Ditemukan {$this->records->count()} reminder log")
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

        $filename = 'Laporan_ReminderLog_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ReminderLogsExport($records), $filename);
    }

    public function exportPdf()
    {
        $params = array_filter($this->data);
        $url    = route('reports.reminder-logs.pdf', $params);

        return redirect($url);
    }

    // ── Query Builder ────────────────────────────────────────

    protected function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $data  = $this->data;
        $query = ReminderLog::query();

        if (!empty($data['entity'])) {
            $query->where('entity', $data['entity']);
        }

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (!empty($data['rule_days'])) {
            $query->where('rule_days', $data['rule_days']);
        }

        if (!empty($data['created_from'])) {
            $query->whereDate('created_at', '>=', $data['created_from']);
        }

        if (!empty($data['created_until'])) {
            $query->whereDate('created_at', '<=', $data['created_until']);
        }

        return $query->orderBy('created_at', 'desc');
    }
}
