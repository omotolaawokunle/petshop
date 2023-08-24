<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\File;
use App\Http\Requests\FileRequest;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(FileRequest $request)
    {
        $data = $request->toArray();
        $data['path'] = $request->file('file')->storePubliclyAs('pet-shop', $data['name'], 'public');
        $file = File::create($data);
        return $this->success($file);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        $filePath = Storage::disk('public')->path("pet-shop/$file->name");
        return $this->download($filePath, $file->name);
    }
}
