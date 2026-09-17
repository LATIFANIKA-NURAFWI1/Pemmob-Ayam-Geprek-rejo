<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ── Windows-safe Filesystem ─────────────────────────────────────────
        // Di Windows, PHP rename() dari .tmp → .php sering gagal "Access denied"
        // akibat Windows Defender real-time scan. Override put() agar fallback
        // ke file_put_contents() jika rename() gagal.
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->singleton('files', function () {
                return new class extends Filesystem {
                    public function put($path, $contents, $lock = false)
                    {
                        if ($lock) {
                            return file_put_contents($path, $contents, LOCK_EX);
                        }
                        $tmpPath = $path . '.' . uniqid('', true) . '.tmp';
                        file_put_contents($tmpPath, $contents);
                        try {
                            // Hapus target jika ada (Windows butuh ini sebelum rename)
                            if (file_exists($path)) {
                                @unlink($path);
                            }
                            rename($tmpPath, $path);
                        } catch (\Throwable) {
                            // Fallback: copy langsung tanpa rename
                            @copy($tmpPath, $path);
                            @unlink($tmpPath);
                        }
                        return strlen($contents);
                    }
                };
            });
        }

        // ── Service Layer Bindings ──────────────────────────────────────────
        // Semua Service di-bind sebagai singleton agar state & dependency
        // hanya di-resolve sekali per request cycle.
        $this->app->singleton(\App\Services\StockService::class);
        $this->app->singleton(\App\Services\HPPService::class);
        $this->app->singleton(\App\Services\PointService::class);
        $this->app->singleton(\App\Services\VoucherService::class);
        $this->app->singleton(\App\Services\ExpenseService::class);
        $this->app->singleton(\App\Services\ProfitLossService::class);

        // OrderService memiliki dependency ke services lain — Laravel
        // akan otomatis resolve via constructor injection.
        $this->app->singleton(\App\Services\OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
