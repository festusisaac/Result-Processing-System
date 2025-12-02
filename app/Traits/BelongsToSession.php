<?php

namespace App\Traits;

use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSession
{
    protected static function bootBelongsToSession()
    {
        static::creating(function ($model) {
            if (empty($model->session_id)) {
                $model->session_id = AcademicSession::getActive()?->id;
            }
        });

        // Global scope to only show records from the active session by default
        static::addGlobalScope('session', function (Builder $builder) {
            $builder->where('session_id', AcademicSession::getActive()?->id);
        });
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}