<?php

namespace Modules\Url\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class DecodeUrlRequest extends FormRequest
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
            'shortUrl' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    // Check if the URL contains short.est domain
                    if (!preg_match('/^https?:\/\/(www\.)?short\.est\//', $value)) {
                        $fail('The :attribute must be a valid short.est URL.');
                    }
                },
            ],
        ];
    }
}
