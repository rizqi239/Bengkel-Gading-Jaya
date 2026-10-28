<?php

namespace App\Providers;

use App\Database\NeonPostgresConnector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', NeonPostgresConnector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        view()->composer(['layouts.app', 'profile'], function ($view) {
            $logoPath = null;
            try {
                $logoPath = DB::table('site_settings')->where('name', 'logo_path')->value('value');
            } catch (\Throwable $e) {
                // Table might not exist yet during migration
            }
            $avatarPath = Auth::user()?->avatar_path;

            $view->with([
                'logoUrl' => $logoPath ? Storage::disk('public')->url($logoPath) : null,
                'avatarUrl' => $avatarPath
                    ? Storage::disk('public')->url($avatarPath)
                    : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop',
            ]);
        });
    }
}
