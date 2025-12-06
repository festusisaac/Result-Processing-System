<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the trait.
     */
    public static function bootAuditable()
    {
        // Ignore if running in console (seeds, migrations) unless specifically wanted
        if (app()->runningInConsole()) {
            return;
        }

        static::created(function ($model) {
            $model->logAudit('Created', [], $model->toArray());
        });

        static::updated(function ($model) {
            // Get changed attributes
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            // Filter out timestamps and hidden fields
            $ignoredColumns = ['updated_at', 'created_at', 'password', 'remember_token'];
            
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $value) {
                if (in_array($key, $ignoredColumns)) {
                    continue;
                }
                
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $value;
            }

            if (!empty($newValues)) {
                $model->logAudit('Updated', $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('Deleted', $model->toArray(), []);
        });
    }

    /**
     * Helper to log the audit entry.
     */
    protected function logAudit($action, $oldValues = [], $newValues = [])
    {
        // Determine readable model name (e.g., "App\Models\Score" -> "Score")
        $modelName = class_basename($this);
        
        // Try to identify the record (e.g., Student Name or ID)
        $identifier = $this->id;
        if (isset($this->name)) {
            $identifier = "{$this->name} ({$this->id})";
        } elseif (isset($this->full_name)) {
            $identifier = "{$this->full_name} ({$this->id})";
        } elseif (isset($this->adm_no)) {
             $identifier = "{$this->adm_no} ({$this->id})";
        }

        $description = "{$action} {$modelName}: {$identifier}";
        
        // Optional: Custom description if model has method
        if (method_exists($this, 'getAuditDescription')) {
            $description = $this->getAuditDescription($action);
        }

        AuditLog::create([
            'action' => $description,
            'user_id' => Auth::check() ? Auth::id() : null,
            'meta' => [
                'model' => get_class($this),
                'model_id' => $this->id,
                'old' => $oldValues,
                'new' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);
    }
}
