<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ConversationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;


class ConversationController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $conversations = $request->user()
            ->conversations()
            ->with('users', 'messages.reactions', 'messages.attachments')
            ->orderBy('id','desc')
            ->paginate(10);

        return ConversationResource::collection($conversations);
    }

    public function store(StoreConversationRequest $request): ConversationResource
    {
        $data = $request->validated();

        $conversation = Conversation::create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
        ]);

        $conversation->users()->attach(array_merge([$request->user()->id], $data['user_ids']));

        return new ConversationResource($conversation->load('users'));
    }


    public function update(UpdateConversationRequest $request, Conversation $conversation): ConversationResource
    {

        $data = $request->validated();

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