<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatisticsRequest;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use MoonShine\Apexcharts\Components\DonutChartMetric;
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
     * @return \MoonShine\Crud\JsonResponse
     */
    public function statistics(StatisticsRequest $request): \MoonShine\Crud\JsonResponse
    {
        $data = $this->statisticsService->byDateGroupByAccounts($request->validated()['period']);

        $html = (string) Div::make([
            DonutChartMetric::make('Расходы')
                ->values($data)
        ]);

        return \MoonShine\Crud\JsonResponse::make()->html($html);
    }
}
