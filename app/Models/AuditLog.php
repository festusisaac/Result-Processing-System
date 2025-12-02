<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['action', 'user_id', 'meta'];
    
    protected $casts = [
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $meta = [])
    {
        return static::create([
            'action' => $action,
            'user_id' => auth()->id(),
            'meta' => $meta
        ]);
    }

    public function getFormattedActionAttribute()
    {
        $user = $this->user ? $this->user->name : 'System';
        return "{$user} {$this->action}";
    }
}
