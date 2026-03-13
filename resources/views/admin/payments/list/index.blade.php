@extends('layouts.app')

@section('title', 'Renovações / Pagamentos')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold">Histórico de Pagamentos / Renovações</h2>
        <a href="{{ route('payments.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i> Registrar Novo Pagamento
        </a>
    </div>

    <!-- Filtros -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="small text-muted">Cliente</label>
                    <select name="client_id" class="form-select bg-secondary text-white border-0">
                        <option value="">Todos</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Início</label>
                    <input type="date" name="start_date" class="form-control bg-secondary text-white border-0" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Fim</label>
                    <input type="date" name="end_date" class="form-control bg-secondary text-white border-0" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    @if($payments->isEmpty())
        <div class="alert alert-info text-center">Nenhum pagamento registrado ainda.</div>
    @else
        <div class="table-responsive">
            <table class="table table-dark table-hover table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Valor (Kz)</th>
                        <th>Data Pagamento</th>
                        <th>Novo Vencimento</th>
                        <th>Método</th>
                        <th>Recebido por</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->client->name }}</td>
                        <td class="fw-bold">{{ number_format($payment->amount, 2, ',', '.') }}</td>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->new_due_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->method ?? '-' }}</td>
                        <td>{{ $payment->user->name ?? 'Sistema' }}</td>
                        <td class="text-end">
                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            {{-- <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form> --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $payments->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
