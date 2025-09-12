<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;

class MessageReactionController extends Controller {
    
    public function store(Request $request, Message $message){
        $data = $request->validate([
            'emoji' => 'required|string',
        ]);

        MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->where('emoji', $data['emoji'])
            ->delete();
        

            $reaction = MessageReaction::create([
                'message_id' => $message->id,
                'user_id' => $request->user()->id,
                'emoji' => $data['emoji'],
            ]);

            return response()->json($reaction);
    }

    public function destroy(Request $request, Message $message, $emoji){
        MessageReaction::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->where('emoji', $emoji)
            ->delete();

        return response()->json(['message' => 'Reaction removed']);
    }
}