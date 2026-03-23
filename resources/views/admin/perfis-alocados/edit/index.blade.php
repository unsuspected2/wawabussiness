@extends('layouts.app')

@section('title', 'Editar Perfil Alocado')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Editar Perfil Alocado #{{ $perfilAlocado->id }}</h1>
            <a href="{{ route('perfis-alocados.index') }}" class="btn btn-outline-secondary">
                ← Voltar à lista
            </a>
        </div>

        @include('layouts._form.edit', [
            'action' => route('perfis-alocados.update', $perfilAlocado->id),
            'method' => 'PUT',
            'perfilAlocado' => $perfilAlocado,
            'clientes' => $clientes ?? [],
            'pagamentos' => $pagamentos ?? [],
        ])
        <!-- Botão de excluir (com confirmação) -->
        <div class="mt-5 border-top pt-4">
            <!-- Botão de excluir -->
            <form action="{{ route('perfis-alocados.destroy', $perfilAlocado->id) }}" method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir este perfil?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    Excluir este Perfil
                </button>
            </form>
            </form>
        </div>
    </div>
@endsection
