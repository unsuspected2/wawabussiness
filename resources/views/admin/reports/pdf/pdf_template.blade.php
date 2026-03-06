<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Clientes - WawaBusiness</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #198754; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #198754; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
        .filter-info { font-size: 10px; color: #666; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>WawaBusiness - Relatório de Vendas</h1>
        <div class="filter-info">
            Gerado em: {{ date('d/m/Y H:i') }} |
            Filtros: Status: {{ $filters['status'] ?? 'Todos' }} |
            Serviço: {{ $filters['service'] ?? 'Todos' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>WhatsApp</th>
                <th>Serviço</th>
                <th>Plano</th>
                <th>Vencimento</th>
                <th>Valor Pago</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($clients as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>{{ $client->whatsapp }}</td>
                <td>{{ $client->service }}</td>
                <td>{{ $client->plan }}</td>
                <td>{{ date('d/m/Y', strtotime($client->due_date)) }}</td>
                <td>{{ number_format($client->value_paid, 2, ',', '.') }} Kz</td>
                <td>{{ $client->status }} {{ $client->trashed() ? '(Cancelado)' : '' }}</td>
            </tr>
            @php $total += $client->value_paid; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f4f4f4; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td colspan="2">{{ number_format($total, 2, ',', '.') }} Kz</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        WawaBusiness - Sistema de Gestão Profissional
    </div>
</body>
</html>
