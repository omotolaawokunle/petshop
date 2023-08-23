<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ResponseCodes;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    protected string $token;
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
        $response = $this->postJson(route('api.v1.user.create'), $this->getInvalidUserData());
        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
    }

    public function test_get_authenticated_user(): void
    {
        $this->loginAsUser();
        $response = $this->withToken($this->token)->getJson(route('api.v1.user.show'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'uuid', 'first_name', 'last_name', 'email', 'email_verified_at', 'avatar', 'address', 'phone_number', 'is_marketing', 'created_at', 'updated_at', 'last_login_at'
            ]
        ]);
    }

    public function test_cannot_get_authenticated_user_without_valid_session(): void
    {
        $response = $this->withToken('invalid-token')->getJson(route('api.v1.user.show'));
        $response->assertStatus(ResponseCodes::HTTP_UNAUTHORIZED);
    }

    public function test_update_user_account(): void
    {
        $this->loginAsUser();
        $response = $this->withToken($this->token)->putJson(route('api.v1.user.edit'), $this->getUserData());
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'uuid', 'first_name', 'last_name', 'email', 'email_verified_at', 'avatar', 'address', 'phone_number', 'is_marketing', 'created_at', 'updated_at', 'last_login_at'
            ]
        ]);
    }

    public function test_update_user_account_as_unauthenticated_user(): void
    {
        $response = $this->withToken("invalid-token")->putJson(route('api.v1.user.edit'), $this->getUserData());
        $response->assertStatus(ResponseCodes::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_user_account_with_invalid_data(): void
    {
        $this->loginAsUser();
        $response = $this->withToken($this->token)->putJson(route('api.v1.user.edit'), $this->getInvalidUserData());
        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
    }

    private function loginAsUser(): void
    {
        $user = User::factory()->create();
        $this->token = $user->createToken('test-user-auth');
        return;
    }

    private function getUserData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@gmail.com',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'is_marketing' => 0,
        ];
    }

    private function getInvalidUserData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'is_marketing' => 0,
        ];
    }
}
