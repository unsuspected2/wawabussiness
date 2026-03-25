<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $monthlyRevenue = Client::select(
            DB::raw('SUM(value_paid) as total'),
            DB::raw("DATE_FORMAT(created_at, '%m/%Y') as month")
        )
            ->groupBy('month')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $topServices = Service::withCount('clients')
            ->orderBy('clients_count', 'desc')
            ->get(['name', 'clients_count']);

        $churnCount = Client::onlyTrashed()
            ->whereMonth('deleted_at', Carbon::now()->month)
            ->count();

        return view('admin.reports.index', compact('monthlyRevenue', 'topServices', 'churnCount'));
    }

    public function export(Request $request)
{
    $request->validate([
        'format'     => 'required|in:pdf,excel',
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    $query = Client::query()->with(['service']);

    // Status
    if ($request->filled('status') && $request->status !== '') {
        if ($request->status === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->where('status', $request->status);
        }
    }

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
        $query->whereHas('service', fn($q) => $q->where('name', 'like', "%{$serviceName}%"));
    }

    $clients = $query->orderBy('name', 'asc')->get();

    if ($clients->isEmpty()) {
        return back()->with('error', 'Nenhum cliente encontrado para os filtros selecionados.');
    }

    $filename = 'Relatorio_WawaBusiness_' . now()->format('d-m-Y_H-i');

    if ($request->format === 'excel') {
        return Excel::download(
            new \App\Exports\ClientsExport($clients),
            $filename . '.xlsx'
        );
    }

    // Volta a abrir como página web normal (HTML/Blade)
    return view('admin.reports.pdf.pdf_template', [
        'clients' => $clients,
        'filters' => $request->all(),
    ]);
}
}