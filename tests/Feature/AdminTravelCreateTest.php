<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class AdminTravelCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_travel_creation(): void
    {
        $response = $this->postJson('/api/v1/admin/travels', [
            'name' => 'Travel',
            'description' => 'Travel description',
            'is_public' => true,
            'number_of_days' => 5
        ]);

        $response->assertStatus(401);
    }

    public function test_unauthorized_travel_creation(): void
    {
        $adminRole = Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->roles()->attach($adminRole->id);
        
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel',
            'description' => 'Travel description',
            'is_public' => true,
            'number_of_days' => 5
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_and_authenticated_but_parameter_validation_errors(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->roles()->attach($adminRole->id);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel',
        ]);

        $response->assertStatus(422);
    }

    public function test_authorized_and_authenticated_and_successful_travel_creation(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->roles()->attach($adminRole->id);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel',
            'description' => 'Travel description',
            'is_public' => true,
            'number_of_days' => 5
        ]);

        $response->assertStatus(201);
    }
}
