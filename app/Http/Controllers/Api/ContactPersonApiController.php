<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactPersonApiController extends Controller
{
    public function index(Request $request, string $employer): JsonResponse
    {
        $tenantId = $request->validate(['tenant_id' => ['required', 'uuid']])['tenant_id'];

        $employer = Employer::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->findOrFail($employer);

        return response()->json(
            $employer->contactPersons()->get(['id', 'employer_id', 'name', 'email', 'phone', 'job_title'])
        );
    }
}
