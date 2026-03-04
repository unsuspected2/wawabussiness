@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="text-success fw-bold">Dashboard WawaBusiness</h1>
            <a href="{{ route('clients.create') }}" class="btn btn-success btn-lg px-5">
                <i class="fas fa-plus me-2"></i> Novo Cliente
            </a>
        </div>

        <!-- Cards de Resumo -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body">
                        <h6 class="text-muted small">Total Clientes</h6>
                        <h2 class="fw-bold mb-0">{{ $totalClients }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body">
                        <h6 class="text-muted small">Ativos</h6>
                        <h2 class="fw-bold text-success mb-0">{{ $activeClients }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body">
                        <h6 class="text-muted small">Vencidos</h6>
                        <h2 class="fw-bold text-danger mb-0">{{ $overdueClients }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body">
                        <h6 class="text-muted small">Total Pago</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($totalPaid, 2, ',', '.') }} Kz</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Gráfico de Pizza - Distribuição de Status -->
            <div class="col-lg-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Distribuição de Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusPieChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Barras - Próximos Vencimentos -->
            <div class="col-lg-6">
                <div class="card bg-dark text-white shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Vencimentos nos Próximos 7 Dias</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dueBarChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exclusões Recentes (com quem eliminou + motivo) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-dark text-white shadow">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Exclusões Recentes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Motivo da Eliminação</th>
                                        <th>Eliminado por</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentDeletions as $log)
                                        <tr>
                                            <!-- Nome do cliente vindo da tabela clients -->
                                            <td>{{ $log->subject->name ?? 'Cliente permanentemente removido' }}</td>

                                            <!-- Motivo vindo da coluna deleted_reason da tabela clients -->
                                            <td class="text-warning">
                                                {{ $log->subject->deleted_reason ?? 'Motivo não informado' }}
                                            </td>

                                            <!-- Usuário que deletou vindo do log -->
                                            <td>
                                                <strong>{{ $log->causer->name ?? 'Sistema' }}</strong>
                                            </td>

                                            <!-- Data vinda do log -->
                                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Nenhuma exclusão recente.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Gráfico de Pizza - Status
        new Chart(document.getElementById('statusPieChart'), {
            type: 'pie',
            data: {
                labels: ['Ativos', 'Vencidos', 'Cancelados'],
                datasets: [{
                    data: [
                        {{ $statusCount['Ativo'] ?? 0 }},
                        {{ $statusCount['Vencido'] ?? 0 }},
                        {{ $statusCount['Cancelado'] ?? 0 }}
                    ],
                    backgroundColor: ['#198754', '#dc3545', '#6c757d'],
                    borderColor: '#212529',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#e0e7ff',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Barras - Próximos Vencimentos
        new Chart(document.getElementById('dueBarChart'), {
            type: 'bar',
            data: {
                labels: @json($upcomingDue->pluck('due_date')->map(fn($d) => date('d/m', strtotime($d)))),
                datasets: [{
                    label: 'Vencimentos',
                    data: @json($upcomingDue->pluck('id')->countBy()->values()),
                    backgroundColor: '#60a5fa',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#343a40'
                        }
                    }
                }
            }
        });
    </script>
@endsection
