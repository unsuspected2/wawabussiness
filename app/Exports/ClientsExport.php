<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $status;
    protected $service;

    public function __construct($status = null, $service = null)
    {
        $this->status = $status;
        $this->service = $service;
    }

    public function collection()
    {
        $query = Client::query()->withTrashed(); // Inclui deletados se necessário

        // Filtro por status
        if ($this->status == 'trashed') {
            $query->onlyTrashed();
        } elseif ($this->status) {
            $query->where('status', $this->status);
        }

        // Filtro por nome do serviço (usando relacionamento)
        if ($this->service) {
            $query->whereHas('service', function ($q) {
                $q->where('name', 'like', '%' . $this->service . '%');
            });
        }

        // Carrega o relacionamento para evitar N+1 no map()
        return $query->with('service')
                     ->orderBy('name', 'asc')
                     ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome Completo',
            'WhatsApp',
            'Serviço',           // ← continua como "Serviço", mas agora mostra o nome real
            'Plano',
            'Valor Pago (Kz)',
            'Início',
            'Vencimento',
            'Status',
            'Observações',
            'Motivo Exclusão',
            'Criado em',
            'Atualizado em',
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->whatsapp,
            
            // CORREÇÃO AQUI: usa o nome do serviço relacionado
            $client->service?->name ?? '—',
            
            $client->plan,
            number_format($client->value_paid, 2, ',', '.'),
            $client->start_date ? date('d/m/Y', strtotime($client->start_date)) : '-',
            $client->due_date ? date('d/m/Y', strtotime($client->due_date)) : '-',
            $client->status . ($client->trashed() ? ' (Removido)' : ''),
            $client->observations ?? '-',
            $client->deleted_reason ?? '-',
            $client->created_at->format('d/m/Y H:i'),
            $client->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF198754']], // Verde
        ]);

        $sheet->getStyle('A2:M' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF6C757D'],
                ],
            ],
        ]);

        // Largura automática das colunas
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}