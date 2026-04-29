<?php

namespace App\Models;

use App\Enums\AccountStatuses;
use App\Enums\AccountTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

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
        'status',
        'opened_at'
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
     * @return HasMany
     */
    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }

    /**
     * @return Collection
     */
    protected function getAllTransactionsAttribute(): Collection
    {
        return $this->sentTransactions->concat($this->receivedTransactions);
    }

    /**
     * @return HasMany
     */
    public function latestTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_account_id')
            ->orWhere('destination_account_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->limit(3);
    }

    /**
     * @return HasMany
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class);
    }

    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function concurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
