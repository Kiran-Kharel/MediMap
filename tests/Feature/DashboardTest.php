<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolePermissionSeeder;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
  $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->create();
    $user->assignRole(RoleName::CUSTOMER->value);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('customer.dashboard'));
    }
}