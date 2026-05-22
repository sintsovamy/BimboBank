<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StatisticsRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'period.date_from' => 'nullable|date',
            'period.date_to' => 'nullable|date|after_or_equal:period.date_from',
            'account_id' => 'nullable|string'
        ];
    }

    /**
     * @return int|null
     */
    public function getAccountId(): ?int
    {
        $accountId = $this->validated()['account_id'] ?? null;

        return $accountId === 'all' ? null : (int) $accountId;
    }

    /**
     * @return Carbon
     */
    public function getDateFrom(): Carbon
    {
        return Carbon::parse(
            $this->input('period.date_from') ?? auth('moonshine')->user()->created_at
        )->startOfDay();
    }

    /**
     * @return Carbon
     */
    public function getDateTo(): Carbon
    {
        return Carbon::parse(
            $this->input('period.date_to') ?? now()
        )->endOfDay();
    }
}
