<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatisticsRequest;
use App\Services\StatisticsService;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Crud\JsonResponse;
use MoonShine\UI\Components\Layout\Div;

class StatisticsController extends Controller
{
    public function __construct(
        protected readonly StatisticsService $statisticsService
    )
    {
    }

    /**
     * @param StatisticsRequest $request
     * @return JsonResponse
     */
    public function statistics(StatisticsRequest $request): JsonResponse
    {
        $data = $this->statisticsService->byDateGroupByAccounts(
            ['date_from' => $request->getDateFrom(), 'date_to' => $request->getDateTo()],
            $request->getAccountId()
        );

        $html = (string) Div::make([
            DonutChartMetric::make('Все расходы')
                ->values($data)
        ]);

        return JsonResponse::make()->html($html);
    }
}
