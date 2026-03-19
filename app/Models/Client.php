<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name', 'whatsapp', 'service_id', 'plan', 'value_paid',
        'start_date', 'due_date', 'status', 'observations', 'deleted_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected $dates = ['deleted_at', 'start_date'];

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
            if (! $client->isForceDeleting()) {
                $client->deleted_reason = request()->input('deleted_reason', 'Motivo não informado');
                $client->saveQuietly(); // salva sem disparar eventos novamente
            }
        });
    }
}
