<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $clients;

    public function __construct(Collection $clients)
    {
        $this->clients = $clients;
    }

    public function collection(): Collection
    {
        return $this->clients;
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
            $client->service?->name ?? '—',
            $client->plan,
            number_format($client->value_paid, 2, ',', '.') . ' Kz',
            $client->start_date ? $client->start_date->format('d/m/Y') : '-',
            $client->due_date   ? $client->due_date->format('d/m/Y')   : '-',
            $client->status . ($client->trashed() ? ' (Removido)' : ''),
            $client->observations ?? '-',
            $client->deleted_reason ?? '-',
            $client->created_at ? $client->created_at->format('d/m/Y H:i') : '-',
            $client->updated_at ? $client->updated_at->format('d/m/Y H:i') : '-',
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