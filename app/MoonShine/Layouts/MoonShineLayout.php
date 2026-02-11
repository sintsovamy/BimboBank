<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\ColorManager\Palettes\ValentinePalette;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\UI\Components\Layout\Sidebar;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var bool
     */
    protected bool $contentCentered = true;

    /**
     * @var bool
     */
    protected bool $sidebar = false;

    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = ValentinePalette::class;

    /**
     * @return array
     */
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    /**
     * @return string
     */
    protected function getFooterCopyright(): string
    {
        return 'Sintsova Marina';
    }

    /**
     * @return string[]
     */
    protected function getFooterMenu(): array
    {
        return [
            'http://t.me/sintsovamy' => 'Contact me',
        ];
    }

    protected function getProfileComponent(): Profile
    {
        return Profile::make()
            ->avatarPlaceholder(url('logo.png'));
    }
}
