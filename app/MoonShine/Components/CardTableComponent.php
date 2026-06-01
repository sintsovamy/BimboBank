<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use App\Models\Currency;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Text;

/**
 * @method static static make()
 */
final class CardTableComponent extends MoonShineComponent
{
    /**
     * @var string
     */
    protected string $view = 'admin.components.card-table-component';

    public function __construct()
    {
        parent::__construct();

        //
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'table' => TableBuilder::make([
                Text::make('title')
            ])->items(Currency::all())
        ];
    }
}
