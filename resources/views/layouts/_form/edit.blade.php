<!-- resources/views/layouts/_form.blade.php  ou onde estiveres usando -->
<form method="POST" action="{{ $action }}" class="needs-validation" novalidate>
    @csrf
    @method($method ?? 'POST')

    <div class="row g-3">
        <!-- Cliente -->
        <div class="col-md-6">
            <label class="form-label text-white">Cliente *</label>
            <select name="client_id" id="client_id" class="form-select bg-dark text-white border-secondary @error('client_id') is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach ($clientes as $id => $nome)
                    <option value="{{ $id }}"
                            {{ old('client_id', $perfilAlocado?->client_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $nome }} (ID: {{ $id }})
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pagamento Vinculado (carregado via AJAX) -->
        <div class="col-md-6">
            <label class="form-label text-white">Pagamento Vinculado *</label>
            <select name="payment_id" id="payment_id" class="form-select bg-dark text-white border-secondary @error('payment_id') is-invalid @enderror" required>
                <option value="">@if(old('client_id') || $perfilAlocado?->client_id) Carregando... @else Selecione cliente primeiro @endif</option>
            </select>
            @error('payment_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Serviço -->
        <div class="col-md-6">
            <label class="form-label text-white">Serviço *</label>
            <select name="service_id" id="service_id" class="form-select bg-dark text-white border-secondary @error('service_id') is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach ($servicos as $id => $nome)
                    <option value="{{ $id }}"
                            {{ old('service_id', $perfilAlocado?->service_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $nome }}
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tipo de Alocação -->
        <div class="col-md-6">
            <label class="form-label text-white">Tipo de Alocação *</label>
            <select name="tipo_alocacao" id="tipo_alocacao" class="form-select bg-dark text-white border-secondary @error('tipo_alocacao') is-invalid @enderror" required>
                <option value="perfil"  {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? 'perfil') === 'perfil' ? 'selected' : '' }}>
                    Perfil (compartilhado)
                </option>
                <option value="pessoal" {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? '') === 'pessoal' ? 'selected' : '' }}>
                    Conta Pessoal
                </option>
            </select>
            @error('tipo_alocacao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campos condicionais (perfil) -->
        <div id="campos-perfil" class="col-12 mt-3"
             style="display: {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? 'perfil') === 'perfil' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-white">Nome do Perfil</label>
                    <input type="text" name="nome_perfil" value="{{ old('nome_perfil', $perfilAlocado?->nome_perfil ?? '') }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Login Perfil</label>
                    <input type="text" name="login_perfil" value="{{ old('login_perfil', $perfilAlocado?->login_perfil ?? '') }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Senha Perfil</label>
                    <input type="text" name="senha_perfil" value="{{ old('senha_perfil', $perfilAlocado?->senha_perfil ?? '') }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
        </div>

        <!-- Campos condicionais (pessoal) -->
        <div id="campos-pessoal" class="col-12 mt-3"
             style="display: {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? '') === 'pessoal' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white">Email da Conta</label>
                    <input type="email" name="email_conta" value="{{ old('email_conta', $perfilAlocado?->email_conta ?? '') }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white">Senha da Conta</label>
                    <input type="text" name="senha_conta" value="{{ old('senha_conta', $perfilAlocado?->senha_conta ?? '') }}"
                           class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="col-12">
            <label class="form-label text-white">Observações</label>
            <textarea name="observacao" rows="3" class="form-control bg-dark text-white border-secondary">
                {{ old('observacao', $perfilAlocado?->observacao ?? '') }}
            </textarea>
        </div>

        <!-- Botões -->
        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-success btn-lg px-5 me-2">
                <i class="fas fa-save me-2"></i> Salvar Perfil
            </button>
            <a href="{{ route('perfis-alocados.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                Cancelar
            </a>
        </div>
    </div>
</form>

<!-- Script para mostrar/esconder campos condicionais -->
<script>
document.getElementById('tipo_alocacao')?.addEventListener('change', function() {
    document.getElementById('campos-perfil').style.display  = this.value === 'perfil'  ? 'block' : 'none';
    document.getElementById('campos-pessoal').style.display = this.value === 'pessoal' ? 'block' : 'none';
});
</script>

<!-- Script AJAX para carregar pagamentos (coloca aqui ou no ficheiro principal se preferires) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    function loadPagamentos(clientId) {
        const select = $('#payment_id');
        
        if (!clientId) {
            select.html('<option value="">Selecione cliente primeiro</option>');
            return;
        }

        select.html('<option value="">Carregando...</option>');

        $.ajax({
            url: '{{ route("pagamentos.by-client") }}',
            type: 'GET',
            data: { client_id: clientId },
            dataType: 'json',
            success: function(data) {
                select.empty().append('<option value="">Selecione o pagamento...</option>');

                if (!data || data.length === 0) {
                    select.append('<option value="" disabled>Sem pagamentos registados</option>');
                } else {
                    $.each(data, function(i, p) {
                        let venc = p.new_due_date ? ' (Vence: ' + p.new_due_date + ')' : '';
                        select.append(
                            `<option value="${p.id}">
                                ID #${p.id} - ${Number(p.amount).toLocaleString('pt-AO', {minimumFractionDigits: 2})} Kz${venc}
                            </option>`
                        );
                    });
                }

                // Restaura valor antigo
                const oldPayment = '{{ old("payment_id") }}' || '{{ $perfilAlocado?->payment_id ?? '' }}';
                if (oldPayment) {
                    select.val(oldPayment);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX:', status, error, xhr.responseText);
                select.html('<option value="">Erro ao carregar</option>');
            }
        });
    }

    $('#client_id').on('change', function() {
        loadPagamentos(this.value);
    });

    // Carrega automaticamente se já tiver cliente (edit ou falha de validação)
    const initialClient = $('#client_id').val();
    if (initialClient) {
        loadPagamentos(initialClient);
    }
});
</script>