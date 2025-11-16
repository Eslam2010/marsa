<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\contractcontroller;
use App\necklase;
use App\customer;
use App\marsa;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ContractControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new contractcontroller();
        $this->user = factory(User::class)->create();
        Auth::login($this->user);
    }

    /** @test */
    public function it_returns_all_contracts()
    {
        factory(necklase::class, 3)->create();

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Contracts viewed successfully', $responseData['message']);
        $this->assertCount(3, $responseData['contracts']);
    }

    /** @test */
    public function it_stores_a_new_contract_with_valid_data()
    {
        $marsa = factory(marsa::class)->create();
        $customer = factory(customer::class)->create();

        $request = new \Illuminate\Http\Request([
            'date' => '2024-01-01',
            'marsaid' => $marsa->id,
            'customerid' => $customer->id,
            'many' => 100,
            'add' => 10,
            'total' => 110,
            'from' => '2024-01-01',
            'to' => '2024-12-31',
            'batch' => 100,
            'totalbatches' => 12,
            'wastaname' => 'Test Wasta',
            'codewasta' => 'TW001',
            'hieght' => 10,
            'width' => 20
        ]);

        $response = $this->controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Contract created successfully', $responseData['message']);
        $this->assertDatabaseHas('necklases', [
            'marsaid' => $marsa->id,
            'customerid' => $customer->id
        ]);
    }

    /** @test */
    public function it_fails_to_store_contract_with_invalid_data()
    {
        $request = new \Illuminate\Http\Request([]);

        $response = $this->controller->store($request);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /** @test */
    public function it_shows_a_specific_contract()
    {
        $contract = factory(necklase::class)->create();

        $response = $this->controller->show($contract->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('contract viewed successfully', $responseData['message']);
        $this->assertEquals($contract->id, $responseData['contracts']['id']);
    }

    /** @test */
    public function it_returns_error_when_contract_not_found()
    {
        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('marsa not found ! ', $responseData['message']);
    }

    /** @test */
    public function it_updates_a_contract()
    {
        $contract = factory(necklase::class)->create();
        $marsa = factory(marsa::class)->create();
        $customer = factory(customer::class)->create();

        $request = new \Illuminate\Http\Request([
            'marsaid' => $marsa->id,
            'customerid' => $customer->id,
            'many' => 200,
            'add' => 20,
            'total' => 220,
            'from' => '2024-02-01',
            'to' => '2024-12-31',
            'date' => '2024-02-01',
            'batch' => 200,
            'totalbatches' => 12,
            'wastaname' => 'Updated Wasta',
            'codewasta' => 'UW001',
            'hieght' => 15,
            'width' => 25
        ]);

        $response = $this->controller->update($request, $contract->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Contract updated successfully', $responseData['message']);
    }

    /** @test */
    public function it_deletes_a_contract()
    {
        $contract = factory(necklase::class)->create();

        $response = $this->controller->destroy($contract->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Contract deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('necklases', ['id' => $contract->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_nonexistent_contract()
    {
        $response = $this->controller->destroy(999);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Contract not found', $responseData['error']);
    }
}
