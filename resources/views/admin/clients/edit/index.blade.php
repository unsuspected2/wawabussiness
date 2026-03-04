@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Editar Cliente: {{ $client->name }}</h3>
            <span class="badge {{ $client->status == 'Ativo' ? 'bg-success' : 'bg-danger' }} fs-6">{{ $client->status }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Mesmos campos do create, mas com value preenchido -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="name" class="form-control bg-secondary text-white border-0" value="{{ old('name', $client->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control bg-secondary text-white border-0" value="{{ old('whatsapp', $client->whatsapp) }}" required>
                        @error('whatsapp') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Serviço</label>
                        <input type="text" name="service" class="form-control bg-secondary text-white border-0" value="{{ old('service', $client->service) }}" required>
                        @error('service') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plano</label>
                        <select name="plan" class="form-select bg-secondary text-white border-0" required>
                            <option value="Básico" {{ old('plan', $client->plan) == 'Básico' ? 'selected' : '' }}>Básico</option>
                            <option value="Premium" {{ old('plan', $client->plan) == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('plan') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valor Pago (kz)</label>
                        <input type="number" step="0.01" name="value_paid" class="form-control bg-secondary text-white border-0" value="{{ old('value_paid', $client->value_paid) }}" required>
                        @error('value_paid') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Data de Início</label>
                        <input type="date" name="start_date" class="form-control bg-secondary text-white border-0" value="{{ old('start_date', $client->start_date) }}" required>
                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações</label>
                    <textarea name="observations" class="form-control bg-secondary text-white border-0" rows="3">{{ old('observations', $client->observations) }}</textarea>
                    @error('observations') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg flex-grow-1">Salvar Alterações</button>
                    <button type="submit" name="renew" value="1" class="btn btn-purple btn-lg flex-grow-1">Renovar Plano (30 dias)</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
    }
</style>
