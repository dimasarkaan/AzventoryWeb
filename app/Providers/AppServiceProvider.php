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

        // Security Logging: Failed Login
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            \App\Models\ActivityLog::create([
                'user_id' => $event->user ? $event->user->id : null,
                'action' => 'Gagal Login',
                'description' => "Upaya login gagal untuk identitas: " . ($event->credentials['login'] ?? ($event->credentials['email'] ?? ($event->credentials['username'] ?? 'Tidak diketahui'))),
                'properties' => [
                    'ip' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]
            ]);
        });

        // Security Logging: Lockout
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Lockout::class, function ($event) {
            \App\Models\ActivityLog::create([
                'user_id' => null,
                'action' => 'Akun Terkunci',
                'description' => "Akun/IP terkunci sementara karena terlalu banyak percobaan login.",
                'properties' => [
                    'login' => $event->request->input('login'),
                    'ip' => $event->request->ip(),
                ]
            ]);
        });

        // Ultimate Reset Password Email (Indonesian, Personalized, Secure)
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            $fullName = $notifiable->name;
            $firstName = explode(' ', $fullName)[0];

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('🔐 Atur Ulang Kata Sandi - ' . $firstName . ' | ' . config('app.name'))
                ->greeting('Halo, ' . $firstName . '!')
                ->line('Kami menerima permintaan untuk mereset kata sandi akun Azventory Anda. Keamanan akun Anda adalah prioritas kami.')
                ->action('Atur Ulang Kata Sandi', $resetUrl)
                ->line('Link atur ulang kata sandi ini akan kedaluwarsa dalam 60 menit.')
                ->line('**🛡️ Keamanan Informasi**: Link ini dibuat khusus untuk Anda dan hanya bisa digunakan satu kali. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.')
                ->line('Jika tombol di atas tidak berfungsi, silakan salin dan tempel link berikut ke browser Anda:')
                ->line($resetUrl)
                ->salutation('Salam Hangat,' . PHP_EOL . '**Tim Support Azventory**');
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
