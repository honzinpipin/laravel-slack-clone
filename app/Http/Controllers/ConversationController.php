<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ConversationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ConversationController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $conversations = $request->user()
            ->conversations()
            ->with('users', 'messages.reactions', 'messages.attachments')
            ->get();

        return ConversationResource::collection($conversations);
    }

    public function store(Request $request): ConversationResource
    {
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

        return new ConversationResource($conversation->load('users'));
    }


    public function update(Request $request, Conversation $conversation): ConversationResource
    {
        $this->authorize('update', $conversation);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $conversation->update(['name' => $data['name']]);
        return new ConversationResource($conversation);
    }


    public function show(Request $request, Conversation $conversation): ConversationResource
    {
        $this->authorize('view', $conversation);

        $conversation->load('users', 'messages.reactions.user', 'messages.attachments');

        return new ConversationResource($conversation);
    }


    public function destroy(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }
}