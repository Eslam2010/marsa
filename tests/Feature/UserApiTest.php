<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        $this->token = $this->authenticate($this->user);
    }

    /** @test */
    public function it_updates_user_password_via_api()
    {
        $user = factory(User::class)->create();

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'password' => 'newpassword123'
        ];

        $response = $this->authenticatedJson('PUT', "/api/user/changePassword/{$user->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'user updated successfully'
                 ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_gets_authenticated_user_data()
    {
        $response = $this->authenticatedJson('GET', '/api/user/getUserData');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'code',
                     'data' => [
                         'user' => ['id', 'name', 'phone']
                     ]
                 ]);
    }
}
