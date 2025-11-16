<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\customer;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CustomerApiTest extends TestCase
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
    public function it_requires_authentication_to_access_customers()
    {
        $response = $this->getJson('/api/customer');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_all_customers_when_authenticated()
    {
        factory(customer::class, 3)->create();

        $response = $this->authenticatedJson('GET', '/api/customer');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'phoneone']
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => ' cutomers  read succesfully'
                 ]);
    }

    /** @test */
    public function it_creates_a_new_customer_via_api()
    {
        $customerData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nationalid' => '123456789',
            'phoneone' => '1234567890'
        ];

        $response = $this->authenticatedJson('POST', '/api/customer', $customerData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'customer created successfully'
                 ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_customer()
    {
        $response = $this->authenticatedJson('POST', '/api/customer', []);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false
                 ]);
    }

    /** @test */
    public function it_prevents_duplicate_phone_numbers()
    {
        $existingCustomer = factory(customer::class)->create([
            'phoneone' => '1234567890'
        ]);

        $customerData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'nationalid' => '987654321',
            'phoneone' => '1234567890'
        ];

        $response = $this->authenticatedJson('POST', '/api/customer', $customerData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'this number already taken'
                 ]);
    }

    /** @test */
    public function it_returns_a_specific_customer()
    {
        $customer = factory(customer::class)->create();

        $response = $this->authenticatedJson('GET', "/api/customer/{$customer->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'customer view successfully',
                     'Customer' => [
                         'id' => $customer->id,
                         'name' => $customer->name
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_customer()
    {
        $response = $this->authenticatedJson('GET', '/api/customer/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'customer not found ! '
                 ]);
    }

    /** @test */
    public function it_updates_an_existing_customer()
    {
        $customer = factory(customer::class)->create([
            'name' => 'Old Name'
        ]);

        $updateData = [
            'name' => 'New Name',
            'email' => $customer->email,
            'nationalid' => $customer->nationalid,
            'phoneone' => $customer->phoneone
        ];

        $response = $this->authenticatedJson('PUT', "/api/customer/{$customer->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'customer view successfully'
                 ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function it_deletes_a_customer()
    {
        $customer = factory(customer::class)->create();

        $response = $this->authenticatedJson('DELETE', "/api/customer/{$customer->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'customer deleted successfully'
                 ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id
        ]);
    }

    /** @test */
    public function it_handles_cors_headers()
    {
        $response = $this->authenticatedJson('GET', '/api/customer');

        $response->assertHeader('Access-Control-Allow-Origin', '*');
    }
}
