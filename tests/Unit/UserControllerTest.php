<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\usercontroller;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new usercontroller();
        $this->user = factory(User::class)->create([
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
        Auth::login($this->user);
    }

    /** @test */
    public function it_updates_user_password()
    {
        $user = factory(User::class)->create();

        $request = new \Illuminate\Http\Request([
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'password' => 'newpassword123'
        ]);

        $response = $this->controller->updatePassword($request, $user->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('user updated successfully', $responseData['message']);
        $this->assertEquals('Updated Name', $responseData['user']['name']);
    }

    /** @test */
    public function it_updates_user_without_password()
    {
        $user = factory(User::class)->create([
            'name' => 'Old Name'
        ]);

        $request = new \Illuminate\Http\Request([
            'name' => 'New Name',
            'phone' => '1234567890'
        ]);

        $response = $this->controller->updatePassword($request, $user->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('user updated successfully', $responseData['message']);
        $this->assertEquals('New Name', $responseData['user']['name']);
    }

    /** @test */
    public function it_updates_user_as_admin()
    {
        $user = factory(User::class)->create([
            'name' => 'Old Name',
            'role' => 'user'
        ]);

        $request = new \Illuminate\Http\Request([
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'role' => 'admin',
            'newPassword' => 'newpassword123'
        ]);

        $response = $this->controller->update($request, $user->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('user updated successfully', $responseData['message']);
        $this->assertEquals('Updated Name', $responseData['user']['name']);
    }

    /** @test */
    public function it_deletes_a_user()
    {
        $user = factory(User::class)->create();

        $response = $this->controller->destroy($user->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('user deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_nonexistent_user()
    {
        $response = $this->controller->destroy(999);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('user not found', $responseData['error']);
    }

    /** @test */
    public function it_checks_password_correctly()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make('correctpassword')
        ]);
        Auth::login($user);

        $result = $this->controller->Check('correctpassword');
        $this->assertTrue($result);

        $result = $this->controller->Check('wrongpassword');
        $this->assertFalse($result);
    }
}
