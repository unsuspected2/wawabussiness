<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
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
        $query = Payment::with(['client', 'user'])->latest();

        // Filtro por cliente
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filtro por período
        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        $payments = $query->paginate(15);

        $clients = Client::orderBy('name')->get(); // Para filtro dropdown

        return view('admin.payments.list.index', compact('payments', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.payments.create.index', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'new_due_date'    => 'required|date|after:payment_date',
            'method'          => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        Payment::create($validated);

        // Atualiza o cliente automaticamente
        $client = Client::find($validated['client_id']);
        $client->update([
            'due_date' => $validated['new_due_date'],
            'status'   => 'Ativo',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pagamento registrado e assinatura renovada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['client', 'user']);
        return view('admin.payments.show.index', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.payments.edit.index', compact('payment', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'new_due_date'    => 'required|date|after:payment_date',
            'method'          => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $payment->update($validated);

        // Atualiza o cliente (caso tenha mudado a data de vencimento)
        $client = Client::find($validated['client_id']);
        $client->update([
            'due_date' => $validated['new_due_date'],
            'status'   => 'Ativo',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pagamento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Registro de pagamento removido!');
    }
}
