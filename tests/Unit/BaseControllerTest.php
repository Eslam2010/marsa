<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\api\BaseController;
use Illuminate\Http\JsonResponse;

class BaseControllerTest extends TestCase
{
    protected $baseController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseController = new BaseController();
    }

    /** @test */
    public function it_sends_success_response()
    {
        $result = ['id' => 1, 'name' => 'Test'];
        $message = 'Success message';

        $response = $this->baseController->sendResponse($result, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals($result, $responseData['data']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_sends_error_response_without_error_messages()
    {
        $error = 'Error message';
        $code = 404;

        $response = $this->baseController->sendError($error, [], $code);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($code, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals($error, $responseData['message']);
        $this->assertArrayNotHasKey('date', $responseData);
    }

    /** @test */
    public function it_sends_error_response_with_error_messages()
    {
        $error = 'Validation error';
        $errorMessages = ['field' => ['The field is required']];
        $code = 422;

        $response = $this->baseController->sendError($error, $errorMessages, $code);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($code, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals($error, $responseData['message']);
        $this->assertEquals($errorMessages, $responseData['date']);
    }
}
