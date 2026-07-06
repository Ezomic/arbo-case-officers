<?php

namespace App\Http\Middleware;

use App\Enums\CaseType;
use App\Models\CaseFile;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'sidebarOpenCases' => function () use ($request) {
                if (! $request->user()) {
                    return [];
                }

                return CaseFile::query()
                    ->where('status', 'open')
                    ->with('employee:id,first_name,last_name')
                    ->oldest('opened_at')
                    ->get(['id', 'employee_id', 'expected_return_date'])
                    ->map(fn (CaseFile $case) => [
                        'id' => $case->id,
                        'employee' => $case->employee
                            ? ['first_name' => $case->employee->first_name, 'last_name' => $case->employee->last_name]
                            : null,
                        'expected_return_date' => $case->expected_return_date?->toDateString(),
                    ])
                    ->all();
            },
            'sidebarCaseTypes' => array_map(
                fn (CaseType $t) => ['value' => $t->value, 'label' => $t->label()],
                CaseType::cases(),
            ),
        ];
    }
}
