<?php

namespace AstridTechnology\LaravelBackup\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;


class AstridBackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../views', 'astrid');
        $this->defineGates();
        $this->publishes([__DIR__ . '/../config/projectbackup.php' => config_path('projectbackup.php')], 'config');
    }

    /**
     * Define the authorization gates.
     *
     * @return void
     */
    protected function defineGates()
    {
        Gate::define('access-backup', function ($user) {
            return in_array($user->email, config('projectbackup.authorized_emails'));
        });
    }
}
