<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\MoonShine\Components\Chat;
use App\MoonShine\Layouts\MoonShineLayout;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;

class ChatPage extends Page
{
    protected ?string $layout = MoonShineLayout::class;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Поддержка';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
		return [
            Chat::make()
        ];
	}
}
