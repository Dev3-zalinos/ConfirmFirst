<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\FeatureRequest;
use Botble\RealEstate\Models\Feature;

class FeatureForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Feature::class)
            ->setValidatorClass(FeatureRequest::class)
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->required()
            )
            ->add(
                'icon',
                CoreIconField::class,
                CoreIconFieldOption::make()
                    ->label(trans('plugins/real-estate::feature.form.icon')),
            );
    }
}
