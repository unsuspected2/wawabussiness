<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'whatsapp', 'service', 'plan', 'value_paid',
        'start_date', 'due_date', 'status', 'observations',
        'deleted_reason'  // ← novo campo para motivo da exclusão
    ];

    protected $dates = ['deleted_at'];

    // Configuração do Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'whatsapp', 'service', 'plan', 'value_paid', 'start_date', 'due_date', 'status', 'observations', 'deleted_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('clientes');
    }

    // Atualiza status automaticamente (já tinha)
    protected function updateStatus()
    {
        if ($this->due_date && $this->due_date < now()->format('Y-m-d') && $this->status !== 'Cancelado') {
            $this->status = 'Vencido';
        }
    }

    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($client) {
            $client->updateStatus();
        });

        // Quando deletar (soft delete), registrar motivo e log
        static::deleting(function ($client) {
            if (!$client->isForceDeleting()) {
                $client->deleted_reason = request()->input('deleted_reason', 'Motivo não informado');
                $client->saveQuietly(); // salva sem disparar eventos novamente
            }
        });
    }
}
