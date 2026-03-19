<?php

namespace App\Providers;

use App\Models\Sparepart;
use App\Policies\SparepartPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrasi service aplikasi.
     */
    public function register(): void
    {
        if (app()->isLocal() && class_exists(\App\Providers\TelescopeServiceProvider::class)) {
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap service aplikasi.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        Gate::policy(Sparepart::class, SparepartPolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        Gate::policy(\App\Models\StockLog::class, \App\Policies\StockLogPolicy::class);
        Gate::policy(\App\Models\Borrowing::class, \App\Policies\BorrowingPolicy::class);

        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        $this->app['translator']->addJsonPath(lang_path());

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());

        // Custom Reset Password Email (Indonesian)
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $resetUrl = url(config('app.url') . route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Atur Ulang Kata Sandi - ' . config('app.name'))
                ->greeting('Halo!')
                ->line('Anda menerima email ini karena kami menerima permintaan atur ulang kata sandi untuk akun Anda.')
                ->action('Atur Ulang Kata Sandi', $resetUrl)
                ->line('Link atur ulang kata sandi ini akan kedaluwarsa dalam 60 menit.')
                ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.')
                ->salutation('Salam,' . PHP_EOL . config('app.name'));
        });

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
