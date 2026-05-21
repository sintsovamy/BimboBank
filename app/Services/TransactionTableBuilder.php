<?php

namespace App\Services;

use App\Enums\TransactionStatusTypes;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Preview;

class TransactionTableBuilder
{
    /**
     * @param Account $account
     * @param int|null $limit
     * @return TableBuilder
     */
    public function getTable(Account $account, int $limit = null): TableBuilder
    {
        $transactions = $limit ?
            $account->latestTransactions($limit)->get() :
            $account->allTransactions;

        return TableBuilder::make()
            ->simple()
            ->tdAttributes(fn(?DataWrapperContract $data, int $row, int $cell): array => match ($cell) {
                0 => ['style' => 'width: 200px;'],
                1 => ['style' => 'width: 150px;'],
                default => [],
            })
            ->fields([
                Preview::make(
                    '',
                    '',
                    function (Transaction $transaction) use ($account) {
                        return $this->getTitleForTransaction($transaction, $account);
                    }),
                Preview::make(
                    '',
                    '',
                    function (Transaction $transaction) use ($account) {
                        return $this->getSumOfOperation($transaction, $account);
                    }),
                Preview::make(
                    '',
                    '',
                    function (Transaction $transaction) use ($account) {
                        return $this->getOperationDateTime($transaction);
                    }),
            ])
            ->items($transactions);
    }

    /**
     * @param Transaction $transaction
     * @param Account $account
     * @return string
     */
    private function getSumOfOperation(Transaction $transaction, Account $account): string
    {
        $amount = number_format((float) $transaction->amount, 2, ',', ' ');
        $sign = $this->getSign($transaction, $account);

        $amount = $sign . $amount;

        $color = $this->colorAmountFromStatus($transaction);
        $icon = $this->iconFromStatus($transaction);

        return '<span style="font-size: 1.1rem; font-weight: bold; ' . $color . '">' . $amount . '</span> ' . $icon;
    }

    /**
     * @param Transaction $transaction
     * @param Account $account
     * @return string
     */
    private function getSign(Transaction $transaction, Account $account): string
    {
        if ($transaction->merchant_name ||
            $transaction->source_account_id == $account->id ||
            $transaction->external_destination_bank) {
            return '-';
        }

        if ($transaction->destination_account_id == $account->id ||
            $transaction->external_source_bank) {
            return '+';
        }

        return '';
    }

    /**
     * @param Transaction $transaction
     * @return string
     */
    private function iconFromStatus(Transaction $transaction): string
    {
        return $transaction->status === TransactionStatusTypes::PENDING->value
            ? view('components.icons.pending')->render()
            : '';
    }

    /**
     * @param Transaction $transaction
     * @return string
     */
    private function colorAmountFromStatus(Transaction $transaction): string
    {
        return $transaction->status === TransactionStatusTypes::CANCELLED->value ?
            'color: #9ca3af;' :
            '';
    }

    /**
     * @param Transaction $transaction
     * @param Account $currentAccount
     * @return string
     */
    private function getTitleForTransaction(Transaction $transaction, Account $currentAccount): string
    {
        $isSource = ($transaction->source_account_id == $currentAccount->id);
        $isDestination = ($transaction->destination_account_id == $currentAccount->id);

        $name = '';

        if ($transaction->merchant_name) {
            $title = 'Покупка';
            $name = $transaction->merchant_name;
        }
        elseif ($isSource && $transaction->destination_account_id) {
            $title = 'Перевод клиенту Bimbo Bank';
            $name = $transaction->destinationAccount->user->profile->nameInTransaction ?? 'Клиент';
        }
        elseif ($isDestination && $transaction->source_account_id) {
            $title = 'Поступление от клиента Bimbo Bank';
            $name = $transaction->sourceAccount->user->profile->nameInTransaction ?? 'Клиент';
        }
        elseif ($isSource && $transaction->external_destination_bank) {
            $title = 'Перевод в';
            $name = $transaction->external_destination_bank;
        }
        elseif ($isDestination && $transaction->external_source_bank) {
            $title = 'Поступление из';
            $name = $transaction->external_source_bank;
        }
        elseif ($transaction->source_account_id && $transaction->destination_account_id) {
            $title = 'Перевод между клиентами';
        }
        else {
            $title = 'Операция';
        }

        $color = $this->colorFromStatus($transaction);
        $reject = $color ? 'Отменено' : '';

        return '<div>'
            . $title . '<br><strong>' . $name . '</strong><br>'
            . '<span style="' . $color . '">' . $reject . '</span>'
            . '</div>';
    }

    /**
     * @param Transaction $transaction
     * @return string
     */
    private function colorFromStatus(Transaction $transaction): string
    {
        return $transaction->status === TransactionStatusTypes::CANCELLED->value ?
            'color: #ef4444;' :
            '';
    }

    /**
     * @param $account
     * @return string
     */
    private function getProductIcon($account): string
    {
        return $account->product ? 'credit-card' : 'banknotes';
    }

    /**
     * @param Transaction $transaction
     * @return string
     */
    private function getOperationDateTime(Transaction $transaction): string
    {
        return Carbon::parse($transaction->created_at)->translatedFormat('d F Y, H:i');
    }
}
