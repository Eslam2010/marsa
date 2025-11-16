<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\marsacontroller;
use App\marsa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarsaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new marsacontroller();
    }

    /** @test */
    public function it_returns_all_marsas()
    {
        factory(marsa::class, 3)->create();

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Marsas viewed successfully', $responseData['message']);
        $this->assertCount(3, $responseData['marsas']);
    }

    /** @test */
    public function it_stores_a_new_marsa_with_valid_data()
    {
        $request = new \Illuminate\Http\Request([
            'name' => 'Test Marsa',
            'numberof' => '12345',
            'numbervat' => 'VAT123',
            'location' => 'Test Location',
            'space' => '1000'
        ]);

        $response = $this->controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('marsa created successfully', $responseData['message']);
        $this->assertDatabaseHas('marsas', [
            'name' => 'Test Marsa',
            'numbervat' => 'VAT123'
        ]);
    }

    /** @test */
    public function it_fails_to_store_marsa_with_invalid_data()
    {
        $request = new \Illuminate\Http\Request([]);

        $response = $this->controller->store($request);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /** @test */
    public function it_shows_a_specific_marsa()
    {
        $marsa = factory(marsa::class)->create();

        $response = $this->controller->show($marsa->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('marsa Viewed successfully', $responseData['message']);
        $this->assertEquals($marsa->id, $responseData['marsa']['id']);
    }

    /** @test */
    public function it_returns_error_when_marsa_not_found()
    {
        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('marsa not found ! ', $responseData['message']);
    }

    /** @test */
    public function it_updates_a_marsa()
    {
        $marsa = factory(marsa::class)->create([
            'name' => 'Old Name'
        ]);

        $request = new \Illuminate\Http\Request([
            'name' => 'New Name',
            'numberof' => $marsa->numberof,
            'numbervat' => $marsa->numbervat,
            'location' => $marsa->location,
            'space' => $marsa->space,
            'taxValue' => 15
        ]);

        $response = $this->controller->update($request, $marsa->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('marsa updated successfully', $responseData['message']);
        $this->assertEquals('New Name', $responseData['marsa']['name']);
    }

    /** @test */
    public function it_deletes_a_marsa()
    {
        $marsa = factory(marsa::class)->create();

        $response = $this->controller->destroy($marsa->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('marsa deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('marsas', ['id' => $marsa->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_nonexistent_marsa()
    {
        $response = $this->controller->destroy(999);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('marsa not found', $responseData['error']);
    }
}
