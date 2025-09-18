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
use App\Http\Resources\MessageResource;
use App\Http\Requests\addUserRequest;
use App\Http\Requests\removeUserRequest;



class ConversationController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $conversations = $request->user()
            ->conversations()
            ->with('users')
            ->orderBy('id', 'desc')
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
        return new ConversationResource($conversation->load('users'));
    }


    public function show(Request $request, Conversation $conversation): ConversationResource
    {
        $this->authorize('view', $conversation);

        $conversation->load('users');

        $messages = $conversation->messages()
            ->with(['user', 'attachments', 'reactions.user'])
            ->orderBy('id', 'asc')
            ->paginate(20);

        return (new ConversationResource($conversation))
            ->additional(['messages' => MessageResource::collection($messages)]);
    }


    public function destroy(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }


    public function addUser(addUserRequest $request, Conversation $conversation)
    {
        $data = $request->validated();

        $conversation->user()->syncWithoutDetaching($data['user_id']);

        return response()->json(['message' => 'User added successfully']);
    }


    public function removeUser(removeUserRequest $request, Conversation $conversation)
    {

        $data = $request->validated();

        $conversation->users()->detach($data['user_id']);

        return response()->json(['message' => 'User removed successfully']);
    }
}