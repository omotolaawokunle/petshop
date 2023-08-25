<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\FileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Upload a file.
     */
    public function store(FileRequest $request): JsonResponse
    {
        $data = $request->toArray();
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');
        $name = $data['name'] . "." . $file->getClientOriginalExtension();
        $data['path'] = $file->storePubliclyAs('pet-shop', $name, 'public');
        $file = File::create($data);
        return $this->success($file);
    }

    /**
     * Read a file.
     * @param File $file The uuid of the file.
     */
    public function show(File $file): BinaryFileResponse
    {
        $filePath = Storage::disk('public')->path($file->path);
        return $this->download($filePath, $file->name);
    }
}
