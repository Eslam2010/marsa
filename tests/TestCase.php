<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create an authenticated user and return JWT token
     *
     * @param User|null $user
     * @return string
     */
    protected function authenticate(User $user = null)
    {
        if (!$user) {
            $user = factory(User::class)->create([
                'password' => bcrypt('password123')
            ]);
        }

        $token = JWTAuth::fromUser($user);
        
        return $token;
    }

    /**
     * Make an authenticated API request
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param User|null $user
     * @return \Illuminate\Testing\TestResponse
     */
    protected function authenticatedJson($method, $uri, array $data = [], User $user = null)
    {
        $token = $this->authenticate($user);
        
        return $this->json($method, $uri, $data, [
            'Authorization' => 'Bearer ' . $token
        ]);
    }
}
