<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'            => ['required', 'url', 'unique:monitors,url'],
            'check_interval' => ['sometimes', 'integer', 'min:1', 'max:60'],
            'threshold'      => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'url.unique' => 'This URL is already being monitored.',
        ];
    }
}
