<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerApiController extends Controller
{
    /**
     * Every lookup here re-checks tenant_id against the employer row itself
     * (via withoutGlobalScope + explicit where) rather than trusting the
     * caller — the calling app authenticates as itself, not as the tenant.
     */
    public function show(Request $request, string $employer): Employer
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        return Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);
    }
}
