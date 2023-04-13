<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'numbers' => 'array',
            'emails' => 'array',
            'numbers.*.id' => 'integer|exists:numbers',
            'numbers.*.number' => 'nullable|integer',
            'emails.*.id' => 'integer|exists:emails',
            'emails.*.email' => 'nullable|email',
        ];
    }
}
