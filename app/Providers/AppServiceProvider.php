<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\Sparepart;
use App\Policies\SparepartPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Sparepart::class, SparepartPolicy::class);

        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        $this->app['translator']->addJsonPath(lang_path());

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
    }
}
