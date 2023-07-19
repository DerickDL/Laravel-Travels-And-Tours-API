<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * feature test for login and access token issuance 
     */
    public function test_successful_login_return_valid_access_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    /**
     * feature for failed login
     */
    public function test_fail_login(): void
    {
        $response = $this->post('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }


}
