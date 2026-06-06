<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description'];

    public function streets()
    {
        return $this->hasMany(Street::class);
    }
}
