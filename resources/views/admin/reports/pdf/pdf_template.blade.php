<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Clientes - WawaBusiness</title>
    
    <!-- Estilos inline + Bootstrap CDN (sem caminhos locais) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #198754;
            padding-bottom: 10px;
        }
        h1 {
            color: #198754;
            margin: 0;
        }
        .filter-info {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #198754;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tfoot tr {
            background: #f4f4f4;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }
        .text-success { color: #198754 !important; }
    </style>
</head>
<body>

    <div class="header">
        <h1>WawaBusiness - Relatório de Clientes</h1>
        <div class="filter-info">
            Gerado em: {{ date('d/m/Y H:i') }} | 
            Filtros: 
            Status: {{ $filters['status'] ?? 'Todos' }} | 
            Serviço: {{ $filters['service'] ?? 'Todos' }} | 
            Período: {{ $filters['start_date'] ?? '—' }} a {{ $filters['end_date'] ?? '—' }}
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
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->whatsapp }}</td>
                    
                    <!-- CORREÇÃO AQUI: usa o relacionamento -->
                    <td>{{ $client->service?->name ?? '—' }}</td>
                    
                    <td>{{ $client->plan }}</td>
                    <td>{{ $client->due_date ? date('d/m/Y', strtotime($client->due_date)) : '—' }}</td>
                    <td>{{ number_format($client->value_paid, 2, ',', '.') }} Kz</td>
                    <td>
                        {{ $client->status }}
                        {{ $client->trashed() ? ' (Removido)' : '' }}
                    </td>
                </tr>
                @php $total += $client->value_paid; @endphp
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Nenhum cliente encontrado para os filtros selecionados.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td colspan="2">{{ number_format($total, 2, ',', '.') }} Kz</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        WawaBusiness - Sistema de Gestão Profissional • Gerado em {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>