<?php
namespace App\Enums;

enum AccountStatuses: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';
    case FROZEN = 'frozen';
    case CLOSING = 'closing';
    case CLOSED = 'closed';

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        return match($this) {
            self::ACTIVE => 'Активный',
            self::INACTIVE => 'Неактивный',
            self::BLOCKED => 'Заблокированный',
            self::FROZEN => 'Замороженный',
            self::CLOSING => 'К закрытию',
            self::CLOSED => 'Закрыт'
        };
    }

    /**
     * @return AccountStatuses[]
     */
    public static function values(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
            self::BLOCKED,
            self::CLOSED,
            self::FROZEN,
            self::CLOSING
        ];
    }
}



