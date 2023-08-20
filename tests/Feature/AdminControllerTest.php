<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $token;

    public function loginAsAdmin()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $this->token = $admin->createToken('test-auth');
    }

    public function test_get_user_listing_without_filters()
    {
        $this->loginAsAdmin();
        User::factory()->count(5)->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.admin.user-listing'));

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
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

    public function test_get_user_listing_with_filters()
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
}