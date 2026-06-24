<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Permit;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::middleware(['auth'])->get('/permits/{permit}/download', function (Permit $permit) {
    abort_unless($permit->attachment_path && Storage::disk('private')->exists($permit->attachment_path), 404);
    return Storage::disk('private')->download($permit->attachment_path);
})->name('permits.download');

// ── Report PDF Downloads ─────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/permits/pdf',       [ReportController::class, 'permitPdf'])     ->name('permits.pdf');
    Route::get('/reminder-logs/pdf', [ReportController::class, 'reminderLogPdf'])->name('reminder-logs.pdf');
    Route::get('/employees/pdf',     [ReportController::class, 'employeePdf'])   ->name('employees.pdf');
});
