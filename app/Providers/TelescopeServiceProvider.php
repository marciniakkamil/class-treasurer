<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class TelescopeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        $this->app->singleton('telescope.enabled', function (): bool {
            // Enabled only when env/config flag is true and not in production.
            return (bool) config('telescope.enabled', false) && ! $this->app->environment('production');
        });

        if (! $this->app->make('telescope.enabled')) {
            return;
        }

        $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
    }

    public function boot(): void
    {
        if (! class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        if (! (bool) ($this->app['telescope.enabled'] ?? false)) {
            return;
        }

        // Local environment: allow always. Non-local: only authenticated admins.
        Telescope::auth(function ($request): bool {
            if ($this->app->environment('local')) {
                return true;
            }

            /** @var User|null $user */
            $user = $request->user();

            return $user !== null && method_exists($user, 'isAdmin') && $user->isAdmin();
        });
    }
}
