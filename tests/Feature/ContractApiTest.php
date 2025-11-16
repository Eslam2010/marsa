<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\necklase;
use App\customer;
use App\marsa;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ContractApiTest extends TestCase
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
    public function it_creates_a_new_contract_via_api()
    {
        $marsa = factory(marsa::class)->create();
        $customer = factory(customer::class)->create();

        $contractData = [
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
        ];

        $response = $this->authenticatedJson('POST', '/api/contract', $contractData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Contract created successfully'
                 ])
                 ->assertJsonStructure([
                     'Contract',
                     'ContractData'
                 ]);

        $this->assertDatabaseHas('necklases', [
            'marsaid' => $marsa->id,
            'customerid' => $customer->id,
            'total' => 110,
            'remainingamount' => 110
        ]);
    }

    /** @test */
    public function it_returns_contracts_for_a_customer()
    {
        $customer = factory(customer::class)->create();
        $contract = factory(necklase::class)->create([
            'customerid' => $customer->id
        ]);

        $response = $this->authenticatedJson('GET', "/api/contractsOfCustomer/{$customer->id}", [
            'lang' => 'EN'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'contracts'
                 ]);
    }
}
