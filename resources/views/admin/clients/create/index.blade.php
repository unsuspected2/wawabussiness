@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">Cadastrar Novo Cliente</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="name" class="form-control bg-secondary text-white border-0" placeholder="Nome completo" value="{{ old('name') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control bg-secondary text-white border-0" placeholder="Ex: +244 9xx xxx xxx" value="{{ old('whatsapp') }}" required>
                        @error('whatsapp') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Serviço</label>
                        <input type="text" name="service" class="form-control bg-secondary text-white border-0" placeholder="Netflix, Spotify, Disney+, etc." value="{{ old('service') }}" required>
                        @error('service') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plano</label>
                        <select name="plan" class="form-select bg-secondary text-white border-0" required>
                            <option value="">Selecione o plano</option>
                            <option value="Básico" {{ old('plan') == 'Básico' ? 'selected' : '' }}>Básico</option>
                            <option value="Premium" {{ old('plan') == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('plan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Pago (kz)</label>
                        <input type="number" step="0.01" name="value_paid" class="form-control bg-secondary text-white border-0" placeholder="Ex: 2500.00" value="{{ old('value_paid') }}" required>
                        @error('value_paid') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data de Início</label>
                        <input type="date" name="start_date" class="form-control bg-secondary text-white border-0" value="{{ old('start_date') }}" required>
                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações</label>
                    <textarea name="observations" class="form-control bg-secondary text-white border-0" rows="3" placeholder="Notas adicionais...">{{ old('observations') }}</textarea>
                    @error('observations') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Cadastrar Cliente</button>
            </form>
        </div>
    </div>
</div>
@endsection
