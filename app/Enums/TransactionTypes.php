<?php
namespace App\Enums;

enum TransactionTypes: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
    case PURCHASE = 'purchase';

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        return match($this) {
            self::INCOMING => 'Исходящий',
            self::OUTGOING => 'Входящий',
            self::PURCHASE => 'Покупка'
        };
    }

    /**
     * @return AccountTypes[]
     */
    public static function values(): array
    {
        return [
            self::INCOMING,
            self::OUTGOING,
            self::PURCHASE
        ];
    }
}



