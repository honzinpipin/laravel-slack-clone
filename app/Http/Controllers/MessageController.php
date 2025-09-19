<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attachment;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\ReplyToMessageRequest;


class MessageController extends Controller
{
    public function index(Conversation $conversation, Request $request): AnonymousResourceCollection
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with(['user', 'replies.user', 'reactions.user', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {

        $data = $request->validated();


        $message = new Message([
            'content' => $data['content'],
            'parent_message_id' => $data['parent_message_id'] ?? null,
        ]);

        $message->conversation()->associate($conversation);
        $message->user()->associate($request->user());
        $message->save();


        if (!empty($data['attachment_ids'])) {
            Attachment::whereIn('id', $data['attachment_ids'])
                ->whereNull('message_id')
                ->update(['message_id' => $message->id]);
        }

        $message->load(['user', 'replies.user', 'reactions.user', 'attachments']);
        return new MessageResource($message);
    }

    public function show(Conversation $conversation, Message $message): array
    {
        $this->authorize('view', $conversation);

        $message->load(['user', 'reactions.user', 'attachments']);

        $replies = $message->replies()
            ->with(['user', 'reactions.user', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return [
            'message' => new MessageResource($message),
            'replies' => MessageResource::collection($replies),
            'pagination' => [
                'current_page' => $replies->currentPage(),
                'last_page' => $replies->lastPage(),
                'per_page' => $replies->perPage(),
                'total' => $replies->total(),
            ],
        ];
    }


    public function destroy(Conversation $conversation, Message $message): JsonResponse
    {
        $this->authorize('delete', $message);

        if ($message->conversation_id !== $conversation->id) {
            return response()->json(['message' => 'Message does not belong to this conversation'], 422);
        }

        $message->replies()->delete();
        $message->attachments()->delete();
        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }
}