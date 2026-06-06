<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }
}
