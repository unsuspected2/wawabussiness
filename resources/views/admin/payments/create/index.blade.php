@extends('layouts.app')

@section('title', 'Novo Pagamento / Renovação')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success">
            <h3 class="mb-0">Registrar Pagamento / Renovação</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select bg-secondary text-white border-0" required>
                        <option value="">Selecione o cliente</option>
                        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} - {{ $client->whatsapp }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Pago (Kz)</label>
                        <input type="number" step="0.01" name="amount" class="form-control bg-secondary text-white border-0"
                               value="{{ old('amount') }}" required>
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data do Pagamento</label>
                        <input type="date" name="payment_date" class="form-control bg-secondary text-white border-0"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nova Data de Vencimento</label>
                        <input type="date" name="new_due_date" class="form-control bg-secondary text-white border-0"
                               value="{{ old('new_due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                        @error('new_due_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Método de Pagamento</label>
                        <input type="text" name="method" class="form-control bg-secondary text-white border-0"
                               placeholder="Multicaixa, Transferência, Numerário..." value="{{ old('method') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações / Notas</label>
                    <textarea name="notes" class="form-control bg-secondary text-white border-0" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Registrar Pagamento</button>
            </form>
        </div>
    </div>
</div>
@endsection
