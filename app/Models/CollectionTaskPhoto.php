<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTaskPhoto extends Model
{
    protected $fillable = [
        'collection_task_id',
        'photo_path',
    ];

    public function collectionTask()
    {
        return $this->belongsTo(CollectionTask::class);
    }
}