<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Batch;
use App\necklase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class BatchApiTest extends TestCase
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
    public function it_requires_authentication_to_access_batches()
    {
        $response = $this->getJson('/api/batch');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_all_batches_when_authenticated()
    {
        factory(Batch::class, 3)->create();

        $response = $this->authenticatedJson('GET', '/api/batch');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'batch' => [
                         '*' => ['id', 'necklaceid', 'amount']
                     ]
                 ])
                 ->assertJson([
                     'message' => 'Batches viewed successfully'
                 ]);
    }

    /** @test */
    public function it_creates_a_new_batch_via_api()
    {
        $necklase = factory(necklase::class)->create([
            'remainingamount' => 1000,
            'remainingbatches' => 10
        ]);

        $batchData = [
            'necklaceid' => $necklase->id,
            'amount' => 100,
            'from' => '2024-01-01',
            'to' => '2024-01-31'
        ];

        $response = $this->authenticatedJson('POST', '/api/batch', $batchData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'batch stored successfully'
                 ]);

        $this->assertDatabaseHas('batches', [
            'necklaceid' => $necklase->id,
            'amount' => 100
        ]);

        // Verify necklace remaining amount is updated
        $necklase->refresh();
        $this->assertEquals(900, $necklase->remainingamount);
        $this->assertEquals(9, $necklase->remainingbatches);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_batch()
    {
        $response = $this->authenticatedJson('POST', '/api/batch', []);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false
                 ]);
    }

    /** @test */
    public function it_returns_batches_for_a_contract()
    {
        $contract = factory(necklase::class)->create();
        factory(Batch::class, 2)->create([
            'necklaceid' => $contract->id
        ]);

        $response = $this->authenticatedJson('GET', "/api/batchesOfContract/{$contract->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'batches' => [
                         '*' => ['id', 'necklaceid']
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_batch_data_with_relationships()
    {
        $batch = factory(Batch::class)->create();

        $response = $this->authenticatedJson('GET', "/api/getBatchData/{$batch->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'batchData' => [
                         '*' => [
                             'id',
                             'necklaceid',
                             'wastaname',
                             'codewasta',
                             'owner',
                             'customerName'
                         ]
                     ]
                 ]);
    }
}
