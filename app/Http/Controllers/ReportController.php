<?php

namespace App\Http\Controllers;

use App\Models\Client; // Importar DomPDF
use App\Models\Service; // Importar Excel
use Barryvdh\DomPDF\Facade\Pdf; // Se for usar Excel
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
        // Validação básica
        $request->validate([
            'format' => 'required|in:pdf,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        
        // Construir query com filtros (inclui soft-deleted)
        $query = Client::query();

    // Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Período (created_at ou outra data relevante)
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Serviço – CORREÇÃO AQUI
    if ($request->filled('service')) {
        $query->whereHas('service', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->service . '%');
        });
    }

    $clients = $query->orderBy('name', 'asc')->get();

        if ($clients->isEmpty()) {
            return back()->with('error', 'Nenhum dado encontrado para os filtros selecionados.');
        }

        // Nome do arquivo dinâmico
        $filename = 'Relatorio_WawaBusiness_'.now()->format('d-m-Y_H-i');

        // Gerar Excel
        if ($request->format === 'excel') {
            return Excel::download(
                new \App\Exports\ClientsExport($request->status, $request->service),
                $filename.'.xlsx'
            );
        }

        // Gerar PDF (mantém o que já tinha)
        $pdf = Pdf::loadView('admin.reports.pdf.pdf_template', [
            'clients' => $clients,
            'filters' => $request->all(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename.'.pdf');
    }
}
