<?php

namespace App\Http\Controllers;

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
        $query = Client::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->service) {
            $query->where('service', $request->service);
        }

        $clients = $query->get();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
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

        $validated['due_date'] = date('Y-m-d', strtotime($validated['start_date'] . ' + 30 days'));
        $validated['status'] = 'Ativo';

        Client::create($validated);

        // Enviar email de boas-vindas (ver pilar 4)
        // Mail::to('exemplo@email.com')->send(new WelcomeMail($client));

        return redirect()->route('clients.index')->with('success', 'Cliente cadastrado!');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
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
            $validated['due_date'] = date('Y-m-d', strtotime(now() . ' + 30 days'));
            $validated['status'] = 'Ativo';
        } else {
            $validated['due_date'] = date('Y-m-d', strtotime($validated['start_date'] . ' + 30 days'));
        }

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado!');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente removido!');
    }
}
