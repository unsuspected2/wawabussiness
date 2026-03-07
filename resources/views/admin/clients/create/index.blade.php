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
                        <div class="mb-3" x-data="clientSearch()">
                            <label class="form-label">Nome do Cliente (buscar existente)</label>
                            <input type="text" x-model="search" @input.debounce.500ms="searchClients()"
                                class="form-control bg-secondary text-white border-0"
                                placeholder="Digite nome ou WhatsApp..." required>

                            <!-- Resultados -->
                            <div class="position-relative">
                                <ul class="list-group position-absolute w-100 mt-1 shadow"
                                    style="z-index: 1000; max-height: 250px; overflow-y: auto;"
                                    x-show="results.length > 0 && search.length > 2">
                                    <template x-for="client in results" :key="client.id">
                                        <li class="list-group-item list-group-item-action bg-dark text-white cursor-pointer"
                                            @click="selectClient(client)">
                                            <span x-text="client.text"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Campo oculto com ID do cliente selecionado -->
                            <input type="hidden" name="existing_client_id" x-model="selectedId">

                            <small class="text-muted d-block mt-1">
                                Se o cliente já existe, selecione-o para renovar (evita duplicatas).
                            </small>
                        </div>



                        <div class="col-md-6 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control bg-secondary text-white border-0"
                                placeholder="Ex: +244 9xx xxx xxx" value="{{ old('whatsapp') }}" required>
                            @error('whatsapp')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serviço</label>
                            <select name="service_id" class="form-select bg-secondary text-white border-0" required>
                                <option value="">Selecione o serviço</option>
                                @foreach (\App\Models\Service::all() as $service)
                                    <option value="{{ $service->id }}"
                                        {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} ({{ $service->default_price }} Kz)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plano</label>
                            <select name="plan" class="form-select bg-secondary text-white border-0" required>
                                <option value="">Selecione o plano</option>
                                <option value="Básico" {{ old('plan') == 'Básico' ? 'selected' : '' }}>Básico</option>
                                <option value="Premium" {{ old('plan') == 'Premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('plan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor Pago (kz)</label>
                            <input type="number" step="0.01" name="value_paid"
                                class="form-control bg-secondary text-white border-0" placeholder="Ex: 2500.00"
                                value="{{ old('value_paid') }}" required>
                            @error('value_paid')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data de Início</label>
                            <input type="date" name="start_date" class="form-control bg-secondary text-white border-0"
                                value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observations" class="form-control bg-secondary text-white border-0" rows="3"
                            placeholder="Notas adicionais...">{{ old('observations') }}</textarea>
                        @error('observations')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">Cadastrar Cliente</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function clientSearch() {
            return {
                search: '',
                results: [],
                selectedId: null,

                async searchClients() {
                    if (this.search.length < 3) {
                        this.results = [];
                        return;
                    }

                    try {
                        const response = await fetch(`/clients/search-json?term=${encodeURIComponent(this.search)}`);
                        this.results = await response.json();
                    } catch (e) {
                        console.error('Erro na busca:', e);
                    }
                },

                selectClient(client) {
                    this.search = client.text;
                    this.selectedId = client.id;
                    this.results = [];
                    // Opcional: preencher outros campos automaticamente
                    // document.querySelector('[name="whatsapp"]').value = client.whatsapp || '';
                }
            }
        }
    </script>
@endsection
