<?php

namespace App\Services;

use App\Enums\TransactionStatusTypes;
use App\Enums\TransactionTypes;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferService
{
    public function transfer(Account $source, Account $receive, float $amount): void
    {
        DB::transaction(function () use ($source, $receive, $amount) {
            $source = Account::query()->where('id', $source->id)->lockForUpdate()->first();
            $receive = Account::query()->where('id', $receive->id)->lockForUpdate()->first();

            $this->checkAmountOnBalance($source, $amount);

            $source->balance -= $amount;
            $receive->balance += $amount;

            Transaction::query()
                ->create([
                    'source_account_id' => $source->id,
                    'destination_account_id' => $receive->id,
                    'amount' => $amount,
                    'currency_id' => 1,
                    'status' => TransactionStatusTypes::COMPLETED->value,
                    'completed_at' => now()
                ]);

            $source->save();
            $receive->save();
        });
    }

    /**
     * @throws ValidationException
     */
    public function checkAmountOnBalance(Account $sourceAccount, float $amount): void
    {
        if ($sourceAccount->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => sprintf('Недостаточно средств. Доступно: %s', $sourceAccount->balance)
            ]);
        }
    }
}
