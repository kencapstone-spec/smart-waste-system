<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'role',
        'status',
        'address',
        'zone_id',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(ScheduleAssignment::class, 'personnel_id');
    }

    public function collectionTasks()
    {
        return $this->hasMany(CollectionTask::class, 'personnel_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'resident_id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'resident_id');
    }

    public function awardedPoints()
    {
        return $this->hasMany(Point::class, 'awarded_by');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'barangay_official';
    }

    public function isPersonnel(): bool
    {
        return $this->role === 'personnel';
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
