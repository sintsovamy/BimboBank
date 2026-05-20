<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Account;
use App\Services\TransferService;
use Illuminate\Validation\ValidationException;
use MoonShine\Crud\JsonResponse;
use MoonShine\Support\Enums\ToastType;

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
    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $this->transferService->transfer(
                source: Account::findOrFail($request->input('source_account_id')),
                receive: Account::findOrFail($request->input('receive_account_id')),
                amount: $request->input('amount')
            );

            return JsonResponse::make()
                ->redirect('/')
                ->toast('Перевод выполнен', ToastType::SUCCESS);

        } catch (ValidationException $e) {
            return JsonResponse::make()->toast($e->getMessage(), ToastType::ERROR);
        }
    }
}
