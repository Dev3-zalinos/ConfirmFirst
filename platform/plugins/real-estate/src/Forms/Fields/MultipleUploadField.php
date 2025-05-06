<?php

namespace Botble\RealEstate\Forms\Fields;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormField;

class MultipleUploadField extends FormField
{
    protected function getTemplate(): string
    {
        Assets::addScripts(['dropzone'])
            ->addStyles(['dropzone']);

        return 'plugins/real-estate::account.forms.fields.multiple-upload';
    }
}
