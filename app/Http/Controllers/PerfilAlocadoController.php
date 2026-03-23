<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\PerfilAlocado;
use App\Models\Service; // Nota: usamos Payment (não Pagamento)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerfilAlocadoController extends Controller
{

public function index()
    {
        $perfisAlocados = PerfilAlocado::with(['cliente', 'pagamento', 'servico'])
            ->latest()
            ->paginate(15);

        return view('admin.perfis-alocados.list.index', compact('perfisAlocados'));
    }

    
public function create()
{
    $clientes = Client::orderBy('name')->get(['id', 'name']);
    $servicos = Service::orderBy('name')->get(['id', 'name']);
    $perfilAlocado = new \App\Models\PerfilAlocado(); // objeto vazio

    return view('admin.perfis-alocados.create.index', compact('clientes', 'servicos', 'perfilAlocado'));
}

public function edit(PerfilAlocado $perfilAlocado)
{
    // Clientes: só id e name, como pluck para select
    $clientes = Client::orderBy('name')->pluck('name', 'id');

    // Serviços: igual
    $servicos = Service::orderBy('name')->pluck('name', 'id');

    // Pagamentos: só colunas necessárias, ordenação por data de pagamento recente
    $pagamentos = Payment::where('client_id', $perfilAlocado->client_id)
        ->orderByDesc('new_due_date')
        ->get(['id','amount', 'new_due_date']);  // remove o resto

    return view('admin.perfis-alocados.edit.index', compact('perfilAlocado', 'clientes', 'servicos', 'pagamentos'));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_id' => 'required|exists:payments,id',
            'service_id' => 'required|exists:services,id',
            'tipo_alocacao' => 'required|in:perfil,pessoal',
            'nome_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'login_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'senha_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'email_conta' => 'required_if:tipo_alocacao,pessoal|nullable|email|max:255',
            'senha_conta' => 'required_if:tipo_alocacao,pessoal|nullable|string|max:255',
            'status' => 'sometimes|in:ativo,expirado,suspenso',
            'observacao' => 'nullable|string',
        ]);

        $pagamento = Payment::findOrFail($validated['payment_id']);
        if ($pagamento->client_id != $validated['client_id']) {
            return back()->withInput()->withErrors(['payment_id' => 'Pagamento não pertence ao cliente selecionado.']);
        }

        // Opcional: pré-preencher service_id a partir do pagamento/cliente se não veio no form
        if (empty($validated['service_id']) && $pagamento->client && $pagamento->client->service_id) {
            $validated['service_id'] = $pagamento->client->service_id;
        }

        try {
            $perfil = PerfilAlocado::create($validated);

            return redirect()->route('perfis-alocados.index')->with('success', 'Perfil criado! ID: '.$perfil->id);
        } catch (\Exception $e) {
            Log::error('Erro ao criar perfil: '.$e->getMessage());

            return back()->withInput()->with('error', 'Erro ao salvar.');
        }
    }

    public function update(Request $request, PerfilAlocado $perfilAlocado)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_id' => 'required|exists:payments,id',
            'service_id' => 'required|exists:services,id',
            'tipo_alocacao' => 'required|in:perfil,pessoal',
            'nome_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'login_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'senha_perfil' => 'required_if:tipo_alocacao,perfil|nullable|string|max:100',
            'email_conta' => 'required_if:tipo_alocacao,pessoal|nullable|email|max:255',
            'senha_conta' => 'required_if:tipo_alocacao,pessoal|nullable|string|max:255',
            'status' => 'sometimes|in:ativo,expirado,suspenso',
            'observacao' => 'nullable|string',
        ]);

        $pagamento = Payment::findOrFail($validated['payment_id']);
        if ($pagamento->client_id != $validated['client_id']) {
            return back()->withInput()->withErrors(['payment_id' => 'Pagamento não pertence ao cliente selecionado.']);
        }

        // Opcional: pré-preencher service_id a partir do pagamento/cliente se não veio no form
        if (empty($validated['service_id']) && $pagamento->client && $pagamento->client->service_id) {
            $validated['service_id'] = $pagamento->client->service_id;
        }

        try {
            $perfilAlocado->update($validated);

            return redirect()->route('perfis-alocados.index')->with('success', 'Perfil atualizado! ID: '.$perfilAlocado->id);
        } catch (\Exception $e) {
            Log::error('Erro ao criar perfil: '.$e->getMessage());

            return back()->withInput()->with('error', 'Erro ao salvar.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerfilAlocado $perfilAlocado)
    {
        try {
            $perfilAlocado->delete();

            return redirect()
                ->route('perfis-alocados.index')
                ->with('success', 'Perfil alocado removido com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir perfil alocado: '.$e->getMessage());

            return back()->with('error', 'Não foi possível excluir o perfil no momento.');
        }
    }
}
