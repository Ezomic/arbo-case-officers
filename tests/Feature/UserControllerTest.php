<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

test('store sends the acting tenant_id to Identity', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Http::fake(['*/api/users' => Http::response(['id' => (string) Str::uuid()])]);

    $response = $this->post('/users', [
        'name' => 'New Doctor',
        'email' => 'doctor@acme.test',
        'user_type_id' => 'medical_doctor',
    ]);

    $response->assertRedirect(route('users.index'));

    Http::assertSent(function ($request) use ($user) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/api/users')
            && $request['tenant_id'] === $user->tenant_id;
    });
});

test('update sends the acting tenant_id so Identity can enforce isolation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $targetUuid = (string) Str::uuid();
    Http::fake(['*/api/users/*' => Http::response(['id' => $targetUuid])]);

    $response = $this->put("/users/{$targetUuid}", ['name' => 'Renamed']);

    $response->assertRedirect(route('users.index'));

    Http::assertSent(function ($request) use ($user) {
        return $request->method() === 'PUT' && $request['tenant_id'] === $user->tenant_id;
    });
});

test('destroy sends the acting tenant_id so Identity can enforce isolation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $targetUuid = (string) Str::uuid();
    Http::fake(['*/api/users/*' => Http::response([], 204)]);

    $response = $this->delete("/users/{$targetUuid}");

    $response->assertRedirect(route('users.index'));

    Http::assertSent(function ($request) use ($user) {
        return $request->method() === 'DELETE' && $request['tenant_id'] === $user->tenant_id;
    });
});
