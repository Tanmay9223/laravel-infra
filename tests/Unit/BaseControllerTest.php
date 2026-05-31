<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;

class BaseControllerTest extends TestCase
{
    public function test_send_error_with_default_code_and_no_error_data()
    {
        $controller = new BaseController();
        $response = $controller->sendError('An error occurred');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('An error occurred', $data['message']);
        $this->assertArrayNotHasKey('data', $data);
    }

    public function test_send_error_with_custom_code_and_error_data()
    {
        $controller = new BaseController();
        $response = $controller->sendError('Not Found', ['id' => 'Invalid ID'], 404);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Not Found', $data['message']);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(['id' => 'Invalid ID'], $data['data']);
    }
}
