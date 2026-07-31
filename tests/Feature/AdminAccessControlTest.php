<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_access_every_section_and_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $this->actingAs($admin)->get('/admin/destinos')->assertOk();
        $this->actingAs($admin)->get('/admin/eventos')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/users/create')->assertOk();
    }

    public function test_user_can_only_access_assigned_sections(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
            'admin_sections' => ['destinos'],
        ]);

        $this->actingAs($user)->get('/admin/destinos')->assertOk();
        $this->actingAs($user)->get('/admin/eventos')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_user_can_open_profile_to_change_own_password(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin/profile')->assertOk();
    }
}
