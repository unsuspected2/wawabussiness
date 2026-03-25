@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="text-success fw-bold mb-4">Relatórios de Desempenho</h2>
        <!-- Botão de Exportar -->
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-export me-2"></i>Exportar Relatório Detalhado
            </button>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold mb-0">Relatórios</h2>
            <a href="{{ route('reports.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i> Exportar Tudo (Excel)
            </a>
        </div>

        <!-- Modal de Exportação -->
        <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white border-success">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Configurar Exportação</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reports.export') }}" method="GET" target="_blank">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Período -->
                                <div class="col-md-6">
                                    <label class="small text-muted">Data Início</label>
                                    <input type="date" name="start_date"
                                        class="form-control bg-secondary text-white border-0">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Data Fim</label>
                                    <input type="date" name="end_date"
                                        class="form-control bg-secondary text-white border-0">
                                </div>

                                <!-- Status -->
                                <div class="col-12">
                                    <label class="small text-muted">Status do Cliente</label>
                                    <select name="status" class="form-select bg-secondary text-white border-0">
                                        <option value="">Todos os Ativos/Vencidos</option>
                                        <option value="Ativo">Apenas Ativos</option>
                                        <option value="Vencido">Apenas Vencidos</option>
                                        <option value="trashed">Apenas Removidos (Cancelados)</option>
                                    </select>
                                </div>

                                <!-- Serviço -->
                                <div class="col-12">
                                    <label class="small text-muted">Serviço Específico</label>
                                    <input type="text" name="service"
                                        class="form-control bg-secondary text-white border-0"
                                        placeholder="Ex: Netflix, IPTV...">
                                </div>

                                <!-- Formato -->
                                <div class="col-12">
                                    <label class="small text-muted">Formato do Arquivo</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" value="pdf"
                                                id="pdf" checked>
                                            <label class="form-check-label" for="pdf text-danger"><i
                                                    class="fas fa-file-pdf"></i> PDF</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="format" value="excel"
                                                id="excel">
                                            <label class="form-check-label" for="excel text-success"><i
                                                    class="fas fa-file-excel"></i> Excel</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <!-- Dentro do modal -->
                            <button type="submit" class="btn btn-success px-4">Gerar Relatório</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- Card Faturamento -->
            <div class="col-md-4">
                <div class="card bg-dark border-success text-white">
                    <div class="card-body">
                        <h6 class="text-muted">Churn Rate (Este Mês)</h6>
                        <h3 class="text-danger">- {{ $churnCount }} Clientes</h3>
                        <p class="small text-muted">Clientes que deixaram o serviço</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Faturamento -->
            <div class="col-md-8">
                <div class="card bg-dark border-secondary text-white">
                    <div class="card-header border-secondary">Faturamento Mensal (Kz)</div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="260"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabela Top Serviços -->
            <div class="col-md-12">
                <div class="card bg-dark border-secondary text-white">
                    <div class="card-header border-secondary">Popularidade por Serviço</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($topServices as $service)
                                <div class="col-md-3 mb-3 text-center">
                                    <div class="p-3 border border-secondary rounded">
                                        <h5 class="text-success">{{ $service->name }}</h5>
                                        <p class="mb-0">{{ $service->count() }} Assinantes</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart');

        // Gradiente neon forte (igual aos dashboards modernos)
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(34, 197, 94, 0.65)'); // verde neon forte
        gradient.addColorStop(0.4, 'rgba(34, 197, 94, 0.25)');
        gradient.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

        // Efeito glow extra (truque profissional)
        const glowDataset = {
            label: 'Glow',
            data: @json($monthlyRevenue->pluck('total')->reverse()),
            borderColor: 'rgba(34, 197, 94, 0.4)',
            borderWidth: 12,
            tension: 0.45,
            pointRadius: 0,
            fill: false
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($monthlyRevenue->pluck('month')->reverse()),
                datasets: [
                    glowDataset, // camada de glow primeiro
                    {
                        label: 'Faturamento Mensal',
                        data: @json($monthlyRevenue->pluck('total')->reverse()),
                        borderColor: '#22c55e',
                        borderWidth: 5,
                        backgroundColor: gradient,
                        pointBackgroundColor: '#0f172a',
                        pointBorderColor: '#22c55e',
                        pointRadius: 7,
                        pointHoverRadius: 11,
                        pointBorderWidth: 4,
                        tension: 0.45,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        borderColor: '#22c55e',
                        borderWidth: 2,
                        titleColor: '#94a3b8',
                        bodyColor: '#22c55e',
                        bodyFont: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: 16,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => ctx.raw.toLocaleString('pt-AO') + ' Kz',
                            title: (ctx) => 'Mês: ' + ctx[0].label
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#334155',
                            lineWidth: 1
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 14
                            },
                            callback: (value) => value.toLocaleString('pt-AO') + ' Kz',
                            stepSize: 2000
                        }
                    },
                    x: {
                        grid: {
                            color: '#334155',
                            lineWidth: 1
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 14
                            },
                            maxRotation: 0
                        }
                    }
                },
                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    }
                },
                animation: {
                    duration: 2200,
                    easing: 'easeOutExpo'
                }
            }
        });
    </script>
@endsection
