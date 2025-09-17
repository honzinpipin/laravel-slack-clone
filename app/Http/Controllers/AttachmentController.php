<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class AttachmentController extends Controller {



    public function store(Request $request, Message $message): JsonResponse{


    $data = $request->validate([
    'file' => 'required|file|max: 20480'
    ]);

    $file = $data['file'];

    $path = $file->store('attachments', 'public');

    $attachment = new Attachment([
        
        'file_path' => $path,
        'file_name' => $file->getClientOriginalName(),
    ]);

    $attachment->message()->associate($message);
    $attachment->save();

    return response()->json([
        'message' => 'Attachment uploaded',
        'attachment' => $attachment,
        'url' => Storage::url($attachment->file_path)
    ]);
}

    public function index (Message $message): JsonResponse{
        return response()->json($message->attachments);
    }


    public function destroy(Attachment $attachment): JsonResponse{
        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted'
        ]);
    }
}
