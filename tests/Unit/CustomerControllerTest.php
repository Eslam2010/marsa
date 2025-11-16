<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\customercontroller;
use App\customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new customercontroller();
    }

    /** @test */
    public function it_returns_all_customers()
    {
        factory(customer::class, 3)->create();

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(' cutomers  read succesfully', $responseData['message']);
        $this->assertCount(3, $responseData['data']);
    }

    /** @test */
    public function it_stores_a_new_customer_with_valid_data()
    {
        $request = new \Illuminate\Http\Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nationalid' => '123456789',
            'phoneone' => '1234567890'
        ]);

        $response = $this->controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('customer created successfully', $responseData['message']);
        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function it_fails_to_store_customer_with_invalid_data()
    {
        $request = new \Illuminate\Http\Request([]);

        $response = $this->controller->store($request);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /** @test */
    public function it_fails_to_store_customer_with_duplicate_phone()
    {
        $existingCustomer = factory(customer::class)->create([
            'phoneone' => '1234567890'
        ]);

        $request = new \Illuminate\Http\Request([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'nationalid' => '987654321',
            'phoneone' => '1234567890'
        ]);

        $response = $this->controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('this number already taken', $responseData['message']);
    }

    /** @test */
    public function it_shows_a_specific_customer()
    {
        $customer = factory(customer::class)->create();

        $response = $this->controller->show($customer->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('customer view successfully', $responseData['message']);
        $this->assertEquals($customer->id, $responseData['Customer']['id']);
    }

    /** @test */
    public function it_returns_error_when_customer_not_found()
    {
        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('customer not found ! ', $responseData['message']);
    }

    /** @test */
    public function it_updates_a_customer()
    {
        $customer = factory(customer::class)->create([
            'name' => 'Old Name'
        ]);

        $request = new \Illuminate\Http\Request([
            'name' => 'New Name',
            'email' => $customer->email,
            'nationalid' => $customer->nationalid,
            'phoneone' => $customer->phoneone
        ]);

        $response = $this->controller->update($request, $customer->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('customer view successfully', $responseData['message']);
        $this->assertEquals('New Name', $responseData['Customer']['name']);
    }

    /** @test */
    public function it_deletes_a_customer()
    {
        $customer = factory(customer::class)->create();

        $response = $this->controller->destroy($customer->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('customer deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_nonexistent_customer()
    {
        $response = $this->controller->destroy(999);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('customer not found', $responseData['error']);
    }
}
