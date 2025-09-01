<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Permit;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->get('/permits/{permit}/download', function (Permit $permit) {
    abort_unless($permit->attachment_path && Storage::disk('private')->exists($permit->attachment_path), 404);
    return Storage::disk('private')->download($permit->attachment_path);
})->name('permits.download');
