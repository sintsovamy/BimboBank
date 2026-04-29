<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;

#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return Collection
     */
    private function getAccounts(): Collection
    {
        $user = auth()->user();

        return $user->accounts()->with(['product', 'latestTransactions'])->get();
    }

    /**
     * @return array
     */
    private function getProductCollapses(): array
    {
        $collapses = [];
        $accounts = $this->getAccounts();

        foreach($accounts as $account) {
            $collapses[] = Collapse::make(fn() => $this->getProductLabel($account), [
                Preview::make('', 'balance',
                    fn () => number_format(
                    (float)$account->balance, decimals: 2, decimal_separator: ',', thousands_separator: ' ') . ' ₽'
                ),
                $this->getTransactionsTable($account)
            ])
                ->persist(false)
                ->open(false)
                ->icon($this->getProductIcon($account));
        }

        return $collapses;
    }

    /**
     * @param Account $account
     * @return TableBuilder
     */
    private function getTransactionsTable(Account $account): TableBuilder
    {
        return TableBuilder::make()
            ->fields([
                Text::make(
                    '',
                    '',
                    fn (Transaction $transaction) => $this->getTitleForTransaction($transaction))
                ->unescape()
            ])
            ->items($account->latestTransactions);
    }

    /**
     * @param Transaction $transaction
     * @return mixed|string
     *
     */
    private function getTitleForTransaction(Transaction $transaction): mixed
    {
        $action = $transaction->destination_account_id ? 'Перевод клиенту Bimbo Bank' :
            ($transaction->external_destination_bank ? 'Перевод в ' :
                ($transaction->source_account_id ? 'Поступление от клиента Bimbo Bank' :
                    ($transaction->external_source_bank ? 'Поступление из' :
                        ($transaction->merchant_name ? 'Покупка' : 'Операция')
                    )
                )
            );

        $name = $transaction->destination_account_id ? $this->getUserName($transaction->destinationAccount()->user) :
            ($transaction->external_destination_bank ? $transaction->external_destination_bank :
                ($transaction->source_account_id ? $this->getUserName($transaction->sourceAccount()->user) :
                    ($transaction->external_source_bank ? $transaction->external_source_bank :
                        ($transaction->merchant_name ? $transaction->merchant_name : '')
                    )
                )
            );

        return [
            'action' => $action,
            'name' => $name
        ];
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
     * @param $account
     * @return string
     */
    private function getProductLabel($account): string
    {
        return $account->product ?
            $account->product->title :
            $account->type->toString() . ' счет';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $account = $this->getAccounts()->first() ?? null;

		return [
            Box::make(
                'Информация о счетах',
                [
                   ...$this->getProductCollapses()
                ]
            )
        ];
	}
}
