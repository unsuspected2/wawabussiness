@extends('layouts.app')

@section('title', 'Detalhes do Pagamento #' . $payment->id)

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Pagamento #{{ $payment->id }}</h3>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Cliente</h5>
                    <p class="lead">{{ $payment->client->name }} ({{ $payment->client->whatsapp }})</p>
                </div>
                <div class="col-md-6">
                    <h5>Valor</h5>
                    <p class="lead">{{ number_format($payment->amount, 2, ',', '.') }} Kz</p>
                </div>
            </div>

            <hr class="bg-secondary">

            <div class="row">
                <div class="col-md-4">
                    <h5>Data Pagamento</h5>
                    <p>{{ $payment->payment_date->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Novo Vencimento</h5>
                    <p>{{ $payment->new_due_date->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Recebido por</h5>
                    <p>{{ $payment->user->name ?? 'Sistema' }}</p>
                </div>
            </div>

            @if($payment->notes)
                <hr class="bg-secondary">
                <h5>Observações</h5>
                <p>{{ $payment->notes }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
