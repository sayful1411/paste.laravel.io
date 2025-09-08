<?php

namespace App\Http\Requests;

use App\Enums\ColorScheme;
use App\Enums\ExpiryOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'color_scheme' => ['nullable', new Enum(ColorScheme::class)],
            'expiry' => ['nullable', new Enum(ExpiryOption::class)],
            'custom_expiry' => 'nullable|date',
            'password' => 'nullable|string|min:6',
        ];
    }
}
