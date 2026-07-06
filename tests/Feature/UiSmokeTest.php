<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the cases index with sidebar shared props', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('cases.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('cases/Index')
            ->has('cases')
            ->has('sidebarOpenCases')
            ->has('sidebarCaseTypes'));
});

it('renders the absence dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('absence-dashboard/Index'));
});
