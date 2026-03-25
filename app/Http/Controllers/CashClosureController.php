<?php

namespace App\Http\Controllers;

use App\Models\MonthlyCashClosure;
use App\Models\Payment;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashClosureController extends Controller
{
    public function index()
    {
        $closures = MonthlyCashClosure::with('user')
            ->orderByDesc('year_month')
            ->get();

        $currentMonth = Carbon::now()->format('Y-m');

        return view('admin.cash-closure.index', compact('closures', 'currentMonth'));
    }

    public function close(Request $request, string $yearMonth)
    {
        if (MonthlyCashClosure::where('year_month', $yearMonth)->exists()) {
            return back()->with('error', 'Este mês já foi fechado.');
        }

        $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        DB::beginTransaction();
        try {
            $totalInflows  = Payment::whereBetween('payment_date', [$start, $end])->sum('amount');
            $totalOutflows = Withdrawal::whereBetween('withdrawal_date', [$start, $end])->sum('amount');
            $totalExpenses = 0; // ← implementar mais tarde

            $lastClosure = MonthlyCashClosure::orderByDesc('year_month')->first();
            $startingBalance = $lastClosure?->ending_balance ?? 0.00;

            $closure = MonthlyCashClosure::create([
                'year_month'       => $yearMonth,
                'closed_at'        => now(),
                'user_id'          => auth()->id(),
                'starting_balance' => $startingBalance,
                'total_inflows'    => $totalInflows,
                'total_outflows'   => $totalOutflows,
                'total_expenses'   => $totalExpenses,
                'notes'            => $request->notes ?? null,
            ]);

            DB::commit();

            // Log automático
            activity('caixa_fechamento')
                ->performedOn($closure)
                ->causedBy(auth()->user())
                ->withProperties([
                    'total_inflows'  => $totalInflows,
                    'total_outflows' => $totalOutflows,
                    'ending_balance' => $closure->ending_balance,
                ])
                ->log("Fechou o caixa do mês {$yearMonth}");

            return redirect()->route('cash-closure.index')
                ->with('success', "Caixa de {$yearMonth} fechado com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao fechar caixa: ' . $e->getMessage());
            return back()->with('error', 'Erro ao fechar o caixa. Tente novamente.');
        }
    }
}