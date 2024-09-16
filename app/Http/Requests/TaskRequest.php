<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => 'required|string',
            'assignee' => 'required|array',
            "dueDate" => 'nullable|date',
            "hours" =>  'nullable:integer',
            "minutes" =>  'nullable:integer',
            "note" => 'nullable|string',
            "reminder" => 'required|boolean',
            "repeat" => 'required|integer',
            "repeatDates" => 'nullable|array',
            "selectedCategories" => 'nullable|array',
            'timeframe' => 'nullable|string',
            'isAdd' => 'nullable|boolean',
            'task_id' => 'nullable|integer',
        ];
    }
}
