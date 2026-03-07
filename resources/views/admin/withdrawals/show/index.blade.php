@extends('layouts.app')

@section('title', 'Detalhes do Saque #' . $withdrawal->id)

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Saque #{{ $withdrawal->id }}</h3>
            <a href="{{ route('withdrawals.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5>Valor</h5>
                    <p class="lead text-danger">{{ number_format($withdrawal->amount, 2, ',', '.') }} Kz</p>
                </div>
                <div class="col-md-4">
                    <h5>Data do Saque</h5>
                    <p>{{ $withdrawal->withdrawal_date->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Feito por</h5>
                    <p>{{ $withdrawal->user->name ?? 'Sistema' }}</p>
                </div>
            </div>

            <hr class="bg-secondary">

            <h5>Motivo</h5>
            <p>{{ $withdrawal->reason }}</p>

            <h5>Para que fim serviu?</h5>
            <p>{{ $withdrawal->purpose }}</p>

            <hr class="bg-secondary">

            <div class="row">
                <div class="col-md-6">
                    <h5>Previsão de Reposição</h5>
                    <p>{{ $withdrawal->repay_date ? $withdrawal->repay_date->format('d/m/Y') : 'Não definido' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Status de Reposição</h5>
                    <span class="badge {{ $withdrawal->repay_status == 'Reposto' ? 'bg-success' : ($withdrawal->repay_status == 'Pendente' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ $withdrawal->repay_status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
