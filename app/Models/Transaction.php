<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_account_id',
        'destination_account_id',
        'external_destination_type',
        'external_destination_value',
        'external_destination_bank',
        'external_destination_holder',
        'external_source_type',
        'external_source_value',
        'external_source_bank',
        'external_source_holder',
        'amount',
        'mcc_id',
        'currency_id',
        'fee',
        'transaction_type',
        'status',
    ];

    /**
     * @return BelongsTo
     */
    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    /**
     * @return BelongsTo
     */
    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }
}
