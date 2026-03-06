<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
Route::get('/home', fn () => redirect()->route('dashboard'))->name('home'); // Redireciona /home para
Route::get('/', fn () => redirect()->route('login'))->name('home'); // Redireciona /home para


});
Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');


    Route::resource('clients', ClientController::class);
    // Logs de Atividades
    Route::get('/logs', [App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    Route::get('/clients/export', function (Request $request) {
        $status = $request->query('status');
        $service = $request->query('service');

        return (new \App\Exports\ClientsExport($status, $service))
            ->download('clientes_wawabusiness.xlsx');
    })->name('clients.export');

    // Relatórios
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});
