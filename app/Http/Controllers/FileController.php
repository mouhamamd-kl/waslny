<?php

// app/Http/Controllers/FileController.php
namespace App\Http\Controllers;

use App\Services\AwsFileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function __construct(
        protected AwsFileService $fileService
    ) {}

    /**
     * Upload file to storage
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'path_prefix' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $url = $this->fileService->upload(
                $request->file('file'),
                '',
                true
                // $request->input('path_prefix')
            );

            return response()->json([
                'url' => $url,
                'message' => 'File uploaded successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file with proper error handling
     */
    public function deleteFile(string $path = 'waslny/Frame%2011.png', string $disk = 'supabase'): bool
    {

        try {
            if (empty($path)) {
                Log::warning('Attempted to delete file with empty path');

                return false;
            }

            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($disk);

            if (! $storage->exists($path)) {
                Log::warning("File not found for deletion: {$path} on disk: {$disk}");
                return true; // Consider non-existent files as successfully deleted
            }

            return $storage->delete($path);
        } catch (\Exception $e) {
            Log::error("S3 Delete Error: {$e->getMessage()}", [
                'path' => $path,
                'disk' => $disk,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Check if the file from storage exists
     */
    public function exists(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $exists = $this->fileService->exists(
                $request->input('file_path')
                // 'Untitled'
            );

            return $exists
                ? response()->json(['message' => 'File exsits '])
                : response()->json(['error' => 'File not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File Exists Check Failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
