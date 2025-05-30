<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AvatarRequest extends Request
{
    public function rules(): array
    {
        return [
            'avatar_file' => ['required', 'image', 'mimes:jpg,jpeg,png'],
        ];
    }
}
