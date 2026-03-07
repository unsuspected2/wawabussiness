@extends('layouts.app')

@section('title', 'Editar Saque')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0">Editar Saque #{{ $withdrawal->id }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('withdrawals.update', $withdrawal) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Valor do Saque (Kz)</label>
                    <input type="number" step="0.01" name="amount" class="form-control bg-secondary text-white border-0"
                           value="{{ old('amount', $withdrawal->amount) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Motivo do Saque</label>
                    <textarea name="reason" class="form-control bg-secondary text-white border-0" rows="3" required>{{ old('reason', $withdrawal->reason) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Para que fim serviu?</label>
                    <textarea name="purpose" class="form-control bg-secondary text-white border-0" rows="3" required>{{ old('purpose', $withdrawal->purpose) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Previsão de Reposição</label>
                        <input type="date" name="repay_date" class="form-control bg-secondary text-white border-0"
                               value="{{ old('repay_date', $withdrawal->repay_date ? $withdrawal->repay_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status de Reposição</label>
                        <select name="repay_status" class="form-select bg-secondary text-white border-0" required>
                            <option value="Pendente" {{ old('repay_status', $withdrawal->repay_status) == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Reposto" {{ old('repay_status', $withdrawal->repay_status) == 'Reposto' ? 'selected' : '' }}>Reposto</option>
                            <option value="Não vai repor" {{ old('repay_status', $withdrawal->repay_status) == 'Não vai repor' ? 'selected' : '' }}>Não vai repor</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg w-100">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>
@endsection
