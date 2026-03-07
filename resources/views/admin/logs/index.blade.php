@extends('layouts.app')

@section('title', 'Logs de Atividades')

@section('content')
<div class="container mt-5">
    <h2 class="text-success fw-bold mb-4">Logs de Atividades do Sistema</h2>

    <!-- Filtros -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="small text-muted">Tipo de Log</label>
                    <select name="log_name" class="form-select bg-secondary text-white border-0">
                        <option value="">Todos</option>
                        <option value="clientes" {{ request('log_name') == 'clientes' ? 'selected' : '' }}>Clientes</option>
                        <option value="pagamentos" {{ request('log_name') == 'pagamentos' ? 'selected' : '' }}>Pagamentos</option>
                        <option value="saques" {{ request('log_name') == 'saques' ? 'selected' : '' }}>Saques</option>
                        <option value="serviços" {{ request('log_name') == 'serviços' ? 'selected' : '' }}>Serviços</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Evento</label>
                    <select name="event" class="form-select bg-secondary text-white border-0">
                        <option value="">Todos</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Criado</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Atualizado</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Excluído</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Usuário</label>
                    <select name="causer_id" class="form-select bg-secondary text-white border-0">
                        <option value="">Todos</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <input type="text" name="search" class="form-control bg-secondary text-white border-0 me-2"
                           placeholder="Buscar..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-success">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    @if($logs->isEmpty())
        <div class="alert alert-info text-center">Nenhum log encontrado.</div>
    @else
        <div class="table-responsive">
            <table class="table table-dark table-hover table-striped">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->causer ? $log->causer->name : 'Sistema' }}</td>
                        <td>
                            <span class="badge {{ $log->event == 'created' ? 'bg-success' : ($log->event == 'updated' ? 'bg-info' : 'bg-danger') }}">
                                {{ ucfirst($log->event) }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#details{{ $log->id }}">
                                Ver detalhes
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de detalhes -->
                    <div class="modal fade" id="details{{ $log->id }}">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header">
                                    <h5>Detalhes da Atividade</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <pre class="bg-secondary p-3 rounded" style="white-space: pre-wrap;">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
{{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}    </div>
    @endif
</div>
@endsection
