<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatisticsService
{
    /**
     * @param array $dates
     * @return array
     */
   public function byDateGroupByAccounts(array $dates): array
   {
       $accountsWithTransactions = $this->accountsWithTransactions($dates);

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
            if ($transaction->mcc) {
                $category = $this->getCategoryByMcc($transaction->mcc->code);

                if (!isset($stat[$category])) {
                    $stat[$category] = 0;
                }
                $stat[$category]++;
            } else {
                if (!isset($stat['Переводы'])) {
                    $stat['Переводы'] = 0;
                }
                $stat['Переводы']++;
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

   private function accountsWithTransactions(array $dates)
   {
       $user = auth('moonshine')->user();
       $dateFrom = Carbon::parse($dates['date_from'])->startOfDay();
       $dateTo = Carbon::parse($dates['date_to'])->endOfDay();

       return $user
           ->accounts()
           ->with(['sentTransactions' => function ($query) use ($dateFrom, $dateTo) {
               $query->whereBetween('created_at', [$dateFrom, $dateTo])
                   ->with('mcc');
           }])
           ->get()
           ->filter(function ($account) {
               return $account->sentTransactions->isNotEmpty();
           });
   }
}
