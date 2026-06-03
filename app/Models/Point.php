<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'resident_id',
        'awarded_by',
        'collection_task_id',
        'points',
        'remarks',
    ];

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function awardedBy()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    public function collectionTask()
    {
        return $this->belongsTo(CollectionTask::class);
    }
}