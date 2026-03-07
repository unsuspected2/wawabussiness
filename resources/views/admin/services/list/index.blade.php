@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success fw-bold">Serviços Cadastrados</h2>
        <a href="{{ route('services.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i> Novo Serviço
        </a>
    </div>

    @if($services->isEmpty())
        <div class="alert alert-info text-center">
            Nenhum serviço cadastrado ainda.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-dark table-hover table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Ícone</th>
                        <th>Preço Padrão (Kz)</th>
                        <th>Descrição</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="fw-medium">{{ $service->name }}</td>
                        <td>
                            @if($service->icon)
                                <i class="{{ $service->icon }} fa-2x"></i>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($service->default_price)
                                {{ number_format($service->default_price, 2, ',', '.') }} Kz
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-muted">{{ Str::limit($service->description, 60) }}</td>
                        <td class="text-end">
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
