<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'receive_account_id' => 'required|exists:accounts,id',
            'source_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01'
        ];
    }
}
