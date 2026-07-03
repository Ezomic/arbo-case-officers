<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ContractApiController extends Controller
{
    public function index(Request $request, string $employer): Collection
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        $employer = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);

        return $employer->contracts()->withoutGlobalScope('tenant')->get();
    }
}
