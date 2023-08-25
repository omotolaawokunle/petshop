<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\File;
use App\Http\Requests\FileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Upload a file.
     */
    public function store(FileRequest $request)
    {
        $data = $request->toArray();
        $data['path'] = $request->file('file')->storePubliclyAs('pet-shop', $data['name'], 'public');
        $file = File::create($data);
        return $this->success($file);
    }

    /**
     * Read a file.
     * @param File $file The uuid of the file.
     */
    public function show(File $file)
    {
        $filePath = Storage::disk('public')->path("pet-shop/{$file->name}");
        return $this->download($filePath, $file->name);
    }
}
