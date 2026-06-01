<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Product;
use App\Models\Profile;
use App\Services\ProductCollapsesBuilder;
use App\Services\StatisticsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\NoReturn;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Apexcharts\Support\SeriesItem;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\Options;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    public function __construct(
        protected readonly StatisticsService $statisticsService,
        protected readonly ProductCollapsesBuilder $productCollapsesBuilder,
        CoreContract $core
    )
    {
        parent::__construct($core);
    }

    /**
     * @var Collection|null
     */
    private ?Collection $accountsCache = null;

    /**
     * @var array|null
     */
    private ?array $stats = null;

    public function getTitle(): string
    {
        return 'Главная';
    }

    /**
     * @return void
     */
    #[NoReturn]
    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->stats = Cache::remember('stat', now()->addHours(24), function() {
            return $this->statisticsService->getStats([
                'date_from' => now()->startOfMonth(),
                'date_to' => now()
            ]);
        });
    }


    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            Box::make([
                ActionButton::make('Перевести', '')
                    ->customAttributes([
                        'style' => 'background-color:#FF69B4; color: white;'
                    ])
                    ->inModal('Перевод',
                        components: [
                            Flex::make([
                                ActionButton::make('Между своими')
                                    ->customAttributes(['style' => 'background-color:#FF69B4; color: white;'])
                                    ->inModal('Перевод между своими счетами',
                                        components: [
                                            FormBuilder::make(route('bank.transfer'))
                                                ->async()
                                                ->fields([
                                                    Select::make('Счет списания', 'source_account_id')
                                                        ->options($this->getProductsForSelect())
                                                        ->onChangeMethod(
                                                            'removeSelectedAccount',
                                                            selector: '#receive-account-field',
                                                            callback: AsyncCallback::with(responseHandler: 'updateSelect')
                                                        ),
                                                    Div::make()->customAttributes([
                                                            'id' => 'receive-account-field'
                                                    ]),
                                                    Divider::make(),
                                                    Div::make()->customAttributes([
                                                        'id' => 'sum-field'
                                                    ]),
                                                ])
                                                ->customAttributes([
                                                    'class' => 'transaction-form'
                                                ])
                                                ->submit('Продолжить', ['style' => 'background-color:#FF69B4; color: white;'])
                                        ]
                                    )
                                    ->customAttributes(['class' => 'w-full aspect-square flex items-center justify-center text-center p-4']),
                                ActionButton::make('Клиенту банка')
                                    ->customAttributes(['style' => 'background-color:#FF69B4; color: white;'])
                                    ->inModal('Перевод клиенту внутри банка',
                                        components: [
                                            FormBuilder::make(route('bank.transfer'))
                                                ->async()
                                                ->fields([
                                                    Select::make('Счет списания', 'source_account_id')
                                                        ->options($this->getProductsForSelect(false)),
                                                    Text::make('Номер телефона', 'phone')
                                                        ->mask('+7 (999) 999-99-99')
                                                        ->onChangeMethod(
                                                            'getRecipientUser',
                                                            selector: '#receive-user-name',
                                                            callback: AsyncCallback::with(responseHandler: 'enableAmountField')
                                                        )
                                                        ->customAttributes(['id' => 'phone-field']),
                                                    Text::make('Номер карты', 'card_number')
                                                        ->mask('9999999999999999')
                                                        ->onChangeMethod(
                                                            'getRecipientUser',
                                                            selector: '#receive-user-name',
                                                            callback: AsyncCallback::with(responseHandler: 'enableAmountField')
                                                        )
                                                        ->customAttributes(['id' => 'card-number-field']),
                                                    Div::make([])
                                                        ->customAttributes(['id' => 'receive-user-name']),
                                                    Number::make('Введите сумму', 'amount')
                                                        ->disabled()
                                                        ->name('amount-field')
                                                        ->customAttributes(['id' => 'amount-field']),
                                                ])
                                                ->customAttributes(['class' => 'transaction-form'])
                                                ->submit('Продолжить', ['style' => 'background-color:#FF69B4; color: white;'])
                                        ]
                                    )
                                    ->customAttributes(['class' => 'w-full aspect-square flex items-center justify-center text-center p-4']),
                            ])->class('grid grid-cols-2 gap-4')
                        ]),
                Grid::make([
                    Column::make([
                        Div::make([
                            Collapse::make('Счета', [
                                ...$this->productCollapsesBuilder->getCollapses($this->getAccounts())
                            ])
                        ]),
                        LineChartMetric::make('Детализация по дням')
                            ->series([
                                SeriesItem::make('Расходы', [
                                    ...$this->stats['lineStat']['sentStat']
                                ])->color('#FF69B4'),
                                SeriesItem::make('Доходы', [
                                    ...$this->stats['lineStat']['receivedStat']
                                ])->color('#F6B8B8')
                            ]),
                    ], colSpan: 7, adaptiveColSpan: 7)
                        ->customAttributes(['class' => 'left-dashboard-column']),

                    Column::make([
                        ValueMetric::make('Самая популярная категория')
                            ->value($this->stats['metrics']['mostPopularCategory']),
                        ValueMetric::make('Доход за месяц')
                            ->value($this->stats['metrics']['receivedSum'])
                            ->valueFormat(fn(int $value): string => number_format($value, 2, ',', ' ')),
                        ValueMetric::make('Траты за месяц')
                            ->value($this->stats['metrics']['sentSum'])
                            ->valueFormat(fn(int $value): string => number_format($value, 2, ',', ' ')),
                        Box::make([
                            FormBuilder::make('', FormMethod::GET, [
                                DateRange::make('Период', 'period')
                                    ->fromTo('date_from', 'date_to'),
                                Select::make('Продукт', 'account_id')
                                    ->options(new Options($this->getProductsForSelect()))
                                    ->default('all')
                            ])
                                ->async('/bank/statistics')
                                ->asyncSelector(['.expenses-chart'])
                                ->submit('Показать', [
                                    'class' => 'w-full',
                                    'style' => 'background-color:#FF69B4; color: white;'
                                ]),

                            Div::make([
                                DonutChartMetric::make('Расходы категориям')
                                    ->colors(['#FFC0CB', '#FFB6C1', '#FF69B4', '#F6B8B8', '#F4B4C4', '#FC8EAC', '#E30B5C', '#CA2C92'])
                                    ->values([
                                        ...$this->stats['donutStat']
                                    ]),
                            ])->customAttributes(['class' => 'expenses-chart']),
                        ]),
                    ], colSpan: 5, adaptiveColSpan: 5),
                ])
            ])
        ];
    }

    /**
     * @return JsonResponse
     */
    #[AsyncMethod]
    public function getRecipientUser(): JsonResponse
    {
        if (isset(request()['_data']['phone'])) {
            $phone = preg_replace('/[^\d+]/', '', request()['_data']['phone']);
            $profile = Profile::with('user.accounts')
                ->where('phone_number', '=', $phone)
                ->first();

            $receiveAccount = $profile?->user->accounts->first();

            if ($profile && $receiveAccount) {
                return JsonResponse::make([
                    'found' => true,
                    'foundBy' => 'phone'
                ])
                    ->html((string)Div::make([
                        Preview::make('Получатель', 'receiver')
                            ->setValue($profile->nameInTransaction),
                        Hidden::make('receive_account_id')->setValue($receiveAccount->id)
                    ])->customAttributes(['id' => 'receive-user-name']));
            }
        }

        if (isset(request()['_data']['card_number'])) {
            $product = Product::query()
                ->where('card_number', '=', request()['_data']['card_number'])
                ->with('account.user.profile')
                ->first();

            $receiveAccount = $product->account;

            if ($product && $receiveAccount) {
                return JsonResponse::make([
                    'found' => true,
                    'foundBy' => 'phone'
                ])
                    ->html((string)Div::make([
                        Preview::make('Получатель', 'receiver')
                            ->setValue($product->account->user->profile->nameInTransaction),
                        Hidden::make('receive_account_id')->setValue($receiveAccount->id)
                    ])->customAttributes(['class' => 'receive-user-name']));
            }
        }

        return JsonResponse::make(['found' => false])
            ->html((string)Div::make([
                Preview::make('Получатель', 'receiver')
                    ->setValue('Получатель не найден. Проверьте данные.'),
            ])->customAttributes(['class' => 'receive-user-name']));
    }

    /**
     * @return JsonResponse
     */
    #[AsyncMethod]
    public function removeSelectedAccount(): JsonResponse
    {
        $removeId = (int)(request()['_data']['source_account_id']);
        $options = $this->getProductsForSelect(all: false, removeId: $removeId);

        return JsonResponse::make()
            ->html(
                (string) Select::make('Счет назначения', 'receive_account_id')
                    ->options($options)
                    ->onChangeMethod(
                        'enableSumField',
                        selector: '#sum-field',
                        callback: AsyncCallback::with(responseHandler: 'enableSumField')
                    ),
            );
    }

    /**
     * @return JsonResponse
     */
    #[AsyncMethod]
    public function enableSumField(): JsonResponse
    {
        return JsonResponse::make()
            ->html(
                (string) Number::make('Введите сумму', 'amount')
                    ->customAttributes(['id' => 'sum-field']),
            );
    }

    /**
     * @param int|null $removeId
     * @return Collection
     */
    private function getAccounts(int $removeId = null): Collection
    {
        if ($this->accountsCache === null) {
            $user = auth()->user();
            $this->accountsCache = $user->accounts()
                ->when($removeId, fn ($query) => $query->whereNot('id', $removeId))
                ->with(['product', 'latestTransactions'])
                ->orderBy('created_at')
                ->get();
        }

        return $this->accountsCache;
    }

    /**
     * @param bool $all
     * @param int|null $removeId
     * @return array
     */
    private function getProductsForSelect(bool $all = true, int $removeId = null): array
    {
        $products = [];

        if ($all) {
            $products[] = new Option(
                label: 'Все',
                value: 'all',
            );
        }

        foreach($this->getAccounts($removeId) as $account) {
            $products[] = new Option(
                label: $account->product->title,
                value: (string)$account->id,
            );
        }

        return $products;
    }
}
