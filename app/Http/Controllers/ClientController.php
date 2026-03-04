<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Client;
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
                    ->orWhere('service', 'like', "%{$search}%");
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'service' => 'required|string|max:50',
            'plan' => 'required|string|max:50',
            'value_paid' => 'required|numeric',
            'start_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $validated['due_date'] = date('Y-m-d', strtotime($validated['start_date'].' + 30 days'));
        $validated['status'] = 'Ativo';

        Client::create($validated);

        // Enviar email de boas-vindas (ver pilar 4)
        // Mail::to('exemplo@email.com')->send(new WelcomeMail($client));

        return redirect()->route('clients.index')->with('success', 'Cliente cadastrado!');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit.index', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'service' => 'required|string|max:50',
            'plan' => 'required|string|max:50',
            'value_paid' => 'required|numeric',
            'start_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        if ($request->has('renew')) {
            $validated['due_date'] = date('Y-m-d', strtotime(now().' + 30 days'));
            $validated['status'] = 'Ativo';
        } else {
            $validated['due_date'] = date('Y-m-d', strtotime($validated['start_date'].' + 30 days'));
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
