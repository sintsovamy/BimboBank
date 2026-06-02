<?php

namespace App\Services;

use App\Enums\ProductTypes;
use App\Models\Account;
use Illuminate\Support\Collection;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\Preview;

class ProductCollapsesBuilder
{
    public function __construct(
        protected readonly TransactionTableBuilder $transactionTableBuilder
    )
    {
    }

    /**
     * @param Collection $accounts
     * @return array
     */
    public function getCollapses(Collection $accounts): array
    {
        $collapses = [];

        foreach($accounts as $account) {
            $collapses[] = Collapse::make(fn() => $this->getProductLabel($account), [
                Preview::make('', 'balance',
                    fn () => number_format(
                            (float)$account->balance, decimals: 2, decimal_separator: ',', thousands_separator: ' ') . ' ₽'
                ),
                $this->transactionTableBuilder->getTable($account, limit: 3),
                Grid::make([
                    Column::make([
                        ActionButton::make('Перейти', '/bank/account/' . $account->id)
                            ->customAttributes(['style' => 'background-color:#FF69B4; color: white;'])
                            ->async(selector: ['.left-dashboard-column'])
                    ], colSpan: 2, adaptiveColSpan: 2),
                ]),
            ])
                ->persist(false)
                ->open(false)
                ->icon($this->getProductIcon($account));
        }

        return $collapses;
    }

    /**
     * @param Collection $accounts
     * @param Account $selectedAccount
     * @return array
     */
    private function getProductsForSelect(Collection $accounts, Account $selectedAccount): array
    {
        $products = [];

        foreach($accounts as $account) {
            if($selectedAccount->id == $account->id) continue;

            $products[] = new Option(
                label: $account->product->title,
                value: (string)$account->id,
            );
        }

        return $products;
    }

    /**
     * @param $account
     * @return string
     */
    private function getProductIcon($account): string
    {
        return match ($account->product->type) {
            ProductTypes::CREDIT_CARD, ProductTypes::DEBIT_CARD => 'credit-card',
            default => 'banknotes',
        };
    }

    /**
     * @param $account
     * @return string
     */
    private function getProductLabel($account): string
    {
        return $account->product ?
            $account->product->title :
            $account->type->toString() . ' счет';
    }
}
