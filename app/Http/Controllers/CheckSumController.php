<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\CheckBalanceService;
use Illuminate\Http\Request;
use MoonShine\Crud\JsonResponse;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

class CheckSumController extends Controller
{
    public function __construct(
        protected readonly CheckBalanceService $checkBalanceService
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkBalance(Request $request): JsonResponse
    {
        $balanceIsEnough = $this->checkBalanceService->checkBalance(
            $request->integer('accountId'),
            $request->float('value')
        );

        $ownerAccount = Account::find($request->input('_data.owner_account'));

        if ($balanceIsEnough) {
            return JsonResponse::make()
                ->html(
                    FormBuilder::make('bank.transfer')
                        ->fields([
                            Text::make('Перевод на счёт', '')
                                ->setValue($ownerAccount->product->title),
                            Text::make('Сумма', '')
                                ->setValue($request->float('value')),
                        ])
                        ->customAttributes(['class' => 'transaction-form'])
                        ->submit('', [], button:
                            ActionButton::make(
                                'Перевести',
                                route('bank.make', ['accountId' => $request->integer('accountId')])
                            )->customAttributes(['class' => 'submit-button'])
                        )
                );
        }

        return JsonResponse::make()
            ->toast('Недостаточно средств на счёте', ToastType::ERROR);
    }
}
