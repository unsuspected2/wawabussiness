@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success mb-0">Lista de Clientes</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-success">+ Novo Cliente</a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="mb-4 bg-dark p-3 rounded shadow">
        <div class="row g-3">
            <div class="col-md-4">
                <select name="status" class="form-select bg-secondary text-white border-0">
                    <option value="">Todos os status</option>
                    <option value="Ativo" {{ request('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Vencido" {{ request('status') == 'Vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="Cancelado" {{ request('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="service" class="form-control bg-secondary text-white border-0" placeholder="Filtrar por serviço (ex: Netflix)" value="{{ request('service') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-dark table-hover table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>WhatsApp</th>
                    <th>Serviço / Plano</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->whatsapp }}</td>
                    <td>{{ $client->service }} <small class="text-muted">({{ $client->plan }})</small></td>
                    <td>{{ $client->due_date ? date('d/m/Y', strtotime($client->due_date)) : '-' }}</td>
                    <td>
                        <span class="badge {{ $client->status == 'Ativo' ? 'bg-success' : ($client->status == 'Vencido' ? 'bg-danger' : 'bg-secondary') }} fs-6">
                            {{ $client->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Nenhum cliente encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Se quiser paginação no futuro: -->
    <!-- {{ $clients->links() }} -->
</div>
@endsection
