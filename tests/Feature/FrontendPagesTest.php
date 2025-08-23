<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_page_can_be_accessed(): void
    {
        $response = $this->get('/events');
        $response->assertOk();
    }

    public function test_events_page_displays_published_events(): void
    {
        $user = User::factory()->create();
        
        // Create published and draft events
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Published Event',
            'status' => 'published'
        ]);
        
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Draft Event',
            'status' => 'draft'
        ]);

        Livewire::test(\App\Livewire\Events\EventsList::class)
            ->assertSee('Published Event')
            ->assertDontSee('Draft Event');
    }

    public function test_events_search_functionality(): void
    {
        $user = User::factory()->create();
        
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Laravel Conference',
            'status' => 'published'
        ]);
        
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'React Workshop',
            'status' => 'published'
        ]);

        Livewire::test(\App\Livewire\Events\EventsList::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Conference')
            ->assertDontSee('React Workshop');
    }

    public function test_events_category_filter(): void
    {
        $user = User::factory()->create();
        
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Tech Event',
            'category' => 'Technology',
            'status' => 'published'
        ]);
        
        Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'Art Event',
            'category' => 'Arts',
            'status' => 'published'
        ]);

        Livewire::test(\App\Livewire\Events\EventsList::class)
            ->set('category', 'Technology')
            ->assertSee('Tech Event')
            ->assertDontSee('Art Event');
    }

    public function test_account_page_requires_authentication(): void
    {
        $response = $this->get('/account');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_account_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/account');
        $response->assertOk();
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com'
        ]);
        
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\Account::class)
            ->set('name', 'Updated Name')
            ->set('email', 'updated@example.com')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertDispatched('profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password')
        ]);
        
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\Account::class)
            ->set('current_password', 'old-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertDispatched('password-updated');

        // Verify password was updated
        $user->refresh();
        $this->assertTrue(\Hash::check('new-password', $user->password));
    }

    public function test_profile_update_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\Account::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->call('updateProfile')
            ->assertHasErrors(['name', 'email']);
    }

    public function test_password_update_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Profile\Account::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'short')
            ->set('password_confirmation', 'different')
            ->call('updatePassword')
            ->assertHasErrors(['current_password', 'password']);
    }

    public function test_events_pagination(): void
    {
        $user = User::factory()->create();
        
        // Create more than 12 events (the pagination limit)
        Event::factory()->count(15)->create([
            'user_id' => $user->id,
            'status' => 'published'
        ]);

        Livewire::test(\App\Livewire\Events\EventsList::class)
            ->assertViewHas('events', function ($events) {
                return $events->count() === 12; // First page should have 12 items
            });
    }
}