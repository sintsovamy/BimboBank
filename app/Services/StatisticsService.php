<?php

namespace App\Services;

use Illuminate\Support\Collection;

class StatisticsService
{
    /**
     * @param array $dates
     * @param int|null $accountId
     * @return array
     */
   public function byDateGroupByAccounts(array $dates, int $accountId = null): array
   {
       $accountsWithTransactions = $this->accountsWithTransactions($dates, $accountId);

       $allTransactions = $this->getAllTransactions($accountsWithTransactions);

       return $this->calculateStats($allTransactions);
   }

    /**
     * @param Collection $transactions
     * @return array
     */
    private function calculateStats(Collection $transactions): array
    {
        $stat = [];

        foreach ($transactions as $transaction) {
            $amount = (int)abs($transaction->amount);

            if ($transaction->mcc) {
                $category = $this->getCategoryByMcc($transaction->mcc->code);
                $stat[$category] = ($stat[$category] ?? 0) + $amount;
            } else {
                $stat['Переводы'] = ($stat['Переводы'] ?? 0) + $amount;
            }
        }

        return $stat;
    }

    /**
     * @param int $mcc
     * @return int|string|void
     */
   private function getCategoryByMcc(int $mcc)
   {
       $categories = config('mcc_categories');

       foreach ($categories as $category => $codes) {
           if (isset($codes['range'])) {
               if ($mcc >= $codes['range'][0] && $mcc <= $codes['range'][1]) {
                   return $category;
               }
           }
           if (in_array($mcc, $codes['single'] ?? [])) {
               return $category;
           }
       }
   }

   private function getAllTransactions(Collection $accounts): Collection
   {
       $result = collect();

       foreach ($accounts as $account) {
           foreach ($account->sentTransactions as $transaction) {
               $result->push($transaction);
           }
       }

       return $result;
   }

   private function accountsWithTransactions(array $dates, int $accountId = null)
   {
       $user = auth('moonshine')->user();

       return $user
           ->accounts()
           ->when($accountId, fn ($query) => $query->where('id', '=', $accountId))
           ->with(['sentTransactions' => function ($query) use ($dates) {
               $query->whereBetween('created_at', [$dates['date_from'], $dates['date_to']])
                   ->with('mcc');
           }])
           ->get()
           ->filter(function ($account) {
               return $account->sentTransactions->isNotEmpty();
           });
   }
}
