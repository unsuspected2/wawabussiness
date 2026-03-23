@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Perfis Alocados</h1>
    
    <a href="{{ route('perfis-alocados.create') }}" class="btn btn-primary mb-3">+ Novo Perfil</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Tipo</th>
                    <th>Perfil / Conta</th>
                    <th>Expira em</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perfisAlocados as $perfil)
                <tr>
                    <td>{{ $perfil->cliente->name ?? '—' }}</td>
                    <td>{{ $perfil->servico->name ?? '—' }}</td>
                    <td>{{ ucfirst($perfil->tipo_alocacao) }}</td>
                    <td>
                        @if($perfil->tipo_alocacao === 'perfil')
                            <strong>{{ $perfil->nome_perfil }}</strong><br>
                            Login: {{ $perfil->login_perfil }}<br>
                            Senha: {{ $perfil->senha_perfil }}
                        @else
                            <strong>Conta Pessoal</strong><br>
                            Email: {{ $perfil->email_conta }}<br>
                            Senha: {{ $perfil->senha_conta }}
                        @endif
                    </td>
                    <td>
                        {{ $perfil->pagamento?->new_due_date?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $perfil->status === 'ativo' ? 'success' : 'danger' }}">
                            {{ ucfirst($perfil->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('perfis-alocados.edit', $perfil) }}" class="btn btn-sm btn-warning">Editar</a>
                        <!-- Botão destroy com confirmação -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $perfisAlocados->links() }}
</div>
@endsection