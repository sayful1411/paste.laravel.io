<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'code' => 'required|max:50000',
            'expiry' => 'nullable|in:1_hour,1_day,1_week,never,custom',
            'custom_expiry' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ];
    }
}
