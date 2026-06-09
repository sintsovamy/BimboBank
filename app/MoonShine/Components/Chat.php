<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use Closure;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class Chat extends MoonShineComponent
{
    protected string $view = 'admin.components.chat';

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'messages' => auth('moonshine')->user()->messages ?? []
        ];
    }
}
