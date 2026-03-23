<form method="POST" action="{{ $action }}" class="needs-validation" novalidate>
    @csrf @method($method ?? 'POST')

    <div class="row g-3">
        <!-- Cliente -->
        <div class="col-md-6">
            <label class="form-label text-white">Cliente *</label>
            <select name="client_id" id="client_id"
                class="form-select bg-dark text-white border-secondary @error('client_id') is-invalid @enderror"
                required>
                <option value="">Selecione...</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}"
                        {{ old('client_id', $perfilAlocado?->client_id ?? '') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->name }} (ID: {{ $cliente->id }})
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pagamento Vinculado -->
        <div class="col-md-6">
            <label class="form-label text-white">Pagamento Vinculado *</label>
            <select name="payment_id" id="payment_id"
                class="form-select bg-dark text-white border-secondary @error('payment_id') is-invalid @enderror"
                required>
                <option value="">Selecione cliente primeiro</option>
            </select>
            @error('payment_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Serviço -->
        <div class="col-md-6">
            <label class="form-label text-white">Serviço *</label>
            <select name="service_id"
                class="form-select bg-dark text-white border-secondary @error('service_id') is-invalid @enderror"
                required>
                <option value="">Selecione...</option>
                @foreach ($servicos as $servico)
                    <option value="{{ $servico->id }}"
                        {{ old('service_id', $perfilAlocado?->service_id ?? '') == $servico->id ? 'selected' : '' }}>
                        {{ $servico->name }}
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
            <select name="tipo_alocacao" id="tipo_alocacao"
                class="form-select bg-dark text-white border-secondary @error('tipo_alocacao') is-invalid @enderror"
                required>
                <option value="perfil"
                    {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? 'perfil') === 'perfil' ? 'selected' : '' }}>
                    Perfil (compartilhado)</option>
                <option value="pessoal"
                    {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? '') === 'pessoal' ? 'selected' : '' }}>
                    Conta Pessoal</option>
            </select>
            @error('tipo_alocacao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campos condicionais -->
        <div id="campos-perfil" class="col-12 mt-3"
            style="display: {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? 'perfil') === 'perfil' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nome do Perfil</label>
                    <input type="text" name="nome_perfil"
                        value="{{ old('nome_perfil', $perfilAlocado?->nome_perfil ?? '') }}"
                        class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Login Perfil</label>
                    <input type="text" name="login_perfil"
                        value="{{ old('login_perfil', $perfilAlocado?->login_perfil ?? '') }}"
                        class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Senha Perfil</label>
                    <input type="text" name="senha_perfil"
                        value="{{ old('senha_perfil', $perfilAlocado?->senha_perfil ?? '') }}"
                        class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
        </div>

        <div id="campos-pessoal" class="col-12 mt-3"
            style="display: {{ old('tipo_alocacao', $perfilAlocado?->tipo_alocacao ?? '') === 'pessoal' ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email da Conta</label>
                    <input type="email" name="email_conta"
                        value="{{ old('email_conta', $perfilAlocado?->email_conta ?? '') }}"
                        class="form-control bg-dark text-white border-secondary">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Senha da Conta</label>
                    <input type="text" name="senha_conta"
                        value="{{ old('senha_conta', $perfilAlocado?->senha_conta ?? '') }}"
                        class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label text-white">Observações</label>
            <textarea name="observacao" rows="3" class="form-control bg-dark text-white border-secondary">{{ old('observacao', $perfilAlocado?->observacao ?? '') }}</textarea>
        </div>

        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-save me-2"></i> Salvar Perfil
            </button>
        </div>
    </div>
</form>
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
                    select.append('<option value="" disabled>Sem pagamentos registados para este cliente</option>');
                } else {
                    $.each(data, function(i, p) {
                        let venc = p.new_due_date ? ' (Vence: ' + p.new_due_date + ')' : '';
                        
                        // Sem status → removemos completamente esta parte
                        select.append(
                            `<option value="${p.id}">
                                ID #${p.id} - ${Number(p.amount).toLocaleString('pt-AO', {minimumFractionDigits: 2, maximumFractionDigits: 2})} Kz${venc}
                            </option>`
                        );
                    });
                }

                // Restaurar valor antigo (de old() ou edit)
                const oldPaymentId = '{{ old("payment_id") }}' || '{{ $perfilAlocado?->payment_id ?? '' }}';
                if (oldPaymentId) {
                    select.val(oldPaymentId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX pagamentos:', status, error, xhr.responseText);
                select.html('<option value="">Erro ao carregar pagamentos</option>');
            }
        });
    }

    $('#client_id').on('change', function() {
        loadPagamentos(this.value);
    });

    // Carrega automaticamente no edit ou após falha de validação
    const initialClientId = $('#client_id').val();
    if (initialClientId) {
        loadPagamentos(initialClientId);
    }
});
</script>
