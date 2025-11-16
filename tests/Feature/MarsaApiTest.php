<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\marsa;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MarsaApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'password' => Hash::make('password123')
        ]);
        $this->token = $this->authenticate($this->user);
    }

    /** @test */
    public function it_returns_all_marsas_when_authenticated()
    {
        factory(marsa::class, 3)->create();

        $response = $this->authenticatedJson('GET', '/api/marsa');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Marsas viewed successfully'
                 ])
                 ->assertJsonCount(3, 'marsas');
    }

    /** @test */
    public function it_creates_a_new_marsa_via_api()
    {
        $marsaData = [
            'name' => 'Test Marsa',
            'numberof' => '12345',
            'numbervat' => 'VAT123',
            'location' => 'Test Location',
            'space' => '1000'
        ];

        $response = $this->authenticatedJson('POST', '/api/marsa', $marsaData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'marsa created successfully'
                 ]);

        $this->assertDatabaseHas('marsas', [
            'name' => 'Test Marsa',
            'numbervat' => 'VAT123'
        ]);
    }
}
