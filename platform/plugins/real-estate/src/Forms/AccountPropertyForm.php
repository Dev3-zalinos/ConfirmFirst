<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\FormFieldOptions;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Http\Requests\AccountPropertyRequest;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Facility;

class AccountPropertyForm extends PropertyForm
{
    public function setup(): void
    {
        parent::setup();

        $facilities = Facility::query()
            ->select(['id', 'name'])
            ->get()
            ->each(function ($item): void {
                $item->name = (string) $item->name;
            });

        if ($this->getModel()) {
            $selectedFacilities = $this->getModel()->facilities()->select('re_facilities.id', 'distance')->get();
        } else {
            $selectedFacilities = collect();
        }

        $this
            ->model(Property::class)
            ->template('plugins/real-estate::account.forms.base')
            ->hasFiles()
            ->setValidatorClass(AccountPropertyRequest::class)
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('never_expired')
            ->modify(
                'auto_renew',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::property.renew_notice', [
                        'days' => RealEstateHelper::propertyExpiredDays(),
                    ]))
                    ->defaultValue(false),
                true
            )
            ->remove('author_id')
            ->addAfter(
                'description',
                'content',
                CustomEditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/real-estate::property.form.content'))
                    ->required()
            )
            ->addAfter(
                'content',
                'images',
                MultipleUploadField::class,
                FormFieldOptions::make()
                    ->label(trans('plugins/real-estate::account-property.images', [
                        'max' => RealEstateHelper::maxPropertyImagesUploadByAgent(),
                    ]))
            )
            ->addMetaBoxes([
                'facilities' => [
                    'title' => trans('plugins/real-estate::property.distance_key'),
                    'content' => view('plugins/real-estate::partials.form-facilities', compact('facilities', 'selectedFacilities')),
                    'priority' => 2,
                ],
            ]);
    }
}