<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Components\ChatButton;
use MoonShine\AssetManager\Js;
use MoonShine\ColorManager\Palettes\ValentinePalette;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Components\Layout\Locales;
use MoonShine\Crud\Components\Layout\Notifications;
use MoonShine\Laravel\Components\Layout\Profile;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\UI\Components\Breadcrumbs;
use MoonShine\UI\Components\Layout\Burger;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Layout\ThemeSwitcher;
use MoonShine\UI\Components\When;

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
            Js::make('js/enableAmountField.js'),
            Js::make('js/enableSumField.js'),
            Js::make('js/updateSelect.js')
        ];
    }

    /**
     * @return Header
     */
    protected function getHeaderComponent(): Header
    {
        return Header::make([
            Div::make(array_filter([
                $this->mobileMode || ! $this->sidebar ? null : Burger::make(),
            ]))->class('menu-burger'),
            Breadcrumbs::make(
                $this->getPage()->getBreadcrumbs(),
            )->prepend(
                $this->getHomeUrl(),
                label: 'На главную',
            ),
            $this->getSearchComponent(),
            ChatButton::make(),
            When::make(
                fn (): bool => $this->hasThemes() && ! $this->isAlwaysDark() && ($this->mobileMode || (! $this->sidebar && ! $this->topBar)),
                static fn (): array => [ThemeSwitcher::make(),],
            ),
            Locales::make(),
            When::make(
                fn (): bool => $this->isProfileEnabled(),
                fn (): array
                => [
                    Fragment::make([
                        $this->getProfileComponent(),
                    ])->name('profile'),
                ],
            ),
            When::make(
                fn (): bool => $this->isUseNotifications() && ($this->mobileMode || ! $this->sidebar),
                static fn (): array => [Notifications::make()],
            ),
        ]);
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);
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

    /**
     * @return Profile
     */
    protected function getProfileComponent(): Profile
    {
        $profile = auth('moonshine')->user()->profile;

        return Profile::make(
            nameOfUser: fn () => $profile->first_name
        )
            ->avatarPlaceholder(url('logo.png'));
    }
}
