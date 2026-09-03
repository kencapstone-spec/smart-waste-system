<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description'];

    public const DEFAULT_ZONES = [
        'Centro',
        'Nabunturan',
        'Cadanoy',
        'YMCA',
        'Mahayahay',
        'Cahayag',
        'Acasia',
        'JBC',
        'Cogao',
        'Samco',
        'Toog',
        'Sambag',
        'Gutahit',
    ];

    public static function ensureDefaultZonesExist(): void
    {
        if (static::count() === 0) {
            foreach (static::DEFAULT_ZONES as $name) {
                static::firstOrCreate(['name' => $name]);
            }
        }
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
