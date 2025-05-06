<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Http\Requests\CustomFieldRequest;
use Botble\RealEstate\Models\CustomField;

class CustomFieldForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts(['jquery-ui'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/global-custom-fields.js',
            ]);

        $this
            ->model(CustomField::class)
            ->setValidatorClass(CustomFieldRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('type', 'customSelect', [
                'label' => trans('plugins/real-estate::custom-fields.type'),
                'required' => true,
                'attr' => ['class' => 'form-control custom-field-type'],
                'choices' => CustomFieldEnum::labels(),
            ])
            ->setBreakFieldPoint('type')
            ->addMetaBoxes([
                'custom_fields_box' => [
                    'attributes' => [
                        'id' => 'custom_fields_box',
                        'style' => 'display: none;',
                    ],
                    'id' => 'custom_fields_box',
                    'title' => trans('plugins/real-estate::custom-fields.options'),
                    'content' => view(
                        'plugins/real-estate::custom-fields.options',
                        ['options' => $this->model->options->sortBy('order')]
                    )->render(),
                    'header_actions' => view(
                        'plugins/real-estate::partials.forms.header-actions.button',
                        [
                            'id' => 'add-new-row',
                            'label' => trans('plugins/real-estate::custom-fields.option.add_row'),
                        ]
                    )->render(),
                    'has_table' => true,
                ],
            ]);
    }
}
