<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BulkImportRequest extends Request
{
    public function rules(): array
    {
        $mimes = implode(',', config('plugins.real-estate.general.bulk-import.mime_types'));

        return [
            'file' => 'required|file|mimetypes:' . $mimes,
        ];
    }
}
