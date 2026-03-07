@extends('layouts.app')

@section('title', 'Saques / Caixa')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold">Saques / Retiradas do Caixa</h2>
        <a href="{{ route('withdrawals.create') }}" class="btn btn-warning">
            <i class="fas fa-hand-holding-usd me-2"></i> Novo Saque
        </a>
    </div>

    <!-- Filtros -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="small text-muted">Status de Reposição</label>
                    <select name="repay_status" class="form-select bg-secondary text-white border-0">
                        <option value="">Todos</option>
                        <option value="Pendente" {{ request('repay_status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Reposto" {{ request('repay_status') == 'Reposto' ? 'selected' : '' }}>Reposto</option>
                        <option value="Não vai repor" {{ request('repay_status') == 'Não vai repor' ? 'selected' : '' }}>Não vai repor</option>
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

    @if($withdrawals->isEmpty())
        <div class="alert alert-info text-center">Nenhum saque registrado ainda.</div>
    @else
        <div class="table-responsive">
            <table class="table table-dark table-hover table-striped">
                <thead>
                    <tr>
                        <th>Valor (Kz)</th>
                        <th>Data Saque</th>
                        <th>Motivo</th>
                        <th>Finalidade</th>
                        <th>Repor em</th>
                        <th>Status Reposição</th>
                        <th>Feito por</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $withdrawal)
                    <tr>
                        <td class="fw-bold text-danger">{{ number_format($withdrawal->amount, 2, ',', '.') }}</td>
                        <td>{{ $withdrawal->withdrawal_date->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($withdrawal->reason, 40) }}</td>
                        <td>{{ Str::limit($withdrawal->purpose, 40) }}</td>
                        <td>
                            @if($withdrawal->repay_date)
                                {{ $withdrawal->repay_date->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $withdrawal->repay_status == 'Reposto' ? 'bg-success' : ($withdrawal->repay_status == 'Pendente' ? 'bg-warning' : 'bg-secondary') }}">
                                {{ $withdrawal->repay_status }}
                            </span>
                        </td>
                        <td>{{ $withdrawal->user->name ?? 'Sistema' }}</td>
                        <td class="text-end">
                            <a href="{{ route('withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('withdrawals.edit', $withdrawal) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('withdrawals.destroy', $withdrawal) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirma exclusão?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $withdrawals->appends(request()->query())->links('pagination::bootstrap-5') }}

        </div>
    @endif
</div>
@endsection
