<?php
namespace App\Enums;

enum ProductTypes: string
{
    case CREDIT = 'credit';
    case DEPOSIT = 'deposit';
    case DEBIT_CARD = 'debit_card';
    case CREDIT_CARD = 'credit_card';

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        return match($this) {
            self::CREDIT => 'Кредит',
            self::DEPOSIT => 'Вклад',
            self::DEBIT_CARD => 'Дебетовая карта',
            self::CREDIT_CARD => 'Кредитная карта'
        };
    }

    /**
     * @return AccountTypes[]
     */
    public static function values(): array
    {
        return [
            self::CREDIT,
            self::DEPOSIT,
            self::DEBIT_CARD,
            self::CREDIT_CARD,
        ];
    }
}



