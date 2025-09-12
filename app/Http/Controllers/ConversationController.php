<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use AuthorizesRequests;

class ConversationController extends Controller{

    public function index(Request $request)
    {
        $conversations = $request->user()
            ->conversations()
            ->with('users', 'messages.reactions', 'messages.attachments')
            ->get();
        return response()->json($conversations);
    }

    public function store(Request $request){
        $data = $request->validate([
            'type' => 'required|in:private,group',
            'name' => 'nullable|string|max:255',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $conversation = Conversation::create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
        ]);

        $conversation->users()->attach(array_merge([$request->user()->id], $data['user_ids']));

        return response()->json($conversation->load('users'));
    }


    public function updateName(Request $request, Conversation $conversation){
        $this->authorize('update', $conversation);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

       $conversation->update(['name' => $data['name']]);
       return response()->json($conversation);
    }
        

    public function show(Request $request, Conversation $conversation){
    $this->authorize('view', $conversation);

    return response()->json(
        $conversation->load(
            'users',
            'messages',
            'messages.reactions',
            'messages.attachments'
        )
    );
}


    public function destroy(Request $request, Conversation $conversation){
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }
}