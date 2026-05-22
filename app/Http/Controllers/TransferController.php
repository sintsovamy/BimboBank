<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Account;
use App\Services\TransferService;
use MoonShine\Crud\JsonResponse;
use MoonShine\Support\Enums\ToastType;
use Throwable;

class TransferController extends Controller
{
    public function __construct(
        protected readonly TransferService $transferService
    )
    {
    }

    /**
     * @param TransferRequest $request
     * @return JsonResponse
     */
    public function __invoke(TransferRequest $request): JsonResponse
    {
        try {
            $this->transferService->transfer(
                source: Account::query()->findOrFail($request->input('source_account_id')),
                receive: Account::query()->findOrFail($request->input('receive_account_id')),
                amount: $request->input('amount')
            );

            return JsonResponse::make()
                ->redirect('/')
                ->toast('Перевод выполнен', ToastType::SUCCESS);

        } catch (Throwable $e) {
            return JsonResponse::make()->toast($e->getMessage(), ToastType::ERROR);
        }
    }
}
