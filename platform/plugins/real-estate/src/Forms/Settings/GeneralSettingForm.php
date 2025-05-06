<?php

namespace Botble\RealEstate\Forms\Settings;

use Botble\Base\Forms\FieldOptions\LabelFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\LabelField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\Settings\GeneralSettingRequest;
use Botble\Setting\Forms\SettingForm;

class GeneralSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/real-estate::settings.general.name'))
            ->setSectionDescription(trans('plugins/real-estate::settings.general.description'))
            ->setValidatorClass(GeneralSettingRequest::class)
            ->add(
                'real_estate_enabled_projects',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.enabled_projects'))
                    ->value(RealEstateHelper::isEnabledProjects())
                    ->helperText(trans('plugins/real-estate::settings.general.form.enabled_projects_helper'))
            )
            ->add(
                'real_estate_enabled_property_types[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.enabled_property_types'))
                    ->choices(PropertyTypeEnum::labels())
                    ->selected(RealEstateHelper::enabledPropertyTypes())
            )
            ->add('real_estate_square_unit', SelectField::class, [
                'label' => trans('plugins/real-estate::settings.general.form.square_unit'),
                'choices' => [
                    '' => trans('plugins/real-estate::settings.general.form.square_unit_none'),
                    'm²' => trans('plugins/real-estate::settings.general.form.square_unit_meter'),
                    'ft2' => trans('plugins/real-estate::settings.general.form.square_unit_feet'),
                    'yd2' => trans('plugins/real-estate::settings.general.form.square_unit_yard'),
                ],
                'selected' => setting('real_estate_square_unit', 'm²'),
            ])
            ->add('real_estate_display_views_count_in_detail_page', OnOffCheckboxField::class, [
                'label' => trans('plugins/real-estate::settings.general.form.display_views_count_in_detail_page'),
                'value' => setting('real_estate_display_views_count_in_detail_page', false),
            ])
            ->add(
                'label_real_estate_hide_properties',
                LabelField::class,
                LabelFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.hide_properties_in_statuses'))
            )
            ->add('real_estate_hide_properties_in_statuses[]', MultiCheckListField::class, [
                'label' => false,
                'choices' => PropertyStatusEnum::labels(),
                'value' => old('real_estate_hide_properties_in_statuses', RealEstateHelper::exceptedPropertyStatuses()),
                'inline' => true,
            ])
            ->add(
                'label_real_estate_hide_projects',
                LabelField::class,
                LabelFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.hide_projects_in_statuses'))
            )
            ->add('real_estate_hide_projects_in_statuses[]', MultiCheckListField::class, [
                'label' => false,
                'choices' => ProjectStatusEnum::labels(),
                'value' => old('real_estate_hide_projects_in_statuses', RealEstateHelper::exceptedProjectsStatuses()),
                'inline' => true,
            ])
            ->add(
                'real_estate_enable_review_feature',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.enable_review_feature'))
                    ->value($enabledReviews = RealEstateHelper::isEnabledReview())
            )
            ->addOpenCollapsible('real_estate_enable_review_feature', '1', $enabledReviews)
            ->add('real_estate_reviews_per_page', TextField::class, [
                'label' => trans('plugins/real-estate::settings.general.form.reviews_per_page'),
                'value' => setting('real_estate_reviews_per_page', 10),
            ])
            ->addCloseCollapsible('real_estate_enable_review_feature', '1')
            ->add(
                'real_estate_enabled_custom_fields_feature',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.enable_custom_fields'))
                    ->value($enabledCustomFields = RealEstateHelper::isEnabledCustomFields())
            )
            ->addOpenCollapsible('real_estate_enabled_custom_fields_feature', '1', $enabledCustomFields)
            ->add(
                'real_estate_show_all_custom_fields_in_form_by_default',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(
                        trans('plugins/real-estate::settings.general.form.show_all_custom_fields_in_form_by_default')
                    )
                    ->helperText(
                        trans(
                            'plugins/real-estate::settings.general.form.show_all_custom_fields_in_form_by_default_helper'
                        )
                    )
                    ->value(setting('real_estate_show_all_custom_fields_in_form_by_default', false))
            )
            ->addCloseCollapsible('real_estate_enabled_custom_fields_feature', '1')
            ->add(
                'real_estate_enabled_consult_form',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.enable_consult_form'))
                    ->value($enabledConsultForm = RealEstateHelper::isEnabledConsultForm())
            )
            ->addOpenCollapsible('real_estate_enabled_consult_form', '1', $enabledConsultForm)
            ->add(
                'label_mandatory_fields_at_consult_form',
                LabelField::class,
                LabelFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.mandatory_fields_at_consult_form'))
            )
            ->add('real_estate_mandatory_fields_at_consult_form[]', MultiCheckListField::class, [
                'label' => false,
                'choices' => RealEstateHelper::getMandatoryFieldsAtConsultForm(),
                'value' => old(
                    'real_estate_mandatory_fields_at_consult_form',
                    RealEstateHelper::enabledMandatoryFieldsAtConsultForm()
                ),
                'inline' => true,
            ])
            ->add(
                'label_real_estate_mandatory_fields_at_consult_form',
                LabelField::class,
                LabelFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.hide_fields_at_consult_form'))
                    ->toArray(),
            )
            ->add('real_estate_hide_fields_at_consult_form[]', MultiCheckListField::class, [
                'label' => false,
                'choices' => RealEstateHelper::getMandatoryFieldsAtConsultForm(),
                'value' => old(
                    'real_estate_hide_fields_at_consult_form',
                    RealEstateHelper::getHiddenFieldsAtConsultForm()
                ),
                'inline' => true,
            ])
            ->addCloseCollapsible('real_estate_enabled_consult_form', '1')
            ->add(
                'real_estate_auto_generate_unique_id',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.auto_generate_unique_id'))
                    ->value($targetValue = setting('real_estate_auto_generate_unique_id', false))
            )
            ->addOpenCollapsible('real_estate_auto_generate_unique_id', '1', $targetValue == '1')
            ->add(
                'real_estate_unique_id_format',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.unique_id_format'))
                    ->value(setting('real_estate_unique_id_format'))
                    ->helperText(trans('plugins/real-estate::settings.general.form.unique_id_format_helper'))
            )
            ->addCloseCollapsible('real_estate_auto_generate_unique_id', '1')
            ->add(
                'real_estate_fixed_maximum_price_for_filter',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.fixed_maximum_price_for_filter'))
                    ->value($targetValue = setting('real_estate_fixed_maximum_price_for_filter', false))
                    ->helperText(trans('plugins/real-estate::settings.general.form.fixed_maximum_price_for_filter_helper'))
            )
            ->addOpenCollapsible('real_estate_fixed_maximum_price_for_filter', '1', $targetValue == '1')
            ->add(
                'real_estate_maximum_price_for_filter',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::settings.general.form.maximum_price_for_filter'))
                    ->value(setting('real_estate_maximum_price_for_filter'))
                    ->helperText(trans('plugins/real-estate::settings.general.form.maximum_price_for_filter_helper'))
            )
            ->addCloseCollapsible('real_estate_fixed_maximum_price_for_filter', '1');
    }
}
