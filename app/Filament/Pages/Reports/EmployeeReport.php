<?php

namespace App\Filament\Pages\Reports;

use App\Exports\EmployeesExport;
use App\Models\Employee;
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

class EmployeeReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Karyawan';
    protected static ?string $title           = 'Laporan Karyawan';
    protected static ?int    $navigationSort  = 3;

    protected static string $view = 'filament.pages.reports.employee-report';

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
                            Select::make('is_permanent')
                                ->label('Status Karyawan')
                                ->options([
                                    '1' => 'Tetap',
                                    '0' => 'Kontrak',
                                ])
                                ->placeholder('Semua Status')
                                ->native(false),

                            TextInput::make('name')
                                ->label('Nama')
                                ->placeholder('Nama Karyawan...')
                                ->maxLength(100),

                            TextInput::make('nip')
                                ->label('NIP')
                                ->placeholder('NIP Karyawan...')
                                ->maxLength(50),
                        ]),
                        Grid::make(3)->schema([
                            DatePicker::make('contract_end_from')
                                ->label('Kontrak Habis (Dari)')
                                ->native(false),

                            DatePicker::make('contract_end_until')
                                ->label('Kontrak Habis (Sampai)')
                                ->native(false),
                                
                            Select::make('rule_days')
                                ->label('Aturan Pengingat (H- Hari)')
                                ->options(function () {
                                    return \App\Models\ReminderRule::where('entity', 'contract')
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
            ->title("Ditemukan {$this->records->count()} data karyawan")
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

        $filename = 'Laporan_Karyawan_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new EmployeesExport($records), $filename);
    }

    public function exportPdf()
    {
        $params = array_filter($this->data);
        $url    = route('reports.employees.pdf', $params);

        return redirect($url);
    }

    // ── Query Builder ────────────────────────────────────────

    protected function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $data  = $this->data;
        $query = Employee::query();

        if (isset($data['is_permanent']) && $data['is_permanent'] !== '') {
            $query->where('is_permanent', $data['is_permanent'] == '1');
        }

        if (!empty($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (!empty($data['nip'])) {
            $query->where('nip', 'like', '%' . $data['nip'] . '%');
        }

        if (!empty($data['contract_end_from'])) {
            $query->whereDate('contract_end', '>=', $data['contract_end_from']);
        }

        if (!empty($data['contract_end_until'])) {
            $query->whereDate('contract_end', '<=', $data['contract_end_until']);
        }

        if (!empty($data['rule_days'])) {
            $days = (int) $data['rule_days'];
            $query->whereDate('contract_end', '>=', now())
                  ->whereDate('contract_end', '<=', now()->addDays($days));
        }

        return $query->orderBy('name', 'asc');
    }
}
