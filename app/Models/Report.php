<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'resident_id',
        'type',
        'description',
        'latitude',
        'longitude',
        'status',
        'official_response',
        'responded_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'responded_at' => 'datetime',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function photos()
    {
        return $this->hasMany(ReportPhoto::class);
    }
}