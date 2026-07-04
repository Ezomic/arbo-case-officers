<?php

namespace App\Http\Controllers;

use App\Enums\CaseType;
use App\Models\CaseFile;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AbsenceDashboardController extends Controller
{
    public function index(): Response
    {
        $tenantId = Auth::user()->tenant_id;
        $now = CarbonImmutable::now();

        $openCases = CaseFile::query()
            ->where('status', 'open')
            ->get(['id', 'case_type', 'employer_id', 'opened_at']);

        $closedThisYear = CaseFile::query()
            ->where('status', 'closed')
            ->whereYear('closed_at', $now->year)
            ->get(['id', 'opened_at', 'closed_at']);

        $avgDurationDays = $closedThisYear->isNotEmpty()
            ? (int) round($closedThisYear->avg(fn ($c) => $c->opened_at->diffInDays($c->closed_at)))
            : null;

        $openByType = $openCases
            ->groupBy(fn (CaseFile $case) => $case->case_type->value ?? 'unknown')
            ->map(fn ($group, $type) => [
                'case_type' => $type === 'unknown' ? 'Unknown' : CaseType::from($type)->label(),
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        $topEmployers = CaseFile::query()
            ->where('cases.status', 'open')
            ->join('employers', 'cases.employer_id', '=', 'employers.id')
            ->select('employers.id', 'employers.name', DB::raw('count(*) as open_count'))
            ->groupBy('employers.id', 'employers.name')
            ->orderByDesc('open_count')
            ->limit(10)
            ->get();

        $newCasesThisMonth = CaseFile::query()
            ->whereYear('opened_at', $now->year)
            ->whereMonth('opened_at', $now->month)
            ->count();

        $closedThisMonth = CaseFile::query()
            ->where('status', 'closed')
            ->whereYear('closed_at', $now->year)
            ->whereMonth('closed_at', $now->month)
            ->count();

        $overdueReturnDates = CaseFile::query()
            ->where('status', 'open')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<', $now->toDateString())
            ->with(['employee:id,first_name,last_name', 'employer:id,name'])
            ->oldest('expected_return_date')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'employee_name' => $c->employee->first_name.' '.$c->employee->last_name,
                'employer_name' => $c->employer->name,
                'expected_return_date' => $c->expected_return_date->toDateString(),
                'days_overdue' => (int) $c->expected_return_date->diffInDays($now),
            ]);

        return Inertia::render('absence-dashboard/Index', [
            'stats' => [
                'open_cases' => $openCases->count(),
                'new_this_month' => $newCasesThisMonth,
                'closed_this_month' => $closedThisMonth,
                'avg_duration_days' => $avgDurationDays,
            ],
            'openByType' => $openByType,
            'topEmployers' => $topEmployers,
            'overdueReturnDates' => $overdueReturnDates,
        ]);
    }
}
