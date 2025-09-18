<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $conversation = $this->route("conversation");
        return $conversation && $conversation->type === 'group' && $conversation->users()->where('users.id', $this->user()->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribude, $value, $fail) {
                    if ($value == $this->user()->id) {
                        $fail('You cannot add yourself to the conversation');
                    }
                }
            ]
        ];
    }
}
