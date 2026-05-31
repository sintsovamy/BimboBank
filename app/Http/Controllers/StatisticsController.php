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
    public function __invoke(StatisticsRequest $request): JsonResponse
    {
        $donutChartData = $this->statisticsService->getStats(
            ['date_from' => $request->getDateFrom(), 'date_to' => $request->getDateTo()],
            $request->getAccountId()
        )['donutStat'];

        $html = (string) Div::make([
            DonutChartMetric::make('Расходы категориям')
                ->colors(['#FFC0CB', '#FFB6C1', '#FF69B4', '#F6B8B8', '#F4B4C4', '#FC8EAC', '#E30B5C', '#CA2C92'])
                ->values([
                    ...$donutChartData
                ]),
        ]);

        return JsonResponse::make()->html($html);
    }
}
