@extends('layouts.app')

@section('title', 'Novo Saque')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0">Registrar Saque do Caixa</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('withdrawals.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Valor do Saque (Kz)</label>
                    <input type="number" step="0.01" name="amount" class="form-control bg-secondary text-white border-0"
                           placeholder="Ex: 5000.00" required>
                    @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Motivo do Saque (obrigatório)</label>
                    <textarea name="reason" class="form-control bg-secondary text-white border-0" rows="3" required
                              placeholder="Ex: Pagamento de fornecedor, conta de luz..."></textarea>
                    @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Para que fim serviu? (obrigatório)</label>
                    <textarea name="purpose" class="form-control bg-secondary text-white border-0" rows="3" required
                              placeholder="Ex: Compra de equipamentos, pagamento de funcionário..."></textarea>
                    @error('purpose') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Previsão de Reposição (opcional)</label>
                        <input type="date" name="repay_date" class="form-control bg-secondary text-white border-0">
                        @error('repay_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status de Reposição</label>
                        <select name="repay_status" class="form-select bg-secondary text-white border-0" required>
                            <option value="Pendente">Pendente (vou repor depois)</option>
                            <option value="Reposto">Já repus</option>
                            <option value="Não vai repor">Não vai repor (despesa definitiva)</option>
                        </select>
                        @error('repay_status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg w-100">Confirmar Saque</button>
            </form>
        </div>
    </div>
</div>
@endsection
