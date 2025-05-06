<?php

namespace Botble\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChunkFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'string'],
            'offset' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'update_existing' => ['nullable', 'boolean'],
        ];
    }
}
