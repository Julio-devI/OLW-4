<?php

use App\Models\User;
use function Pest\Laravel\seed;
use Database\Seeders\RoleSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function() {
    seed(RoleSeeder::class);
    seed(CompanySeeder::class);
});

test('confirm password screen can be rendered', function () {
    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
