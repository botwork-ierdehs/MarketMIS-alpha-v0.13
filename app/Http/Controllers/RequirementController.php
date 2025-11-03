<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

use Illuminate\Http\UploadedFile;
use App\Models\Requirement;
use App\Http\Requests\RequirementRequest;
use Illuminate\Http\JsonResponse;

class RequirementController extends Controller
{

    public function addRequirements(array $requirements): JsonResponse
    {
        Log::info('Received requirements for upload', [
            'count' => count($requirements),
            'requirement_keys' => array_map(function ($item) {
                return [
                    'permit_id' => $item['permit_id'] ?? null,
                    'requirement_checklist_id' => $item['requirement_checklist_id'] ?? null,
                    'has_attachment' => isset($item['attachment']) && $item['attachment'] instanceof UploadedFile,
                ];
            }, $requirements),
        ]);

        $uploaded = [];

        foreach ($requirements as $item) {
            // Ensure valid upload
            if (
                !isset($item['attachment']) ||
                !isset($item['requirement_checklist_id']) ||
                !isset($item['permit_id']) ||
                !$item['attachment'] instanceof UploadedFile
            ) {
                Log::warning('Skipped invalid requirement item', $item);
                continue;
            }

            $file = $item['attachment'];
            $permitId = $item['permit_id'];
            $checklistId = $item['requirement_checklist_id'];

            // Generate unique filename
            $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // ✅ Move file immediately to public/uploads
            $file->move(public_path('uploads'), $uuidName);

            $path = 'uploads/' . $uuidName;

            // ✅ Save record in DB
            Requirement::create([
                'permit_id' => $permitId,
                'requirement_checklist_id' => $checklistId,
                'attachment' => $uuidName,
                'path' => $path,
            ]);

            // ✅ Add result for response
            $uploaded[] = [
                'permit_id' => $permitId,
                'requirement_checklist_id' => $checklistId,
                'original_name' => $file->getClientOriginalName(),
                'attachment' => $uuidName,
                'url' => asset($path), // ✅ correct public URL
            ];
        }

        return response()->json([
            'message' => 'All attachments uploaded and saved.',
            'files' => $uploaded,
        ]);
    }

    public function fieldValidator(RequirementRequest $request): Response
    {
        return $request->validated();
    }

    public function deleteRequirements($permitId)
    {
        try {
            // Delete all requirements for the given permit ID
            Requirement::where('permit_id', $permitId)->delete();

            Log::info("All requirements deleted for permit_id: {$permitId}");

            return response()->json([
                'success' => true,
                'message' => 'Requirements deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete requirements for permit_id: {$permitId}. Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete requirements.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
