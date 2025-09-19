<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReactionResource;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreMessageReactionRequest;

class MessageReactionController extends Controller
{

    public function store(StoreMessageReactionRequest $request, Message $message): ReactionResource
    {
        $data = $request->validated();

        MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->where('emoji', $data['emoji'])
            ->delete();


        $reaction = new MessageReaction([
            'emoji' => $data['emoji'],
        ]);

        $reaction->user()->associate($request->user());
        $reaction->message()->associate($message);
        $reaction->save();

        $reaction->load('user');

        return new ReactionResource($reaction);
    }

    public function destroy(Request $request, Message $message, $emoji): JsonResponse
    {
        MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->where('emoji', $emoji)
            ->delete();

        return response()->json(['message' => 'Reaction removed']);
    }
}