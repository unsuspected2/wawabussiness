<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        // Filtro por tipo de log (pagamentos, saques, serviços, clientes)
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filtro por evento (created, updated, deleted)
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filtro por usuário (quem fez a ação)
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        // Busca por descrição ou propriedades
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereJsonContains('properties->attributes', $search)
                  ->orWhereJsonContains('properties->old', $search);
            });
        }

        $logs = $query->paginate(20);

        // Para os filtros dropdown
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.logs.index', compact('logs', 'users'));
    }
}
