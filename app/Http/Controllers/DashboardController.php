<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Models\MonthlyCashClosure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Estatísticas de Clientes
        $totalClients     = Client::count();
        $activeClients    = Client::where('status', 'Ativo')->count();
        $overdueClients   = Client::where('status', 'Vencido')->count();
        $todayRenewals    = Client::whereDate('due_date', Carbon::today())->count();

        // Próximos vencimentos (próximos 7 dias)
        $upcomingDue = Client::whereBetween('due_date', [
            Carbon::today(),
            Carbon::today()->addDays(7),
        ])->orderBy('due_date')->take(10)->get();

        // Próximos expirações para alertas
        $upcomingExpirations = Client::where('status', '!=', 'Cancelado')
            ->whereDate('due_date', '>=', Carbon::today())
            ->whereDate('due_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('due_date')
            ->take(15)
            ->get();

        // Contagem por status
        $statusCount = Client::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Exclusões recentes
        $recentDeletions = \Spatie\Activitylog\Models\Activity::where('event', 'deleted')
            ->where('subject_type', 'App\\Models\\Client')
            ->with(['causer', 'subject' => fn($q) => $q->withTrashed()])
            ->latest()
            ->take(8)
            ->get();

        // ==================== CÁLCULO FINANCEIRO CORRETO ====================

        $currentMonth = Carbon::now()->format('Y-m');
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // 1. Entradas reais do mês (usando tabela payments)
        $monthlyInflows = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 2. Saídas reais do mês (saques não repostos)
        $monthlyOutflows = Withdrawal::whereBetween('withdrawal_date', [$startOfMonth, $endOfMonth])
            ->where('is_repaid', false)
            ->sum('amount');

        // 3. Último fechamento (para pegar o saldo inicial)
        $lastClosure = MonthlyCashClosure::orderByDesc('year_month')->first();
        $startingBalance = $lastClosure?->ending_balance ?? 0.00;

        // 4. Saldo real do mês atual
        $monthlyBalance = $startingBalance + $monthlyInflows - $monthlyOutflows;

        // 5. Saques pendentes de reposição (total geral, não só do mês)
        $pendingRepay = Withdrawal::where('repay_status', 'Pendente')
            ->where('is_repaid', false)
            ->sum('amount');

        // Valor total histórico (mantido como referência)
        $totalPaid = Payment::sum('amount');

        return view('admin.dashboard.index', compact(
            'totalClients',
            'activeClients',
            'overdueClients',
            'todayRenewals',
            'upcomingDue',
            'upcomingExpirations',
            'statusCount',
            'recentDeletions',
            'monthlyInflows',
            'monthlyOutflows',
            'monthlyBalance',
            'pendingRepay',
            'totalPaid',
            'lastClosure',
            'startingBalance'
        ));
    }
}