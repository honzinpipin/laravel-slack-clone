<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class MessageReactionController extends Controller {
    
    public function store(Request $request, Message $message): JsonResponse{
        $data = $request->validate([
            'emoji' => 'required|string',
        ]);

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

            return response()->json($reaction);
    }

    public function destroy(Request $request, Message $message, $emoji): JsonResponse{
        MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->where('emoji', $emoji)
            ->delete();

        return response()->json(['message' => 'Reaction removed']);
    }
}