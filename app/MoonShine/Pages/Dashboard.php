<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Enums\TransactionStatusTypes;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Preview;

#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @var Collection|null
     */
    private ?Collection $accountsCache = null;

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
        if ($this->accountsCache === null) {
            $user = auth()->user();
            $this->accountsCache = $user->accounts()
                ->with(['product', 'latestTransactions'])
                ->get();
        }

        return $this->accountsCache;
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
            ->tdAttributes(fn(?DataWrapperContract $data, int $row, int $cell): array => match ($cell) {
                0 => ['style' => 'width: 300px;'],
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
            ->items($account->latestTransactions);
    }

    private function getOperationDateTime(Transaction $transaction): string
    {
        return Carbon::parse($transaction->created_at)->translatedFormat('d F Y, H:i');
    }

    private function getSumOfOperation(Transaction $transaction, Account $account): string
    {
        $amount = number_format((float) $transaction->amount, 2, ',', ' ');
        $sign = '';

        if ($transaction->merchant_name) {
            $sign = '-';
        } elseif ($transaction->source_account_id == $account->id) {
            $sign = '-';
        } elseif ($transaction->destination_account_id == $account->id) {
            $sign = '+';
        } elseif ($transaction->external_source_bank) {
            $sign = '+';
        } elseif ($transaction->external_destination_bank) {
            $sign = '-';
        }

        $amount = $sign . $amount;

        $color = $this->colorAmountFromStatus($transaction);
        $icon = $this->iconFromStatus($transaction);

        return '<span style="font-size: 1.1rem; font-weight: bold; ' . $color . '">' . $amount . '</span> ' . $icon;
    }

    private function iconFromStatus(Transaction $transaction): string
    {
        if ($transaction->status === TransactionStatusTypes::PENDING->value) {
            return '<svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 mr-1" style="color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2"/>
                    </svg>';
        }

        return '';
    }

    private function colorFromStatus(Transaction $transaction): string
    {
        return $transaction->status === TransactionStatusTypes::CANCELLED->value ?
            'color: #ef4444;' :
            '';
    }

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

    public function statisticsByDates($dateFrom, $dateTo)
    {
        dd([$dateFrom, $dateTo]);
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $account = $this->getAccounts()->first() ?? null;

		return [
            Box::make(
                [
                    Grid::make([
                        Column::make([
                            ...$this->getProductCollapses()
                        ], colSpan: 8, adaptiveColSpan: 8),

                        Column::make([
                            FormBuilder::make('', FormMethod::GET, [
                                DateRange::make('Период', 'period')
                                    ->fromTo('date_from', 'date_to')
                            ])
                                ->async('/bank/statistics')
                            ->submit(),
                            DonutChartMetric::make('Расходы')
                                ->values([
                                    'Direct' => 3250,
                                    'Organic' => 2100,
                                    'Social' => 1850,
                                    'Referral' => 1200,
                                ])
                                ->customAttributes(['id' => 'expenses-chart'])
                            ], colSpan: 4, adaptiveColSpan: 4),
                    ])
                ]
            )
        ];
	}
}
