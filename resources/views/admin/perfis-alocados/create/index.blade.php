@extends('layouts.app')  <!-- ou layouts.dashboard, o que usares -->

@section('title', 'Criar Novo Perfil Alocado')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Criar Novo Perfil Alocado</h1>
        <a href="{{ route('perfis-alocados.index') }}" class="btn btn-outline-secondary">
            ← Voltar à lista
        </a>
    </div>

    @include('layouts._form.create', [
        'action' => route('perfis-alocados.store'),
        'method' => 'POST',
        'clientes' => $clientes ?? [],
        'pagamentos' => [],  // Pode vir vazio ou pré-carregado via JS/Ajax
    ])

    <!-- Se quiseres usar Livewire/Alpine para carregar pagamentos dinamicamente pelo cliente selecionado -->
</div>
@endsection