<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyToMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $message = $this->route("message");
        return $message && $this->user()->can('view', $message->conversation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $message = $this->route('message');

        return [
            'content' => 'required|string|max:1000',
            'parent_message_id' => 'nullable|integer|exists:messages,id,conversation_id' . ($message->conversations->id ?? 0),
        ];
    }
}
