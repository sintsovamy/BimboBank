<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\PrepareHtmlService;
use MoonShine\Crud\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        protected readonly PrepareHtmlService $prepareHtmlService
    )
    {
    }

    /**
     * @param Account $account
     * @return JsonResponse
     */
    public function __invoke(Account $account): JsonResponse
    {
        $leftColumn = $this->prepareHtmlService->prepareAccountHtml($account);

        return JsonResponse::make()->html($leftColumn);
    }
}
