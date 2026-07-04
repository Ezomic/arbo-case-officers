<?php

namespace App\Providers;

use App\Models\User;
use App\Services\RolePermissionSyncService;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureGates();
    }

    protected function configureGates(): void
    {
        $permissions = [
            'view-employers' => 'View employers',
            'manage-employers' => 'Manage employers',
            'view-employees' => 'View employees',
            'manage-employees' => 'Manage employees',
            'view-cases' => 'View cases',
            'manage-cases' => 'Manage cases',
            'close-cases' => 'Close cases',
            'view-contracts' => 'View contracts',
            'manage-contracts' => 'Manage contracts',
        ];

        foreach ($permissions as $ability => $permissionName) {
            Gate::define($ability, function (User $user) use ($permissionName) {
                if ($user->tenant_id === null || $user->current_role === null) {
                    return false;
                }

                $sync = app(RolePermissionSyncService::class);

                return $sync->permissionsFor($user->tenant_id, $user->current_role)->contains($permissionName);
            });
        }
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api-client', function (Request $request) {
            return Limit::perMinute(60)->by($request->bearerToken() ?? $request->ip());
        });
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
