<?php

use function Pest\Laravel\seed;
use Database\Seeders\RoleSeeder;
use Database\Seeders\CompanySeeder;
use App\Models\Company;

beforeEach(function() {
    seed(\Database\Seeders\AddressSeeder::class);
    seed(RoleSeeder::class);
    seed(CompanySeeder::class);
});

test('seller user can only see clients on his tenant', function(){
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = \App\Models\User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])->has(\App\Models\Seller::factory()->state(['company_id' => $company1->id]))->create();
    $user2 = \App\Models\User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])->has(\App\Models\Seller::factory()->state(['company_id' => $company2->id]))->create();

    \App\Models\Client::factory()
        ->count(10)
        ->create(['company_id' => $company1->id]);

    \App\Models\Client::factory()
        ->count(10)
        ->create(['company_id' => $company2->id]);

    $this->assertSame(20, \App\Models\Client::count());

    auth()->loginUsingId($user1->id);

    $this->assertSame(10, \App\Models\Client::count());
});

test('seller user can only see sellers on his tenant', function(){
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = \App\Models\User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])->has(\App\Models\Seller::factory()->state(['company_id' => $company1->id]))->create();
    $user2 = \App\Models\User::factory()->state(['role_id' => \App\Enums\RoleEnum::SELLER])->has(\App\Models\Seller::factory()->state(['company_id' => $company2->id]))->create();

    \App\Models\Seller::factory()
        ->count(10)
        ->create(['company_id' => $company1->id]);

    \App\Models\Seller::factory()
        ->count(10)
        ->create(['company_id' => $company2->id]);

    $this->assertSame(22, \App\Models\Seller::count());

    auth()->loginUsingId($user1->id);

    $this->assertSame(11, \App\Models\Seller::count());
});
