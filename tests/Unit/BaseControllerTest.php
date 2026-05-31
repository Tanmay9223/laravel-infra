<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;

class BaseControllerTest extends TestCase
{
    public function test_send_response_without_result()
    {
        $controller = new BaseController();
        $response = $controller->sendResponse('Success message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Success message', $data['message']);
        $this->assertArrayNotHasKey('data', $data);
    }

    public function test_send_response_with_result()
    {
        $controller = new BaseController();
        $response = $controller->sendResponse('Success message', ['id' => 1, 'name' => 'John']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Success message', $data['message']);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(['id' => 1, 'name' => 'John'], $data['data']);
    }
}
