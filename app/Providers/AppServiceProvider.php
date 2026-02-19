<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\Sparepart;
use App\Policies\SparepartPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrasi service aplikasi.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap service aplikasi.
     */
    public function boot(): void
    {
        Gate::policy(Sparepart::class, SparepartPolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);

        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        $this->app['translator']->addJsonPath(lang_path());

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
        
        // Environment validation untuk production
        if (app()->environment('production')) {
            $this->validateCriticalEnvVariables();
        }
    }
    
    /**
     * Validasi environment variables kritis untuk production.
     */
    private function validateCriticalEnvVariables(): void
    {
        $required = [
            'APP_KEY' => 'Application encryption key',
            'DB_CONNECTION' => 'Database connection',
        ];
        
        foreach ($required as $env => $description) {
            if (empty(env($env))) {
                throw new \RuntimeException("Missing critical environment variable: {$env} ({$description})");
            }
        }
    }
}
