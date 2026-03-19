<?php

namespace App\Http\Controllers;

use App\Models\Client; // Importar DomPDF
use App\Models\Service; // Importar Excel
// Se for usar Excel
use Carbon\Carbon; // Classe que criaremos para Excel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Faturamento Mensal (últimos 6 meses)
        $monthlyRevenue = Client::select(
            DB::raw('SUM(value_paid) as total'),
            DB::raw("DATE_FORMAT(created_at, '%m/%Y') as month")
        )
            ->groupBy('month')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Top Serviços mais vendidos
        $topServices = Service::withCount('clients')  // conta quantos clients tem esse service_id
            ->orderBy('clients_count', 'desc')
            ->get(['name', 'clients_count']);

        // Clientes perdidos (Cancelados/Deletados no mês atual)
        $churnCount = Client::onlyTrashed()
            ->whereMonth('deleted_at', Carbon::now()->month)
            ->count();

        return view('admin.reports.index', compact('monthlyRevenue', 'topServices', 'churnCount'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Usa query sem global scopes problemáticos (com withTrashed se necessário)
        $query = Client::query()->with(['service']);

        // Status
        if ($request->filled('status') && $request->status !== '') {
            if ($request->status === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->status);
            }
        }
        // Quando vazio ("Todos") → traz tudo (ativos + vencidos + cancelados se quiseres)
        // Se quiseres excluir removidos por default:
        // else {
        //     $query->whereNull('deleted_at');
        // }

        // Período - due_date
        if ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        // Serviço
        if ($request->filled('service') && trim($request->service) !== '') {
            $serviceName = trim($request->service);
            $query->whereHas('service', function ($q) use ($serviceName) {
                $q->where('name', 'like', "%{$serviceName}%");
            });
        }

        $clients = $query->orderBy('name', 'asc')->get();

        if ($clients->isEmpty()) {
            return back()->with('error', 'Nenhum cliente encontrado para os filtros selecionados.');
        }

        $filename = 'Relatorio_WawaBusiness_'.now()->format('d-m-Y_H-i');

        if ($request->format === 'excel') {
            return Excel::download(
                new \App\Exports\ClientsExport($clients),
                $filename.'.xlsx'
            );
        }

        return view('admin.reports.pdf.pdf_template', [
            'clients' => $clients,
            'filters' => $request->all(),
        ]);
    }
}
