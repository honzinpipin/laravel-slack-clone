<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Conversation;
class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $conversation = $this->route('conversation');
        return $conversation && $this->user()->can('view', $conversation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "content" => "required|string|max:1000",
            "attachment_ids" => "nullable|array",
            "attachment_ids.*" => "exists:attachments,id",
            'parent_message_id' => 'nullable|integer|exists:messages,id'
        ];
    }
}
