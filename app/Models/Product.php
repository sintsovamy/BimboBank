<?php

namespace App\Models;

use App\Enums\ProductTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'title',
        'type',
        'rate',
        'limit',
        'end_date'
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => ProductTypes::class,
            'end_date' => 'dateTime',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
