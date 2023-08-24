<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\ResponseCodes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function test_admin_can_login(): void
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

    public function test_non_admin_cannot_login(): void
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

    public function test_admin_cannot_login_with_wrong_credentials(): void
    {
        User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->postJson(route('api.v1.admin.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'success' => 0,
            'error' => 'Failed to authenticate user!',
            'errors' => [],
            'data' => []
        ]);
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $token = $admin->createToken('test-auth');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('api.v1.admin.logout'));
        $response->assertStatus(ResponseCodes::HTTP_OK);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('api.v1.admin.logout'));
        $response->assertStatus(ResponseCodes::HTTP_UNAUTHORIZED);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.user.login'), [
            'email' => $user->email,
            'password' => 'userpassword',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }

    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        User::factory()->create();

        $response = $this->postJson(route('api.v1.user.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'success' => 0,
            'error' => 'Failed to authenticate user!',
            'errors' => [],
            'data' => []
        ]);
    }

    public function test_user_can_logout(): void
    {
        $admin = User::factory()->create();

        $token = $admin->createToken('test-auth');

        $response = $this->withToken($token)->postJson(route('api.v1.user.logout'));
        $response->assertStatus(ResponseCodes::HTTP_OK);

        $response = $this->withToken($token)->postJson(route('api.v1.user.logout'));
        $response->assertStatus(ResponseCodes::HTTP_UNAUTHORIZED);
    }

    public function test_forgot_password_email_sent_successfully(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('api.v1.user.forgot-password'), [
            'email' => $user->email,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'reset_token',
            ],
        ]);
    }

    public function test_forgot_password_email_not_sent_with_invalid_email(): void
    {
        $response = $this->postJson(route('api.v1.user.forgot-password'), [
            'email' => 'test@email.com',
        ]);

        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_successfully(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);
        $newPassword = 'new-password';

        $response = $this->postJson(route('api.v1.user.reset-password'), [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        /** @var User $user */
        $user = $user->fresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'message' => 'Password has been successfully updated.',
            ],
        ]);
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::factory()->create();
        $newPassword = 'new-password';
        $response = $this->postJson(route('api.v1.user.reset-password'), [
            'token' => 'invalid-token',
            'email' => $user->email, // Provide a valid user email
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'error' => 'Invalid or expired token.',
        ]);
    }
}
