<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\OrganizationalUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class OrganizationalUnitApiController extends Controller
{
    /**
     * @return Collection<int, OrganizationalUnit>
     */
    public function index(Request $request, string $employer): Collection
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        $employerModel = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);

        return $employerModel->organizationalUnits()->withoutGlobalScope('tenant')->oldest()->get();
    }
}
