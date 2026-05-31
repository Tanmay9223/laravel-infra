<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAuthControllerTest extends TestCase
{
    /**
     * Test admin login validation failure.
     */
    public function test_admin_login_validation_failure(): void
    {
        // Missing both email and password
        $response = $this->postJson('/api/v1/admin/login', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'email',
                         'password'
                     ]
                 ]);

        // Missing password
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@example.com'
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'password'
                     ]
                 ]);

        // Missing email
        $response = $this->postJson('/api/v1/admin/login', [
            'password' => 'secret'
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'email'
                     ]
                 ]);

        // Invalid email format
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'invalid-email',
            'password' => 'secret'
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'email'
                     ]
                 ]);
    }
}
