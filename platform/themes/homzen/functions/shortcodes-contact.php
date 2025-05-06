<?php

use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Contact\Forms\ShortcodeContactAdminConfigForm;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\FieldOptions\ShortcodeTabsFieldOption;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

app()->booted(function (): void {
    if (! is_plugin_active('contact')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace('partials.shortcodes.contact-form.index');
        });

        Shortcode::setPreviewImage('contact-form', Theme::asset()->url('images/shortcodes/contact-form/style-1.png'));

        Shortcode::modifyAdminConfig('contact-form', function (ShortcodeContactAdminConfigForm $form) {
            $attributes = $form->getModel();

            return $form
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->choices(
                            collect(range(1, 2))
                                ->mapWithKeys(fn ($number) => [
                                    $number => [
                                        'label' => __('Style :number', ['number' => $number]),
                                        'image' => Theme::asset()->url("images/shortcodes/contact-form/style-$number.png"),
                                    ],
                                ])
                                ->all()
                        )
                        ->selected(Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Subtitle'))
                        ->collapsible('style', '2', Arr::get($attributes, 'style', 1)),
                )
                ->add(
                    'description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Description'))
                )
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background image'))
                )
                ->add(
                    'show_information_box',
                    OnOffCheckboxField::class,
                    CheckboxFieldOption::make()
                        ->label(__('Show information box'))
                        ->defaultValue(true)
                        ->collapsible('style', '1', Arr::get($attributes, 'style', 1)),
                )
                ->addOpenFieldset('information_box', [
                    'data-bb-collapse' => 'true',
                    'data-bb-trigger' => '[name=style]',
                    'data-bb-value' => 1,
                    'style' => Arr::get($attributes, 'style', 1) == 1 ? 'display: block' : 'display: none',
                ])
                ->add(
                    'contact_title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Information box title'))
                )
                ->add(
                    'contact_info',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->fields([
                            'label' => [
                                'type' => 'text',
                                'title' => __('Label'),
                            ],
                            'content' => [
                                'type' => 'textarea',
                                'title' => __('Content'),
                                'helper' => __('You can use HTML tags. Example: &lt;a href=&quot;tel:0123456789&quot;&gt;0123456789&lt;/a&gt; or &lt;br&gt; for line break'),
                            ],
                        ])
                        ->attrs($attributes)
                        ->max(5)
                )
                ->add(
                    'show_social_links',
                    OnOffCheckboxField::class,
                    CheckboxFieldOption::make()
                        ->label(__('Show social links'))
                        ->helperText(__('Manage the social links in Theme Options -> Social Links'))
                        ->defaultValue(true)
                )
                ->addCloseFieldset('information_box');
        });
    });
});
