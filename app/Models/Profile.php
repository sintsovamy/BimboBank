<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'patronymic',
        'gender',
        'birthday',
        'passport_series_number',
        'passport_details',
        'address',
        'phone_number',
        'email'
    ];

    /**
     * @return Attribute
     */
    protected function nameInTransaction(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>
                $attributes['first_name'] . ' ' .
                $attributes['patronymic'] . ' ' .
                mb_substr($attributes['last_name'], 0, 1) . '.'
        );
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class, 'user_id', 'id');
    }
}
