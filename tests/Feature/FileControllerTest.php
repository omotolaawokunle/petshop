<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\File;

class FileControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_file_can_be_uploaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson(route('api.v1.file.upload'), ['file' => $file]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['uuid', 'name', 'type', 'path', 'size']
            ]);

        Storage::disk('public')->assertExists('pet-shop/' . $response->json('path'));
    }

    public function test_file_can_be_downloaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        $this->postJson(route('api.v1.file.upload'), ['file' => $file]);

        $response = $this->getJson(route('api.v1.file.download', File::latest()->first()));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/octet-stream');
    }
}
