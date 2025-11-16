<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\batchController;
use App\Batch;
use App\necklase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new batchController();
        $this->user = factory(User::class)->create();
        Auth::login($this->user);
    }

    /** @test */
    public function it_returns_all_batches()
    {
        factory(Batch::class, 3)->create();

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Batches viewed successfully', $responseData['message']);
        $this->assertCount(3, $responseData['batch']);
    }

    /** @test */
    public function it_stores_a_new_batch_with_valid_data()
    {
        $necklase = factory(necklase::class)->create([
            'remainingamount' => 1000,
            'remainingbatches' => 10
        ]);

        $request = new \Illuminate\Http\Request([
            'necklaceid' => $necklase->id,
            'amount' => 100,
            'from' => '2024-01-01',
            'to' => '2024-01-31'
        ]);

        $response = $this->controller->store($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('batch stored successfully', $responseData['message']);
        $this->assertDatabaseHas('batches', [
            'necklaceid' => $necklase->id,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_fails_to_store_batch_with_invalid_data()
    {
        $request = new \Illuminate\Http\Request([]);

        $response = $this->controller->store($request);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    /** @test */
    public function it_shows_a_specific_batch()
    {
        $batch = factory(Batch::class)->create();

        $response = $this->controller->show($batch->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('batch Viewed successfully', $responseData['message']);
        $this->assertEquals($batch->id, $responseData['batch']['id']);
    }

    /** @test */
    public function it_returns_error_when_batch_not_found()
    {
        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('batch not found ! ', $responseData['message']);
    }

    /** @test */
    public function it_updates_a_batch()
    {
        $batch = factory(Batch::class)->create([
            'amount' => 100,
            'from' => '2024-01-01',
            'to' => '2024-01-31'
        ]);

        $request = new \Illuminate\Http\Request([
            'necklaceid' => $batch->necklaceid,
            'amount' => 200,
            'from' => '2024-02-01',
            'to' => '2024-02-28'
        ]);

        $response = $this->controller->update($request, $batch->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('batch updated successfully', $responseData['message']);
    }

    /** @test */
    public function it_deletes_a_batch()
    {
        $batch = factory(Batch::class)->create();

        $response = $this->controller->destroy($batch->id);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('batch deleted successfully', $responseData['message']);
        $this->assertDatabaseMissing('batches', ['id' => $batch->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_nonexistent_batch()
    {
        $response = $this->controller->destroy(999);

        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('batch not found', $responseData['error']);
    }
}
