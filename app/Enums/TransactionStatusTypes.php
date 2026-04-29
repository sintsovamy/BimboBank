<?php
namespace App\Enums;

enum TransactionStatusTypes: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        return match($this) {
            self::PENDING => 'В процессе',
            self::COMPLETED => 'Исполнен',
            self::FAILED => 'Неудачный',
            self::CANCELLED => 'Отмененный'
        };
    }

    /**
     * @return AccountTypes[]
     */
    public static function values(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
            self::FAILED,
            self::CANCELLED,
        ];
    }
}



