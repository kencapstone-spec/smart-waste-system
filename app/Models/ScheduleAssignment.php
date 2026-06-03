<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleAssignment extends Model
{
    protected $fillable = [
        'schedule_id',
        'personnel_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function personnel()
    {
        return $this->belongsTo(User::class, 'personnel_id');
    }
}