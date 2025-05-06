<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\MetaBox;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Http\Requests\ConsultCustomFieldRequest;
use Botble\RealEstate\Models\ConsultCustomField;

class ConsultCustomFieldForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts('jquery-ui')
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/custom-field.js');

        $this
            ->model(ConsultCustomField::class)
            ->formClass('custom-field-form')
            ->setValidatorClass(ConsultCustomFieldRequest::class)
            ->add(
                'type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/real-estate::consult.custom_field.type'))
                    ->required()
                    ->choices(ConsultCustomFieldTypeEnum::labels())
                    ->toArray()
            )
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->required()
                    ->toArray()
            )
            ->add(
                'placeholder',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::consult.custom_field.placeholder'))
                    ->placeholder(trans('plugins/real-estate::consult.custom_field.placeholder'))
                    ->maxLength(120)
                    ->toArray()
            )
            ->add(
                'required',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::consult.custom_field.required'))
                    ->toArray()
            )
            ->add(
                'order',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/real-estate::consult.custom_field.order'))
                    ->required()
                    ->defaultValue(999)
                    ->toArray()
            )
            ->when(is_plugin_active('language'), function (FormAbstract $form): void {
                $isDefaultLanguage = ! defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')
                    || ! request()->input('ref_lang')
                    || request()->input('ref_lang') === Language::getDefaultLocaleCode();
                $customField = $form->getModel();
                $options = $customField->options->sortBy('order');

                $form->addMetaBox(
                    MetaBox::make('consult-custom-field-options')
                        ->hasTable()
                        ->attributes([
                            'class' => 'custom-field-options-box',
                            'style' => sprintf(
                                'display: %s;',
                                in_array(old('type', $customField), [ConsultCustomFieldTypeEnum::DROPDOWN, ConsultCustomFieldTypeEnum::RADIO]) ? 'block' : 'none;'
                            ),
                        ])
                        ->title(trans('plugins/real-estate::consult.custom_field.options'))
                        ->content(view(
                            'plugins/real-estate::consults.custom-fields.options',
                            compact('options', 'isDefaultLanguage')
                        ))
                        ->footerContent($isDefaultLanguage ? view(
                            'plugins/real-estate::consults.custom-fields.options-footer',
                            compact('isDefaultLanguage')
                        ) : null)
                );
            });
    }
}
