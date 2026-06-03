<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionTask extends Model
{
    protected $fillable = [
        'schedule_id',
        'personnel_id',
        'collection_date',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
        ];
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function personnel()
    {
        return $this->belongsTo(User::class, 'personnel_id');
    }

    public function photos()
    {
        return $this->hasMany(CollectionTaskPhoto::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }
}