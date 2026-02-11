<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MoonShine\Laravel\Models\MoonshineUser as User;

class MoonshineUser extends User
{
    /**
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }
}
