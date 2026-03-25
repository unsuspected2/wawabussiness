@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4 px-4">
        <!-- Cabeçalho Principal -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="text-success fw-bold mb-0">Dashboard WawaBusiness</h1>
                <p class="text-muted">Bem-vindo ao controle financeiro e de clientes.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-warning btn-lg px-4" data-bs-toggle="modal" data-bs-target="#saqueModal">
                    <i class="fas fa-hand-holding-usd me-2"></i> Novo Saque
                </button>
                <a href="{{ route('clients.create') }}" class="btn btn-success btn-lg px-4">
                    <i class="fas fa-plus me-2"></i> Novo Cliente
                </a>
            </div>
        </div>

        <!-- Cards de Resumo Financeiro e Clientes -->
        <div class="row g-4 mb-5">
            <!-- Saldo do Mês (Destaque Principal) -->
            <div class="col-xl-4 col-md-12">
                <div class="card bg-dark text-white shadow border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted small uppercase">Saldo do Mês Atual</h6>
                        <h2 class="fw-bold {{ $monthlyBalance >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                            {{ number_format($monthlyBalance, 2, ',', '.') }} Kz
                        </h2>
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top border-secondary small">
                            <div>
                                <span class="text-success">Entradas:</span><br>
                                <strong>{{ number_format($monthlyInflows, 2, ',', '.') }} Kz</strong>
                            </div>
                            <div class="text-end">
                                <span class="text-danger">Saídas:</span><br>
                                <strong>{{ number_format($monthlyOutflows, 2, ',', '.') }} Kz</strong>
                            </div>
                        </div>
                        @if ($lastClosure)
                            <div class="mt-3 pt-3 border-top border-secondary small text-muted">
                                Saldo inicial: {{ number_format($startingBalance, 2, ',', '.') }} Kz
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Outros Cards menores -->
            <div class="col-xl-2 col-md-4">
                <div class="card bg-dark text-white shadow h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Pendentes Reposição</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($pendingRepay, 2, ',', '.') }}</h3>
                        <small class="text-muted">A recuperar</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card bg-dark text-white shadow h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Clientes Ativos</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $activeClients }}</h3>
                        <small class="text-muted">De {{ $totalClients }} totais</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card bg-dark text-white shadow h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Vencidos</h6>
                        <h3 class="fw-bold text-danger mb-0">{{ $overdueClients }}</h3>
                        <small class="text-muted">Ação necessária</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card bg-dark text-white shadow h-100">
                    <div class="card-body">
                        <h6 class="text-muted small">Total Histórico</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.2rem">{{ number_format($totalPaid, 0, ',', '.') }}
                        </h3>
                        <small class="text-muted">Desde o início</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card bg-dark text-white shadow border-0">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Distribuição de Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusPieChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-dark text-white shadow border-0">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Vencimentos (Próx. 7 dias)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dueBarChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- NOVA ÁREA: Tarefas e Alertas de Vencimento -->
        <div class="card bg-dark text-white shadow border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Tarefas e Alertas de Vencimento</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Vencimento</th>
                                <th>Alerta</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingExpirations as $client)
                                @php $daysLeft = \Carbon\Carbon::today()->diffInDays($client->due_date); @endphp <tr>
                                    <td class="fw-bold">{{ $client->name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($client->due_date)) }}</td>
                                    <td class="{{ $daysLeft <= 3 ? 'text-danger fw-bold' : 'text-warning' }}">
                                        @if ($daysLeft <= 3)
                                            <i class="fas fa-exclamation-triangle"></i> {{ $daysLeft }} dias!
                                        @else
                                            {{ $daysLeft }} dias
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                            data-bs-target="#taskModal" onclick="setClientId({{ $client->id }})">
                                            <i class="fas fa-plus"></i> Deixar Tarefa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Nenhum vencimento próximo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Tabela de Exclusões -->
        <div class="card bg-dark text-white shadow border-0">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-user-slash me-2"></i>Exclusões Recentes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-muted">
                                <th class="ps-4">Cliente</th>
                                <th>Motivo da Eliminação</th>
                                <th>Eliminado por</th>
                                <th class="pe-4">Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDeletions as $log)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $log->subject->name ?? 'Removido permanentemente' }}</td>
                                    <td class="text-warning">{{ $log->subject->deleted_reason ?? 'Motivo não informado' }}
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $log->causer->name ?? 'Sistema' }}</span></td>
                                    <td class="pe-4">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Nenhuma exclusão recente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Saque Estilizado -->
    <div class="modal fade" id="saqueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-warning">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning"><i class="fas fa-money-bill-wave me-2"></i>Registrar Novo Saque
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('withdrawals.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Valor do Saque (Kz)</label>
                            <input type="number" name="amount" step="0.01"
                                class="form-control bg-secondary text-white border-0" placeholder="0,00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Razão do Saque</label>
                            <input type="text" name="reason" class="form-control bg-secondary text-white border-0"
                                placeholder="Ex: Pagamento de Internet" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Descrição Detalhada (Opcional)</label>
                            <textarea name="purpose" class="form-control bg-secondary text-white border-0" rows="2"
                                placeholder="Para que serviu este valor?"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Data Prevista Reposição</label>
                                <input type="date" name="repay_date"
                                    class="form-control bg-secondary text-white border-0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Status de Reposição</label>
                                <select name="repay_status" class="form-select bg-secondary text-white border-0">
                                    <option value="Pendente">Pendente</option>
                                    <option value="Reposto">Já repus</option>
                                    <option value="Não vai repor">Não vai repor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">Confirmar Saque</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal de tarefa rápida -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Adicionar Tarefa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('clients.update.task') }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="client_id" id="modalClientId">
                    <div class="modal-body">
                        <textarea name="observations" class="form-control bg-secondary text-white" rows="4" required
                            placeholder="Ex: Contactar cliente para renovar..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Salvar Tarefa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Scripts de Gráficos (Mantidos conforme original com pequenos ajustes de cores) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        function setClientId(id) {
            document.getElementById('modalClientId').value = id;
        }


        // ... (Seu script de Chart.js permanece o mesmo, ele já está funcional) ...
        // Gráfico de Pizza
        new Chart(document.getElementById('statusPieChart'), {
            type: 'pie',
            data: {
                labels: ['Ativos', 'Vencidos', 'Cancelados'],
                datasets: [{
                    data: [{{ $statusCount['Ativo'] ?? 0 }}, {{ $statusCount['Vencido'] ?? 0 }},
                        {{ $statusCount['Cancelado'] ?? 0 }}
                    ],
                    backgroundColor: ['#198754', '#dc3545', '#6c757d'],
                    borderColor: '#212529',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#e0e7ff'
                        }
                    }
                }
            }
        });

        // Gráfico de Barras
        new Chart(document.getElementById('dueBarChart'), {
            type: 'bar',
            data: {
                labels: @json($upcomingDue->pluck('due_date')->map(fn($d) => date('d/m', strtotime($d)))),
                datasets: [{
                    label: 'Vencimentos',
                    data: @json($upcomingDue->pluck('id')->countBy()->values()),
                    backgroundColor: '#60a5fa',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#343a40'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endsection
