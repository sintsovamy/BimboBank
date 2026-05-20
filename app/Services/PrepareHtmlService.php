<?php

namespace App\Services;

use App\Models\Account;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Title;
use MoonShine\UI\Fields\Preview;

class PrepareHtmlService
{
    public function __construct(
        protected readonly TransactionTableBuilder $transactionTableBuilder
    )
    {
    }

    /**
     * @param Account $account
     * @return Column
     */
   public function prepareAccountHtml(Account $account): Column
   {
       $account->load(['product', 'sentTransactions', 'receivedTransactions']);

       return Column::make([
           ActionButton::make('Вернуться', '/bank'),

           Title::make(
               $account->product->type->toString() . ' ' . $account->product->title
           ),

           Preview::make('', '', function () use ($account) {
               return '<span>Номер счёта: </span>' . $account->account_number . '<br>' .
                   '<span>Процентная ставка: </span>' . $account->product->rate . '%' . '<br>' .
                   '<span>Лимит овердрафта: </span>' . $account->product->limit . '₽';
           }),

           $this->transactionTableBuilder->getTable($account),
       ]);
   }
}
