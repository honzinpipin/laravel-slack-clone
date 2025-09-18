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
            ->orderBy('created_at','desc')
            ->paginate(20);
            
        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request, Conversation $conversation): MessageResource
    {

        $data = $request->validated();


        $message = new Message([
            'content' => $data['content']
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

    public function reply(ReplyToMessageRequest $request, Message $message): MessageResource|JsonResponse
    {
        $data = $request->validated();





        $reply = new Message([
            'content' => $data['content'],
            'parent_message_id' => $message->id,
        ]);

        $reply->user()->associate($request->user());
        $reply->conversation()->associate($message->conversation);
        $reply->save();

        $reply->load(['user', 'replies.user', 'reactions.user', 'attachments']);

        return new MessageResource($reply);
    }
}