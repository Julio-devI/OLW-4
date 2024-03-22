<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\seed;
use Database\Seeders\RoleSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function() {
    seed(RoleSeeder::class);
    seed(CompanySeeder::class);
});

test('password can be updated', function () {
    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});
