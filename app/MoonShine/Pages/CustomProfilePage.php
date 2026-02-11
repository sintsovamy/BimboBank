<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;


class CustomProfilePage extends ProfilePage
{
    /**
     * @return iterable
     */
    protected function fields(): iterable
    {
        $user = MoonShineAuth::getGuard()->user() ?? MoonShineAuth::getModel();
        $user->load('profile');

        $userFields = array_filter([
            ID::make(),

            Text::make('Имя', '')
                ->setValue(
                    $user->profile?->last_name . ' '
                    . $user->profile?->first_name . ' '
                    . $user->profile?->middle_name ?? ''
                )
                ->readonly(),

            Date::make('Дата рождения', 'profile.birthday')
                ->readonly(),

            Text::make('Телефон', 'profile.phone_number')
                ->readonly(),

            Text::make('Email', 'profile.email')
                ->readonly(),

            moonshineConfig()->getUserField('username')
                ? Text::make(__('moonshine::ui.login.username'), moonshineConfig()->getUserField('username'))
                ->required()
                : null,
        ]);

        $userPasswordsFields = moonshineConfig()->getUserField('password') ? [
            Heading::make(__('moonshine::ui.resource.change_password')),

            Password::make(__('moonshine::ui.resource.password'), moonshineConfig()->getUserField('password'))
                ->customAttributes(['autocomplete' => 'new-password'])
                ->eye(),

            PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                ->customAttributes(['autocomplete' => 'confirm-password'])
                ->eye(),
        ] : [];

        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), $userFields),
                    Tab::make(__('moonshine::ui.resource.password'), $userPasswordsFields)->canSee(
                        fn (): bool => $userPasswordsFields !== [],
                    ),
                ]),
            ]),
        ];
    }
}
