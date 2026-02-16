<?php
namespace App\Enums;

enum AccountTypes: string
{
    case CURRENT = 'current';
    case DEPOSIT = 'deposit';
    case CHECKING = 'checking';
    case CREDIT = 'credit';

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        return match($this) {
            self::CURRENT => 'Текущий',
            self::DEPOSIT => 'Депозитный',
            self::CHECKING => 'Расчетный',
            self::CREDIT => 'Кредитный'
        };
    }
}



