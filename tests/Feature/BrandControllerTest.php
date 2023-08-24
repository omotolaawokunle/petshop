<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Brand;

class BrandControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    protected $token;

    public function test_get_all_brands(): void
    {
        $response = $this->getJson(route('api.v1.brand'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'slug', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    public function test_get_all_brands_with_sort(): void
    {
        $response = $this->getJson(route('api.v1.brand'), ['sortBy' => 'title', 'desc' => 0]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'slug', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    public function test_authenticated_user_can_create_brand(): void
    {
        $this->loginAsAdmin();

        $data = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.brand.create'), $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['uuid']
        ]);
    }

    public function test_retrieve_brand(): void
    {
        $brand = $this->getBrand();
        $response = $this->getJson(route('api.v1.brand.show', $brand));

        $response->assertOk();
        $response->assertJson([
            'data' => ['uuid' => $brand->uuid]
        ]);
    }

    public function test_authenticated_user_can_update_brand(): void
    {
        $this->loginAsAdmin();

        $brand = $this->getBrand();

        $updatedData = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.brand.edit', $brand), $updatedData);

        $response->assertOk();
        $response->assertJson(['data' => $updatedData]);
    }

    public function test_authenticated_user_can_destroy_brand(): void
    {
        $this->loginAsAdmin();
        $brand = $this->getBrand();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.brand.delete', $brand));

        $response->assertOk();
        $response->assertJson(['data' => []]);

        $this->assertDatabaseMissing('brands', ['title' => $brand->title]);
    }

    public function test_unauthenticated_user_cannot_access_protected_methods(): void
    {
        $brand = $this->getBrand();

        // Testing store
        $response = $this->postJson(route('api.v1.brand.create'));
        $response->assertUnauthorized();

        // Testing update
        $response = $this->putJson(route('api.v1.brand.edit', $brand));
        $response->assertUnauthorized();

        // Testing destroy
        $response = $this->deleteJson(route('api.v1.brand.delete', $brand));
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

    private function getBrand(): Brand
    {
        return Brand::factory()->create();
    }
}
