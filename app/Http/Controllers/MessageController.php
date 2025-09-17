<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attachment;


class MessageController extends Controller
{
    public function index(Conversation $conversation, Request $request): JsonResponse
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()->with(['user', 'replies', 'reactions', 'attachments'])->get();
        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $data = $request->validate([
            'content' => 'required|string',
            'attachment_ids' => 'nullable|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ]);


        $message = new Message([
            'content' => $data['content'],
        ]);

        $message->conversation()->associate($conversation);
        $message->user()->associate($request->user());
        $message->save();

        if (!empty($data['attachment_ids'])) {
            Attachment::whereIn('id', $data['attachment_ids'])
                ->whereNull('message_id')
                ->update(['message_id' => $message->id]);
        }

        return response()->json($message->load(['user', 'replies', 'reactions']));
    }

    public function reply(Request $request, Message $message): JsonResponse
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $this->authorize('view', $message->conversation);

        if ($message->conversation_id !== $message->conversation->id) {
            return response()->json([
                'error' => 'Invalid conversation: message does not belong to this conversation.'
            ], 422);
        }


        $reply = new Message([
            'content' => $data['content'],
            'parent_message_id' => $message->id,
        ]);

        $reply->user()->associate($request->user());
        $reply->conversation()->associate($message->conversation);
        $reply->save();

        return response()->json($reply->load(['user', 'replies', 'reactions']));
    }
}