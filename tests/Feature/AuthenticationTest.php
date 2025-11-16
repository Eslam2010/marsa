<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_unauthenticated_access_to_login_endpoint()
    {
        $response = $this->postJson('/api/user/login', [
            'phone' => '1234567890',
            'password' => 'password123'
        ]);

        // This will fail if user doesn't exist, but endpoint is accessible
        $response->assertStatus(200);
    }

    /** @test */
    public function it_logs_in_user_and_returns_token()
    {
        $user = factory(User::class)->create([
            'phone' => '1234567890',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/user/login', [
            'phone' => '1234567890',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'Token',
                     'role'
                 ])
                 ->assertJson([
                     'message' => 'login successfully'
                 ]);
    }

    /** @test */
    public function it_rejects_invalid_credentials()
    {
        $user = factory(User::class)->create([
            'phone' => '1234567890',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/user/login', [
            'phone' => '1234567890',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'invalid phone and password'
                 ]);
    }

    /** @test */
    public function it_validates_login_input()
    {
        $response = $this->postJson('/api/user/login', []);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'phone',
                     'password'
                 ]);
    }
}
