<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use LogsActivity;

    protected $fillable = [
        'resident_id',
        'type',
        'description',
        'status',
        'official_response',
        'responded_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
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
