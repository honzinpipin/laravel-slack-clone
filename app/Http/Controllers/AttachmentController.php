<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AttachmentResource;
use App\Http\Requests\StoreAttachmentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttachmentController extends Controller
{



    public function store(StoreAttachmentRequest $request): AttachmentResource
    {


        $data = $request->validated();

        $file = $data['file'];

        $path = $file->store('attachments', 'public');

        $attachment = new Attachment([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
        ]);

        $attachment->user()->associate($request->user());
        $attachment->save();



        return new AttachmentResource($attachment);
    }

    public function index(Message $message): AnonymousResourceCollection
    {
        $attachments = $message->attachments()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return AttachmentResource::collection($attachments);
    }


    public function destroy(Attachment $attachment): JsonResponse
    {
        Storage::disk('public')->delete($attachment->file_path);

        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted']);
    }
}
