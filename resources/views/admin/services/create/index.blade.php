@extends('layouts.app')

@section('title', 'Novo Serviço')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success">
            <h3 class="mb-0">Cadastrar Novo Serviço</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('services.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nome do Serviço</label>
                    <input type="text" name="name" class="form-control bg-secondary text-white border-0"
                           value="{{ old('name') }}" placeholder="Ex: Netflix, IPTV, HBO" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Ícone (Font Awesome class)</label>
                    <input type="text" name="icon" class="form-control bg-secondary text-white border-0"
                           value="{{ old('icon') }}" placeholder="Ex: fab fa-netflix, fas fa-tv">
                    @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Preço Padrão (Kz)</label>
                    <input type="number" step="0.01" name="default_price" class="form-control bg-secondary text-white border-0"
                           value="{{ old('default_price') }}" placeholder="Ex: 2500.00">
                    @error('default_price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" class="form-control bg-secondary text-white border-0" rows="4">{{ old('description') }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">Cadastrar Serviço</button>
            </form>
        </div>
    </div>
</div>
@endsection
