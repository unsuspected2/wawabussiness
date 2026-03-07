@extends('layouts.app')

@section('title', 'Editar Serviço')

@section('content')
<div class="container mt-5">
    <div class="card bg-dark text-white shadow">
        <div class="card-header bg-success">
            <h3 class="mb-0">Editar Serviço: {{ $service->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome do Serviço</label>
                    <input type="text" name="name" class="form-control bg-secondary text-white border-0"
                           value="{{ old('name', $service->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Ícone (Font Awesome class)</label>
                    <input type="text" name="icon" class="form-control bg-secondary text-white border-0"
                           value="{{ old('icon', $service->icon) }}">
                    @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Preço Padrão (Kz)</label>
                    <input type="number" step="0.01" name="default_price" class="form-control bg-secondary text-white border-0"
                           value="{{ old('default_price', $service->default_price) }}">
                    @error('default_price') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" class="form-control bg-secondary text-white border-0" rows="4">{{ old('description', $service->description) }}</textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg flex-grow-1">Salvar Alterações</button>
                    <a href="{{ route('services.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
