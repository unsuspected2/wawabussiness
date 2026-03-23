<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PerfilAlocadoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Rota pública inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação (Login, Register, etc.)
Auth::routes();

// Redireciona /home para o dashboard
Route::get('/home', [DashboardController::class, 'index'])->middleware('auth')->name('home');

// Todas as rotas administrativas protegidas
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes (Exportação e Busca antes do Resource para evitar conflitos)
    Route::get('/clients/export-excel', [ClientController::class, 'export'])->name('clients.export');
    Route::get('/clients/search-json', [ClientController::class, 'searchJson'])->name('clients.search.json');
    Route::resource('clients', ClientController::class);

    // Serviços
    Route::resource('services', ServiceController::class);

    // Pagamentos / Renovações
    Route::resource('payments', PaymentController::class)->except(['destroy']);

    // Saques / Caixa
    Route::resource('withdrawals', WithdrawalController::class);

    // Relatórios
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Logs de Atividades
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    // Rota para atualizar tarefa (exemplo de endpoint específico)
    Route::patch('/clients/task', [ClientController::class, 'updateTask'])->name('clients.update.task');

    // Rotas para Perfis Alocados (Web)
    Route::resource('perfis-alocados', PerfilAlocadoController::class)->parameters([
        'perfis-alocados' => 'perfilAlocado',  // força o parâmetro a ser {perfilAlocado}
    ]);    // Rotas para Perfis Alocados (API)
    Route::get('/clientes/{clienteId}/perfis', [PerfilAlocadoController::class, 'index']);
    Route::post('/perfis', [PerfilAlocadoController::class, 'store']);

    // Rota AJAX para buscar pagamentos por cliente (usada no form de perfis alocados)
    // routes/web.php
    Route::get('/pagamentos/by-client', function (Illuminate\Http\Request $request) {
        $clientId = $request->query('client_id');

        if (! $clientId) {
            return response()->json([]);
        }

        try {
            $pagamentos = App\Models\Payment::where('client_id', $clientId)
                ->orderByDesc('payment_date')
                ->get(['id', 'amount', 'new_due_date']);

            return response()->json($pagamentos->map(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => $p->amount ?? 0,
                    'new_due_date' => $p->new_due_date ? $p->new_due_date->format('d/m/Y') : null,
                    // Sem status → removido completamente
                ];
            }));
        } catch (\Exception $e) {
            \Log::error('Erro na rota pagamentos/by-client: '.$e->getMessage());

            return response()->json(['error' => 'Erro interno ao carregar pagamentos'], 500);
        }
    })->name('pagamentos.by-client');
});
Route::get('/super-limpar-cache', function (Request $request) {
    // Proteção simples por senha (mude 'abc123super' para algo teu forte)

    // Limpeza completa que resolve o problema do provider cached
    Artisan::call('optimize:clear');     // Principal: limpa compiled services, providers, cache tudo
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');

    // Apaga manualmente os arquivos de cache para garantir
    $cachePath = storage_path('framework/cache/data');
    $bootstrapCache = base_path('bootstrap/cache');

    // Se quiser, delete arquivos (opcional, mas força)
    // array_map('unlink', glob("$bootstrapCache/*.php")); // Cuidado: só se souber

    return '<h1>Caches limpos com sucesso!</h1><p>Agora acesse o site principal com Ctrl + F5. Se ainda erro, delete bootstrap/cache/ via File Manager.</p>';
});
