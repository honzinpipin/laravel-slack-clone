<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller{
    public function index(Conversation $conversation, Request $request){
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()->with(['user', 'replies', 'reactions'])->get();
        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation){
        $this->authorize('view', $conversation);

        $data = $request->validate([
            'content' => 'required|string',
            'parent_message_id' => 'nullable|exists:messages,id',
        ]);


        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'parent_message_id' => $data['parent_message_id'] ?? null,
        ]);

        return response()->json($message->load(['user', 'replies', 'reactions']));
    }

    public function reply(Request $request, Message $message){
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $reply = Message::create([
            'conversation_id' => $message->conversation_id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'parent_message_id' => $message->id,
        ]);

        return response()->json($reply->load(['user', 'replies', 'reactions']));
    }
}