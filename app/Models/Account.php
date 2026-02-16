<?php

namespace App\Models;

use App\Enums\AccountStatuses;
use App\Enums\AccountTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'currency_id',
        'account_number',
        'type',
        'balance',
        'status'
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => AccountTypes::class,
            'status' => AccountStatuses::class,
            'opened_at' => 'timestamp',
            'closed_at' => 'timestamp'
        ];
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class);
    }

    /**
     * @return BelongsTo
     */
    public function concurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
