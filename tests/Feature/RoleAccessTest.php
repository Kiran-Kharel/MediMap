<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect(route('login'));
    }

    public function test_public_registration_assigns_customer_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();

        $user = User::query()
            ->where('email', 'new-customer@example.test')
            ->firstOrFail();

        $this->assertTrue(
            $user->hasRole(RoleName::CUSTOMER->value)
        );
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->createOne([
            'email_verified_at' => now(),
        ]);

        $admin->assignRole(RoleName::ADMIN->value);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->createOne([
            'email_verified_at' => now(),
        ]);

        $customer->assignRole(RoleName::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_redirects_customer_to_customer_area(): void
    {
        $customer = User::factory()->createOne([
            'email_verified_at' => now(),
        ]);

        $customer->assignRole(RoleName::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertRedirect(route('customer.dashboard'));
    }

    public function test_suspended_user_is_logged_out(): void
    {
        $customer = User::factory()->createOne([
            'email_verified_at' => now(),
            'status' => UserStatus::SUSPENDED,
        ]);

        $customer->assignRole(RoleName::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $customer = User::factory()->unverified()->create();

        $customer->assignRole(RoleName::CUSTOMER->value);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }
}