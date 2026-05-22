<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class AuthEventLogger
{
    /**
     * @param Login $event
     * @return void
     */
    public function handleUserLogin(Login $event): void
    {
        Log::channel('auth')->info('User login', [
            'user_id' => $event->user->id,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * @param Logout $event
     * @return void
     */
    public function handleUserLogout(Logout $event): void
    {
        Log::channel('auth')->info('User logout', [
            'user_id' => $event->user->id,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * @param Failed $event
     * @return void
     */
    public function handleFailedLogout(Failed $event): void
    {
        Log::channel('auth')->info('Failed login attempt', [
            'login' => $event->credentials['email'] ?? $event->credentials['login'] ?? null,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * @param $events
     * @return void
     */
    public function subscribe($events): void
    {
        $events->listen(
            Login::class,
            [self::class, 'handleUserLogin']
        );

        $events->listen(
            Logout::class,
            [self::class, 'handleUserLogout']
        );

        $events->listen(
            Failed::class,
            [self::class, 'handleFailedLogin']
        );
    }
}
