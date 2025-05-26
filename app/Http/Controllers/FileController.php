<?php

// app/Http/Controllers/FileController.php
namespace App\Http\Controllers;

use App\Services\AwsFileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
     * Delete file from storage
     */
    public function delete(Request $request): JsonResponse
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
            $deleted = $this->fileService->delete(
                $request->input('file_path')
            );

            return $deleted
                ? response()->json(['message' => 'File deleted successfully'])
                : response()->json(['error' => 'File not found'], 404);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File deletion failed: ' . $e->getMessage()
            ], 500);
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
