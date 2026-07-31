<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil do proprio usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Analises e aquisicoes
    Route::middleware('permission:analyses.view')->group(function () {
        Route::get('analyses', [AnalysisController::class, 'index'])->name('analyses.index');
        Route::get('analyses/{analysis}', [AnalysisController::class, 'show'])->name('analyses.show');
        Route::get('analyses/{analysis}/pdf', [AnalysisController::class, 'pdf'])->name('analyses.pdf');
    });
    Route::middleware('permission:analyses.manage')->group(function () {
        Route::post('analyses/calculate', [AnalysisController::class, 'calculate'])->name('analyses.calculate');
        Route::get('analyses-create/new', [AnalysisController::class, 'create'])->name('analyses.create');
        Route::post('analyses', [AnalysisController::class, 'store'])->name('analyses.store');
        Route::get('analyses/{analysis}/edit', [AnalysisController::class, 'edit'])->name('analyses.edit');
        Route::put('analyses/{analysis}', [AnalysisController::class, 'update'])->name('analyses.update');
        Route::delete('analyses/{analysis}', [AnalysisController::class, 'destroy'])->name('analyses.destroy');
        Route::post('analyses/{analysis}/notes', [AnalysisController::class, 'addNote'])->name('analyses.notes.store');
        Route::post('analyses/{analysis}/attachments', [AnalysisController::class, 'uploadAttachment'])->name('analyses.attachments.store');
        Route::delete('analyses/{analysis}/attachments/{attachment}', [AnalysisController::class, 'deleteAttachment'])->name('analyses.attachments.destroy');
        Route::post('analyses/{analysis}/convert', [AnalysisController::class, 'convertToFleet'])->name('analyses.convert');
    });

    // Clientes
    Route::middleware('permission:clients.view')->group(function () {
        Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    });
    Route::middleware('permission:clients.manage')->group(function () {
        Route::get('clients-create/new', [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Bancos / financeiras
    Route::middleware('permission:banks.view')->group(function () {
        Route::get('banks', [BankController::class, 'index'])->name('banks.index');
    });
    Route::middleware('permission:banks.manage')->group(function () {
        Route::get('banks-create/new', [BankController::class, 'create'])->name('banks.create');
        Route::post('banks', [BankController::class, 'store'])->name('banks.store');
        Route::get('banks/{bank}/edit', [BankController::class, 'edit'])->name('banks.edit');
        Route::put('banks/{bank}', [BankController::class, 'update'])->name('banks.update');
        Route::delete('banks/{bank}', [BankController::class, 'destroy'])->name('banks.destroy');
    });

    // Usuarios (somente admin)
    Route::middleware('permission:users.manage')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Log de auditoria
    Route::middleware('permission:audit.view')->group(function () {
        Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
    });
});

require __DIR__.'/auth.php';
