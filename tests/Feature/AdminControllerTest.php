<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminControllerTest extends TestCase
{
    use DatabaseTransactions;
    protected string $token;

    public function loginAsAdmin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $this->token = $admin->createToken('test-auth');
    }

    public function test_get_user_listing_without_filters(): void
    {
        $this->loginAsAdmin();
        User::factory()->count(5)->create();
        $response = $this->withToken($this->token)->getJson(route('api.v1.admin.user-listing'));

        $response->assertOk();
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid', 'first_name', 'last_name', 'email', 'email_verified_at', 'avatar', 'address', 'phone_number', 'is_marketing', 'created_at', 'updated_at', 'last_login_at'
                ],
            ],
            'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'
        ]);
    }

    public function test_get_user_listing_with_filters(): void
    {
        $this->loginAsAdmin();
        User::factory()->create([
            'first_name' => 'John',
            'email' => 'john@example.com',
        ]);

        User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'jane@example.com',
        ]);

        $response = $this->withToken($this->token)->getJson(route('api.v1.admin.user-listing', [
            'first_name' => 'John',
            'email' => 'example.com',
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'first_name' => 'John',
                    'email' => 'john@example.com',
                ],
            ],
        ]);
    }

    public function test_admin_can_create_admin_account_with_valid_data(): void
    {
        $this->loginAsAdmin();
        $adminData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'marketing' => 0,
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.admin.create'), $adminData);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => $adminData['email'],
        ]);
    }

    public function test_admin_cannot_create_admin_account_with_invalid_data(): void
    {
        $this->loginAsAdmin();
        $invalidAdminData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'marketing' => 0,
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.admin.create'), $invalidAdminData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
    }

    public function test_admin_can_edit_user_account_with_valid_data(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'janedoe@example.com',
        ]);
        $this->loginAsAdmin();
        $adminData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'marketing' => 0,
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.admin.users.edit', ['user' => $user->uuid]), $adminData);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => $adminData['email'],
        ]);
    }

    public function test_admin_cannot_edit_user_account_with_invalid_data(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'janedoe@example.com',
        ]);
        $this->loginAsAdmin();
        $invalidAdminData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'janedoe',
            'address' => '1 White Ave GRA',
            'phone_number' => '2348190234923',
            'password' => 'userpassword',
            'marketing' => 0,
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.admin.users.edit', ['user' => $user->uuid]), $invalidAdminData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
    }

    public function test_admin_can_delete_valid_user_account(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'janedoe@example.com',
        ]);
        $this->loginAsAdmin();
        $response = $this->withToken($this->token)->deleteJson(route('api.v1.admin.users.delete', ['user' => $user->uuid]));

        $response->assertOk();
        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
        ]);
    }

    public function test_admin_cannot_delete_invalid_user_account(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'janedoe@example.com',
        ]);
        $this->loginAsAdmin();
        $response = $this->withToken($this->token)->deleteJson(route('api.v1.admin.users.delete', ['user' => $user->uuid . "ab"]));

        $response->assertStatus(404);
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }
}
