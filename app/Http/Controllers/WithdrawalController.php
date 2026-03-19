<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Client;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('user')->latest();

        // Filtro por status de reposição
        if ($request->filled('repay_status')) {
            $query->where('repay_status', $request->repay_status);
        }

        // Filtro por período
        if ($request->filled('start_date')) {
            $query->whereDate('withdrawal_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('withdrawal_date', '<=', $request->end_date);
        }

        $withdrawals = $query->paginate(15);

        return view('admin.withdrawals.list.index', compact('withdrawals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.withdrawals.create.index');
    }

    public function edit(withdrawal $withdrawal)
    {
        return view('admin.withdrawals.edit.index', compact('withdrawal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'purpose' => 'required|string|max:500',
            'repay_date' => 'nullable|date|after_or_equal:today',
            'repay_status' => 'required|in:Pendente,Reposto,Não vai repor',
        ]);

        $withdrawal = Withdrawal::create([
            'amount' => $validated['amount'],
            'withdrawal_date' => now(),
            'reason' => $validated['reason'],
            'purpose' => $validated['purpose'],
            'repay_date' => $validated['repay_date'],
            'repay_status' => $validated['repay_status'],
            'user_id' => auth()->id(),
            'is_repaid' => ($validated['repay_status'] === 'Reposto'), // Se já marcou como reposto, considera devolvido
        ]);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Saque registrado com sucesso!');
    }
       public function show(Withdrawal $withdrawal)
    {
        return view('admin.withdrawals.show.index', compact('withdrawal'));
    }


    public function update(Request $request, Withdrawal $withdrawal)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'purpose' => 'required|string|max:500',
            'repay_date' => 'nullable|date|after_or_equal:today',
            'repay_status' => 'required|in:Pendente,Reposto,Não vai repor',
        ]);

        // Se mudou para "Reposto" agora, marca como devolvido
        if ($validated['repay_status'] === 'Reposto' && ! $withdrawal->is_repaid) {
            $validated['is_repaid'] = true;
        }

        $withdrawal->update($validated);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Saque atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdrawal $withdrawal)
    {
        $withdrawal->delete();

        return redirect()->route('withdrawals.index')
            ->with('success', 'Saque removido do histórico!');
    }
}
