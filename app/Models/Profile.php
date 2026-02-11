<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'birthday',
        'passport_series_number',
        'passport_details',
        'address',
        'phone_number',
        'email'
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(MoonshineUser::class, 'user_id', 'id');
    }
}
