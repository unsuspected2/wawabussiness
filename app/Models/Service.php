<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Service extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'icon', 'default_price', 'description'];

        public function clients()
    {
        return $this->hasMany(Client::class);
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'icon', 'default_price', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('serviços')
            ->setDescriptionForEvent(fn(string $eventName) => "Serviço foi {$eventName}");
    }
}
