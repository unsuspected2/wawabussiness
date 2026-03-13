<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Requer login
    }

    public function index(Request $request)
    {
        // PASSO 1: Atualização Automática de Status
        // Se a data passou de hoje e ainda está 'Ativo', vira 'Vencido'
        Client::where('status', 'Ativo')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'Vencido']);

        // PASSO 2: Iniciar Query (Verificar se quer removidos ou normais)
        if ($request->has('trashed')) {
            $query = Client::onlyTrashed();
        } else {
            $query = Client::query();
        }

        // PASSO 3: Filtro por Status (Ativo / Vencido)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // PASSO 4: Filtro de Busca (Nome ou Serviço)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('service', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $clients = $query->orderBy('due_date', 'asc')->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create.index');
    }

    public function store(Request $request)
    {
/*         dd($request->all());
 */
        $validated = $request->validate([
            'existing_client_id' => 'nullable|exists:clients,id',
            'name' => 'required_if:existing_client_id,null|string|max:255',
            'whatsapp' => 'required_if:existing_client_id,null|string|max:20',
            'service_id' => 'required|exists:services,id',
            'plan' => 'required|string|max:50',
            'value_paid' => 'required|numeric|min:0.01',
            'start_date' => 'nullable|date',
            'observations' => 'nullable|string',
        ]);

        if ($request->filled('existing_client_id')) {
            // Renovação
            $client = Client::findOrFail($request->existing_client_id);

            Payment::create([
                'client_id' => $client->id,
                'amount' => $validated['value_paid'],
                'payment_date' => now(),
                'new_due_date' => now()->addDays(30),
                'user_id' => auth()->id(),
                'notes' => $validated['observations'] ?? 'Renovação automática',
            ]);

            $client->update([
                'due_date' => now()->addDays(30),
                'status' => 'Ativo',
            ]);

            return redirect()->route('clients.index')
                ->with('success', 'Assinatura renovada com sucesso!');
        }

        // Novo cliente
        $client = Client::create([
            'name' => $validated['name'],
            'whatsapp' => $validated['whatsapp'],
            'service_id' => $validated['service_id'],
            'plan' => $validated['plan'],
            'value_paid' => $validated['value_paid'],
            'start_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'Ativo',
            'observations' => $validated['observations'],
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Client $client)
    {
        $services = Service::all();

        return view('admin.clients.edit.index', compact('client', 'services'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'plan' => 'required|string|max:50',
            'value_paid' => 'required|numeric',
            'start_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        if ($request->has('renew')) {
            // Renovação: cria registro de pagamento e atualiza vencimento
            Payment::create([
                'client_id' => $client->id,
                'amount' => $validated['value_paid'],
                'payment_date' => now(),
                'new_due_date' => Carbon::today()->addDays(30),
                'method' => $request->method ?? 'Multicaixa',
                'notes' => $request->notes ?? 'Renovação manual',
                'user_id' => auth()->id(),
            ]);

            $validated['due_date'] = Carbon::today()->addDays(30);
            $validated['status'] = 'Ativo';
        } else {
            $validated['due_date'] = Carbon::parse($validated['start_date'])->addDays(30);
        }

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado!');
    }

    public function destroy(Client $client)
    {
        // Validação do motivo (obrigatório)
        $request = request(); // pega a request atual
        $request->validate([
            'deleted_reason' => 'required|string|max:500',
        ]);

        $client->deleted_reason = $request->deleted_reason;
        $client->saveQuietly();

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente removido com sucesso. Motivo: '.$request->deleted_reason);
    }
}
