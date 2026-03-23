@extends('layouts.app')

@section('title', 'Detalhes do Perfil Alocado #' . $perfilAlocado->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalhes do Perfil Alocado</h1>
        <div>
            <a href="{{ route('perfis-alocados.index') }}" class="btn btn-outline-secondary me-2">← Lista</a>
            <a href="{{ route('perfis-alocados.edit', $perfilAlocado) }}" class="btn btn-warning">Editar</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <strong>Informações Gerais</strong>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Cliente</dt>
                <dd class="col-sm-8">{{ $perfilAlocado->cliente->nome ?? '—' }} (ID: {{ $perfilAlocado->client_id }})</dd>

                <dt class="col-sm-4">Serviço</dt>
                <dd class="col-sm-8">{{ $perfilAlocado->servico->nome ?? $perfilAlocado->service_id }}</dd>

                <dt class="col-sm-4">Tipo de Alocação</dt>
                <dd class="col-sm-8">{{ ucfirst($perfilAlocado->tipo_alocacao) }}</dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    <span class="badge bg-{{ $perfilAlocado->status === 'ativo' ? 'success' : 'danger' }}">
                        {{ ucfirst($perfilAlocado->status) }}
                    </span>
                </dd>

                <dt class="col-sm-4">Vinculado ao Pagamento</dt>
                <dd class="col-sm-8">
                    ID: {{ $perfilAlocado->payment_id }}<br>
                    Vencimento: {{ $perfilAlocado->pagamento?->data_vencimento?->format('d/m/Y') ?? '—' }}<br>
                    Valor: {{ number_format($perfilAlocado->pagamento?->valor ?? 0, 2, ',', '.') }} Kz
                </dd>

                <dt class="col-sm-4">Criado em</dt>
                <dd class="col-sm-8">{{ $perfilAlocado->created_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-light">
            <strong>Credenciais</strong>
        </div>
        <div class="card-body">
            @if($perfilAlocado->tipo_alocacao === 'perfil')
                <p><strong>Nome do Perfil:</strong> {{ $perfilAlocado->nome_perfil ?? '—' }}</p>
                <p><strong>Login do Perfil:</strong> <code>{{ $perfilAlocado->login_perfil ?? '—' }}</code></p>
                <p><strong>Senha do Perfil:</strong> <code>{{ $perfilAlocado->senha_perfil ?? '—' }}</code></p>
            @else
                <p><strong>Email da Conta:</strong> <code>{{ $perfilAlocado->email_conta ?? '—' }}</code></p>
                <p><strong>Senha da Conta:</strong> <code>{{ $perfilAlocado->senha_conta ?? '—' }}</code></p>
            @endif

            @if($perfilAlocado->observacao)
                <hr>
                <p><strong>Observações:</strong></p>
                <pre class="bg-light p-3 rounded">{{ $perfilAlocado->observacao }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection