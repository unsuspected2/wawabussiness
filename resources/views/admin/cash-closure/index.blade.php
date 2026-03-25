@extends('layouts.app')

@section('title', 'Fechamento de Caixa Mensal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Fechamento de Caixa Mensal</h1>
        <a href="{{ route('cash-closure.index') }}" class="btn btn-outline-secondary">
            ← Voltar
        </a>
    </div>

    <!-- Card do Mês Atual -->
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body">
            <h5 class="mb-3">
                Mês Atual: <strong>{{ now()->format('F Y') }}</strong>
            </h5>

            @if (!$closures->where('year_month', now()->format('Y-m'))->first())
                <form action="{{ route('cash-closure.close', now()->format('Y-m')) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg px-4" 
                            onclick="return confirm('Tem certeza que deseja fechar o caixa deste mês? Esta ação não pode ser desfeita facilmente.')">
                        <i class="fas fa-lock me-2"></i>
                        Fechar Caixa de {{ now()->format('F Y') }}
                    </button>
                </form>
                <small class="text-muted mt-2 d-block">
                    Ao fechar, os movimentos deste mês ficarão protegidos contra alterações.
                </small>
            @else
                <div class="d-flex align-items-center">
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-check-circle me-2"></i>
                        Caixa já fechado
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Histórico de Fechamentos -->
    <div class="card bg-dark border-secondary">
        <div class="card-header bg-transparent border-bottom border-secondary">
            <h5 class="mb-0">Histórico de Fechamentos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>Mês/Ano</th>
                            <th>Data do Fechamento</th>
                            <th class="text-end">Entradas</th>
                            <th class="text-end">Saques</th>
                            <th class="text-end">Despesas</th>
                            <th class="text-end fw-bold">Saldo Final</th>
                            <th>Fechado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($closures as $closure)
                            <tr>
                                <td>
                                    <strong>{{ $closure->year_month }}</strong><br>
                                    <small class="text-muted">{{ $closure->formatted_month }}</small>
                                </td>
                                <td>{{ $closure->closed_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end text-success">
                                    {{ number_format($closure->total_inflows, 2, ',', '.') }} Kz
                                </td>
                                <td class="text-end text-danger">
                                    {{ number_format($closure->total_outflows, 2, ',', '.') }} Kz
                                </td>
                                <td class="text-end">
                                    {{ number_format($closure->total_expenses, 2, ',', '.') }} Kz
                                </td>
                                <td class="text-end fw-bold">
                                    {{ number_format($closure->ending_balance, 2, ',', '.') }} Kz
                                </td>
                                <td>{{ $closure->user->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Ainda não existem fechamentos de caixa registados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection