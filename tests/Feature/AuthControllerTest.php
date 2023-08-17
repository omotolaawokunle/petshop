<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ResponseCodes;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_login()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->postJson(route('api.v1.admin.login'), [
            'email' => $admin->email,
            'password' => 'userpassword', // Use the correct password here
        ]);

        $response->assertStatus(ResponseCodes::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }

    public function test_non_admin_cannot_login()
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->postJson(route('api.v1.admin.login'), [
            'email' => $user->email,
            'password' => 'userpassword',
        ]);

        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'success' => 0,
            'error' => 'Failed to authenticate user!',
            'errors' => [],
            'data' => []
        ]);
    }
}
