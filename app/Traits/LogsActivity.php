<?php

namespace App\Traits;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Boot the LogsActivity trait for a model.
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logAction($model, 'created', $model->getAttributes());
        });

        static::updated(function ($model) {
            // getChanges() returns the attributes that were changed in the last save
            $changes = $model->getChanges();

            // Remove 'updated_at' from changes to avoid logging mere saves without data change
            unset($changes['updated_at']);

            if (! empty($changes)) {
                self::logAction($model, 'updated', $changes);
            }
        });

        static::deleted(function ($model) {
            self::logAction($model, 'deleted', $model->getAttributes());
        });
    }

    /**
     * Log the action to the system_logs table.
     */
    protected static function logAction($model, $action, $changes = [])
    {
        SystemLog::create([
            'user_id' => Auth::id(), // Will be null if triggered by CLI/System
            'action' => $action,
            'model_type' => class_basename(get_class($model)),
            'model_id' => $model->id ?? null,
            'changes' => $changes,
            'ip_address' => Request::ip(),
        ]);
    }
}
