<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use LogsActivity;

    protected $fillable = [
        'zone_id',
        'created_by',
        'title',
        'description',
        'frequency',
        'start_date',
        'end_date',
        'collection_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(ScheduleAssignment::class);
    }

    public function collectionTasks()
    {
        return $this->hasMany(CollectionTask::class);
    }
}
