@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold"><i class="fas fa-users me-2"></i>Gestão de Clientes</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-success shadow-sm">+ Novo Cliente</a>
    </div>

    <!-- Barra de Filtros -->
    <div class="card bg-dark border-secondary mb-4 shadow">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-12 mb-2">
                    <label class="text-muted small d-block mb-2">Filtrar por Status:</label>
                    <div class="btn-group shadow-sm">
                        <!-- Botão Todos: Limpa todos os filtros da URL -->
                        <a href="{{ route('clients.index') }}"
                           class="btn btn-sm {{ !request('status') && !request('trashed') ? 'btn-light' : 'btn-outline-light' }}">Todos</a>

                        <!-- Botão Ativos -->
                        <a href="{{ route('clients.index', ['status' => 'Ativo']) }}"
                           class="btn btn-sm {{ request('status') == 'Ativo' ? 'btn-success' : 'btn-outline-success' }}">Ativos</a>

                        <!-- Botão Vencidos -->
                        <a href="{{ route('clients.index', ['status' => 'Vencido']) }}"
                           class="btn btn-sm {{ request('status') == 'Vencido' ? 'btn-danger' : 'btn-outline-danger' }}">Vencidos</a>

                        <!-- Botão Removidos -->
                        <a href="{{ route('clients.index', ['trashed' => 1]) }}"
                           class="btn btn-sm {{ request('trashed') ? 'btn-secondary' : 'btn-outline-secondary' }}">Removidos</a>
                    </div>
                </div>

                <div class="col-md-9">
                    <input type="text" name="search" class="form-control bg-secondary text-white border-0"
                           placeholder="Buscar por nome ou serviço..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive rounded shadow-lg">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-secondary text-dark">
                <tr>
                    <th>Nome</th>
                    <th>Serviço / Plano</th>
                    <th>Vencimento</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr style="{{ $client->trashed() ? 'opacity: 0.6;' : '' }}">
                    <td>{{ $client->name }}</td>
                    <td><span class="badge bg-primary">{{ $client->service }}</span><br><small class="text-muted">{{ $client->plan }}</small></td>
                    <td>{{ \Carbon\Carbon::parse($client->due_date)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($client->trashed())
                            <span class="badge bg-secondary">Removido</span>
                        @else
                            <span class="badge {{ $client->status == 'Ativo' ? 'bg-success' : 'bg-danger' }}">
                                {{ $client->status }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if(!$client->trashed())
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>

                            <!-- Botão Excluir que abre o Modal -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#del{{ $client->id }}">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Modal de Motivo -->
                            <div class="modal fade" id="del{{ $client->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark border-danger text-white">
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title text-danger">Excluir Cliente</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <p>Deseja excluir <strong>{{ $client->name }}</strong>?</p>
                                                <label class="small text-muted mb-1">Motivo da exclusão (obrigatório):</label>
                                                <textarea name="deleted_reason" class="form-control bg-secondary text-white border-0" required></textarea>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="submit" class="btn btn-danger">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Mostra o Motivo para os Removidos -->
                            <div class="text-start">
                                <small class="text-danger fw-bold">Motivo: </small>
                                <small class="text-white">{{ $client->deleted_reason ?? 'Não informado' }}</small>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">Nenhum cliente encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 d-flex justify-content-center">

        {{ $clients->appends(request()->query())->links('pagination::bootstrap-5') }}

    </div>
</div>
@endsection
