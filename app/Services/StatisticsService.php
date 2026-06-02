<?php

namespace App\Services;

use App\Enums\TransactionRelation;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class StatisticsService
{
    /**
     * @var string|null
     */
    private ?string $popularCategory = null;

    /**
     * @var float|null
     */
    private ?float $sentTransactionsSum = 0.0;

    /**
     * @var float|null
     */
    private ?float $receivedTransactionsSum = 0.0;

    /**
     * @param array $dates
     * @param int|null $accountId
     * @return array
     */
    public function getStats(array $dates, int $accountId = null): array
    {
        $accountsWithTransactions = $this->getAccountWithTransactions($dates, $accountId);

        return [
            'donutStat' => $this->donutStat($accountsWithTransactions),
            'lineStat' => $this->lineStat($accountsWithTransactions, $dates),
            'metrics' => $this->metrics()
        ];
    }

    /**
     * @param $dates
     * @param $accountId
     * @return mixed
     */
    private function getAccountWithTransactions($dates, $accountId): mixed
    {
        $user = auth('moonshine')->user();

        return $user
            ->accounts()
            ->when($accountId, fn ($query) => $query->where('id', '=', $accountId))
            ->with(['sentTransactions' => function ($query) use ($dates) {
                $query->whereBetween('created_at', [$dates['date_from'], $dates['date_to']])
                    ->with('mcc');
            }])
            ->with(['receivedTransactions' => function ($query) use ($dates) {
                $query->whereBetween('created_at', [$dates['date_from'], $dates['date_to']]);
            }])
            ->get()
            ->filter(function ($account) {
                return $account->sentTransactions->isNotEmpty();
            });
    }

    /**
     * @return array
     */
    private function metrics(): array
    {
        return [
            'mostPopularCategory' => $this->popularCategory,
            'sentSum' => $this->sentTransactionsSum,
            'receivedSum' => $this->receivedTransactionsSum
        ];
    }

    /**
     * @param Collection $accountsWithTransactions
     * @return array
     **/
    public function donutStat(Collection $accountsWithTransactions): array
    {
        $allTransactions = $this->getAllTransactions($accountsWithTransactions);

        return $this->calculateDonutStats($allTransactions);
    }

    /**
     * @param Collection $accountsWithTransactions
     * @param array $dates
     * @return array
     */
    public function lineStat(Collection $accountsWithTransactions, array $dates): array
    {
        $sentStat = $this->calculateLineStats($accountsWithTransactions, $dates, TransactionRelation::SENT->value);
        $receivedStat = $this->calculateLineStats($accountsWithTransactions, $dates, TransactionRelation::RECEIVED->value);

        return [
            'sentStat' => $sentStat,
            'receivedStat' => $receivedStat
        ];
    }

    /**
     * @param Collection $accounts
     * @param array $dates
     * @param string $typeOfTransaction
     * @return array
     */
    private function calculateLineStats(Collection $accounts, array $dates, string $typeOfTransaction): array
    {
        $result = collect();
        $sum = 0;

        foreach ($accounts as $account) {
            foreach ($account->$typeOfTransaction as $transaction) {
                $result->push($transaction);
                $sum += $transaction->amount;
            }
        }

        $key = $typeOfTransaction . 'Sum';
        $this->$key = $sum;

        $groppedTransactions = $result->groupBy(function($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function($transactions) {
            return round($transactions->sum('amount'), 2);
        })->toArray();

        $period = CarbonPeriod::create($dates['date_from'], $dates['date_to']);

        $filled = [];
        foreach ($period as $date) {
            $filled[$date->format('Y-m-d')] = $groppedTransactions[$date->format('Y-m-d')] ?? 0;
        }

        return $filled;
    }

    /**
     * @param Collection $transactions
     * @return array
     */
    private function calculateDonutStats(Collection $transactions): array
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

        if (empty($stat)) {
            $stat[] = 0;
        }

        arsort($stat);
        $this->popularCategory = (string)array_key_first($stat);

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

    /**
     * @param Collection $accounts
     * @return Collection
     */
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
}
