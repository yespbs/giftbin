<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions (since we're using RefreshDatabase)
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        // Create permissions
        $permissions = [
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage users',
            'access admin panel',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage users',
            'access admin panel',
        ]);

        $userRole->givePermissionTo([
            'view events',
            'create events',
            'edit events',
        ]);
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Welcome, ' . $admin->name);
    }

    public function test_admin_panel_shows_correct_stats(): void
    {
        // Create some users
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $regularUsers = User::factory(3)->create();
        foreach ($regularUsers as $user) {
            $user->assignRole('user');
        }
        
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();
        $response->assertSee('Total Users');
        $response->assertSee('4'); // 1 admin + 3 regular users
        $response->assertSee('Admin Users');
        $response->assertSee('1'); // 1 admin user
    }

    public function test_admin_navigation_link_visible_to_admin_only(): void
    {
        // Test regular user doesn't see admin link
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $response = $this->actingAs($user)->get('/account');
        $response->assertOk();
        $response->assertDontSee('Admin Panel');
        
        // Test admin sees admin link
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin)->get('/account');
        $response->assertOk();
        $response->assertSee('Admin Panel');
    }
}
