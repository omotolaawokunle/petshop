<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Product;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    protected $token;

    public function test_can_get_products_list(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson(route('api.v1.product'));
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['title', 'category_uuid', 'price', 'description', 'metadata', 'category', 'brand'],
                ],
            ]);
    }

    public function test_can_create_product(): void
    {
        $this->loginAsAdmin();

        $productData = Product::factory()->definition();
        $response = $this->withToken($this->token)->postJson(route('api.v1.product.create'), $productData);

        $response->assertOk();
        $this->assertDatabaseHas('products', ['title' => $productData['title']]);
    }

    public function test_can_show_product()
    {
        $product = $this->getProduct();

        $response = $this->getJson(route('api.v1.product.show', $product));

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'title' => $product->title,
                ],
            ]);
    }

    public function test_can_update_product()
    {
        $this->loginAsAdmin();

        $product = $this->getProduct();
        $product->title = $this->faker->sentence;

        $response = $this->withToken($this->token)->putJson(route('api.v1.product.edit', $product), $product->toArray());

        $response->assertOk();
        $this->assertDatabaseHas('products', [
            'uuid' => $product->uuid,
            'title' => $product->title,
        ]);
    }

    public function test_can_delete_product()
    {
        $this->loginAsAdmin();

        $product = $this->getProduct();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.product.delete', $product));

        $response->assertOk();
        $this->assertSoftDeleted('products', ['uuid' => $product->uuid]);
    }

    public function test_unauthenticated_user_cannot_access_protected_methods(): void
    {
        $product = $this->getProduct();

        // Testing store
        $response = $this->postJson(route('api.v1.product.create'));
        $response->assertUnauthorized();

        // Testing update
        $response = $this->putJson(route('api.v1.product.edit', $product));
        $response->assertUnauthorized();

        // Testing destroy
        $response = $this->deleteJson(route('api.v1.product.delete', $product));
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

    private function getProduct(): Product
    {
        return Product::factory()->create();
    }
}
