<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Category;

class CategoryControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    protected $token;

    public function test_get_all_categories(): void
    {
        $response = $this->getJson(route('api.v1.category'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'slug', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    public function test_get_all_categories_with_sort(): void
    {
        $response = $this->getJson(route('api.v1.category'), ['sortBy' => 'title', 'desc' => 0]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'slug', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    public function test_authenticated_user_can_create_category(): void
    {
        $this->loginAsAdmin();

        $data = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.category.create'), $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['uuid']
        ]);
    }

    public function test_retrieve_category(): void
    {
        $category = $this->getCategory();
        $response = $this->getJson(route('api.v1.category.show', $category));

        $response->assertOk();
        $response->assertJson([
            'data' => ['uuid' => $category->uuid]
        ]);
    }

    public function test_authenticated_user_can_update_category(): void
    {
        $this->loginAsAdmin();

        $category = $this->getCategory();

        $updatedData = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.category.edit', $category), $updatedData);

        $response->assertOk();
        $response->assertJson(['data' => $updatedData]);
    }

    public function test_authenticated_user_can_destroy_category(): void
    {
        $this->loginAsAdmin();
        $category = $this->getCategory();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.category.delete', $category));

        $response->assertOk();
        $response->assertJson(['data' => []]);
    }

    public function test_unauthenticated_user_cannot_access_protected_methods(): void
    {
        $category = $this->getCategory();

        // Testing store
        $response = $this->postJson(route('api.v1.category.create'));
        $response->assertUnauthorized();

        // Testing update
        $response = $this->putJson(route('api.v1.category.edit', $category));
        $response->assertUnauthorized();

        // Testing destroy
        $response = $this->deleteJson(route('api.v1.category.delete', $category));
        $response->assertUnauthorized();
    }

    private function loginAsAdmin(): void
    {
        $admin = \App\Models\User::factory([
            'is_admin' => true
        ])->create();
        $this->token = $admin->createToken('test-admin-auth');
        return;
    }

    private function getCategory(): Category
    {
        return Category::factory()->create();
    }
}
