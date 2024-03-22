<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\seed;
use Database\Seeders\RoleSeeder;
use Database\Seeders\CompanySeeder;

beforeEach(function() {
    seed(RoleSeeder::class);
    seed(CompanySeeder::class);
});

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])
        ->has(\App\Models\Seller::factory()
            ->state(['company_id' => \App\Models\Company::first()->id]))
        ->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});
