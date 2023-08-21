<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ResponseCodes;

class UserControllerTest extends TestCase
{
    public function test_can_create_user_account_with_valid_data(): void
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'is_marketing' => 0,
        ];

        $response = $this->postJson(route('api.v1.user.create'), $userData);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    public function test_cannot_create_user_account_with_invalid_data(): void
    {
        $invalidAdminData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'is_marketing' => 0,
        ];

        $response = $this->postJson(route('api.v1.user.create'), $invalidAdminData);
        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
    }
}
