<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RewardAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assignee' => 'required|array',
            'place_id' => 'required|integer',
            'description' => 'required|string',
        ];
    }
}
