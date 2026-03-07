<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'client_id', 'user_id', 'amount', 'payment_date',
        'new_due_date', 'method', 'notes'
    ];

    protected $dates = ['payment_date', 'new_due_date'];

          protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'new_due_date' => 'date',
        ];
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Configuração do log (registra todas as mudanças)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_id', 'amount', 'payment_date', 'new_due_date', 'method', 'notes'])
            ->logOnlyDirty()           // só loga o que mudou
            ->dontSubmitEmptyLogs()
            ->useLogName('pagamentos')
            ->setDescriptionForEvent(fn(string $eventName) => "Pagamento foi {$eventName}");
    }
}
