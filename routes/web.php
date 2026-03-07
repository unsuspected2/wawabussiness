<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;      // Novo: CRUD de Serviços
use App\Http\Controllers\PaymentController;      // Novo: Histórico de Renovações/Pagamentos
use App\Http\Controllers\WithdrawalController;   // Saques
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LogController;
use Illuminate\Http\Request;

// Rota pública inicial
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação (login, register, logout, etc.)
Auth::routes();

// Redireciona /home para o dashboard (padrão do Laravel Auth)
Route::get('/home', [DashboardController::class, 'index'])->middleware('auth')->name('home');

// Todas as rotas administrativas protegidas por autenticação
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes (CRUD + Export + Busca JSON para autocomplete)
    Route::resource('clients', ClientController::class);

    // Exportação de clientes para Excel (com filtros)
    Route::get('/clients/export-excel', function (Request $request) {
        $status = $request->query('status');
        $service = $request->query('service');

        return (new \App\Exports\ClientsExport($status, $service))
            ->download('clientes_wawabusiness_' . now()->format('d-m-Y_H-i') . '.xlsx');
    })->name('clients.export');

    // Busca JSON para autocomplete (usado no cadastro/renovação)
    Route::get('/clients/search-json', function (Request $request) {
        $term = $request->query('term', '');

        $clients = \App\Models\Client::where('name', 'like', "%$term%")
            ->orWhere('whatsapp', 'like', "%$term%")
            ->select('id', 'name', 'whatsapp')
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'text' => $c->name . ' - ' . $c->whatsapp
            ]);

        return response()->json($clients);
    })->name('clients.search.json');

    // Serviços (CRUD)
    Route::resource('services', ServiceController::class)->names([
        'index' => 'services.index',
        'create' => 'services.create',
        'store' => 'services.store',
        'show' => 'services.show',
        'edit' => 'services.edit',
        'update' => 'services.update',
        'destroy' => 'services.destroy',
    ]);

    // Pagamentos / Renovações (histórico)
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show', 'destroy'])->names([
        'index' => 'payments.index',
        'show' => 'payments.show',
        'edit' => 'payments.edit',
        'create' => 'payments.create',
        'store' => 'payments.store',
    ]);

    // Saques / Caixa
    Route::resource('withdrawals', WithdrawalController::class)->only(['index','show','edit', 'create', 'store', 'update', 'destroy'], )->names([
        'index' => 'withdrawals.index',
        'show' => 'withdrawals.show',
        'edit' => 'withdrawals.edit',
        'create' => 'withdrawals.create',
        'store' => 'withdrawals.store',
        'update' => 'withdrawals.update',
        'destroy' => 'withdrawals.destroy',
    ]);

    // Relatórios
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Logs de Atividades
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});
