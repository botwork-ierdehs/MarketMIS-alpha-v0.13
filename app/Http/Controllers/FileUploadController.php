<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function uploadArray(Request $request)
    {
        $request->validate([
            'attachments' => 'required|array',
            'attachments.*.id' => 'required|integer',
            'attachments.*.attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        $uploaded = [];

        foreach ($request->attachments as $item) {
            $file = $item['attachment'];
            $id = $item['id'];

            // Generate UUID filename with original extension
            $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store the file with UUID name in the 'uploads' directory
            $path = $file->storeAs('uploads/'.$uuidName);

            $uploaded[] = [
                'id' => $id,
                'original_name' => $file->getClientOriginalName(),
                'uuid_name' => $uuidName,
                'path' => $path,
                'url' => asset('storage/' . $path),
            ];
        }

        return response()->json([
            'message' => 'Attachments uploaded successfully',
            'files' => $uploaded,
        ]);
    }

}
