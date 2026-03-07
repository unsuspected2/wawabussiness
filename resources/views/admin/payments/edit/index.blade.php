@extends('layouts.app')

@section('title', 'Editar Pagamento')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success">
            <h3 class="mb-0">Editar Pagamento #{{ $payment->id }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.update', $payment) }}" method="POST">
                @csrf @method('PUT')

                <!-- Campos iguais ao create, mas com valores preenchidos -->
                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select bg-secondary text-white border-0" required>
                        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $payment->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Pago (Kz)</label>
                        <input type="number" step="0.01" name="amount" class="form-control bg-secondary text-white border-0"
                               value="{{ old('amount', $payment->amount) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data do Pagamento</label>
                        <input type="date" name="payment_date" class="form-control bg-secondary text-white border-0"
                               value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nova Data de Vencimento</label>
                        <input type="date" name="new_due_date" class="form-control bg-secondary text-white border-0"
                               value="{{ old('new_due_date', $payment->new_due_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Método</label>
                        <input type="text" name="method" class="form-control bg-secondary text-white border-0"
                               value="{{ old('method', $payment->method) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notas</label>
                    <textarea name="notes" class="form-control bg-secondary text-white border-0" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>
@endsection
