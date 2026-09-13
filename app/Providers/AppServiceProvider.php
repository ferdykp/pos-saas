<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('platform-admin', fn (User $user) => $user->is_platform_admin === true);
        Gate::define('manage-finance', fn (User $user) => $user->role === 'admin' && $user->tenant_id !== null);

        // 1. Gate Fitur QRIS (Khusus Growth & Scale)
        Gate::define('feature-qris', function (User $user) {
            $plan = $user->tenant?->currentPlan();

            return $plan && in_array($plan->slug, ['growth', 'scale']);
        });

        // 2. Gate Fitur CRM / Pelanggan & Member (Khusus Growth & Scale)
        Gate::define('feature-crm', function (User $user) {
            $plan = $user->tenant?->currentPlan();

            return $plan && in_array($plan->slug, ['growth', 'scale']);
        });

        // 3. Gate Fitur Multi-Outlet / Cabang (Khusus Growth & Scale)
        Gate::define('feature-multi-outlet', function (User $user) {
            $plan = $user->tenant?->currentPlan();

            return $plan && in_array($plan->slug, ['growth', 'scale']);
        });

        // 4. Gate Fitur Analitik AI Eksklusif (Khusus Scale)
        Gate::define('feature-ai-analytics', function (User $user) {
            $plan = $user->tenant?->currentPlan();

            return $plan && $plan->slug === 'scale';
        });
    }
}
