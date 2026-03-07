<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\Payment;
use App\Models\Withdrawal;





use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Estatísticas principais
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'Ativo')->count();
        $overdueClients = Client::where('status', 'Vencido')->count();
        $todayRenewals = Client::whereDate('due_date', Carbon::today())->count();

        // Valor total pago (soma de value_paid)
        $totalPaid = Client::sum('value_paid');

        // Próximos vencimentos (próximos 7 dias)
        $upcomingDue = Client::whereBetween('due_date', [
            Carbon::today(),
            Carbon::today()->addDays(7),
        ])
            ->orderBy('due_date')
            ->take(10)
            ->get();

        // Contagem por status para gráfico simples
        $statusCount = Client::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $recentDeletions = \Spatie\Activitylog\Models\Activity::where('event', 'deleted')
            ->where('subject_type', 'App\\Models\\Client')
    // O segredo está aqui: carregar o subject mesmo que esteja deletado
            ->with(['causer', 'subject' => function ($query) {
                $query->withTrashed();
            }])
            ->latest()
            ->take(8)
            ->get();

        $monthlyPaid = Client::whereMonth('created_at', Carbon::now()->month)->sum('value_paid');
        $monthlyWithdrawals = Withdrawal::whereMonth('withdrawal_date', Carbon::now()->month)->sum('amount');
        $monthlyBalance = $monthlyPaid - $monthlyWithdrawals;

        // Entradas do mês
    $monthlyPaid = Payment::whereMonth('payment_date', Carbon::now()->month)
                          ->sum('amount');

    // Saídas do mês (apenas saques NÃO repostos)
    $monthlyWithdrawals = Withdrawal::whereMonth('withdrawal_date', Carbon::now()->month)
                                    ->where('is_repaid', false)
                                    ->sum('amount');

    $monthlyBalance = $monthlyPaid - $monthlyWithdrawals;

    // Saques pendentes de reposição (total geral)
    $pendingRepay = Withdrawal::where('repay_status', 'Pendente')
                              ->where('is_repaid', false)
                              ->sum('amount');

        return view('admin.dashboard.index', compact(
            'totalClients',
            'activeClients',
            'overdueClients',
            'todayRenewals',
            'totalPaid',
            'upcomingDue',
            'statusCount',
            'recentDeletions',
            'monthlyPaid',
            'monthlyWithdrawals',
            'monthlyBalance',
            'pendingRepay'
        ));
    }
}
