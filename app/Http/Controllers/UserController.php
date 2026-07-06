<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IdentityClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request, IdentityClient $identity): Response
    {
        /** @var User $user */
        $user = $request->user();

        $users = rescue(
            fn () => $identity->getUsers($user->tenant_id, ['arbo', 'medical_doctor']),
            [],
        );

        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request, IdentityClient $identity): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'user_type_id' => ['required', 'string', 'in:arbo,medical_doctor'],
        ]);

        $created = $identity->createUser($user->tenant_id, $data['name'], $data['email'], $data['user_type_id']);

        return to_route('users.index')->with('temporaryPassword', $created['temporary_password'] ?? null);
    }

    public function update(Request $request, string $uuid, IdentityClient $identity): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email'],
        ]);

        $identity->updateUser($user->tenant_id, $uuid, $data);

        return to_route('users.index');
    }

    public function destroy(Request $request, string $uuid, IdentityClient $identity): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $identity->deleteUser($user->tenant_id, $uuid);

        return to_route('users.index');
    }
}
