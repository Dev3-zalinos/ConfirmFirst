<?php

use Botble\Base\Forms\FieldOptions\LabelFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\LabelField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Newsletter\Forms\Fronts\NewsletterForm;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Property;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\FieldOptions\ShortcodeTabsFieldOption;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Carbon\Carbon;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

Event::listen(RouteMatched::class, function (): void {
    ThemeSupport::registerGoogleMapsShortcode(Theme::getThemeNamespace('partials.shortcodes'));
    ThemeSupport::registerYoutubeShortcode();

    Shortcode::register(
        'image-slider',
        __('Image Slider'),
        __('Dynamic carousel for featured content with customizable links.'),
        function (ShortcodeCompiler $shortcode) {
            $tabs = Shortcode::fields()->getTabsData(['name', 'image', 'url'], $shortcode);

            if (empty($tabs)) {
                return null;
            }

            return Theme::partial('shortcodes.image-slider.index', compact('shortcode', 'tabs'));
        }
    );

    Shortcode::setPreviewImage('image-slider', Theme::asset()->url('images/shortcodes/image-slider.png'));

    Shortcode::setAdminConfig('image-slider', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->addBackgroundColorField()
            ->add(
                'tabs',
                ShortcodeTabsField::class,
                ShortcodeTabsFieldOption::make()
                    ->fields([
                        'name' => [
                            'type' => 'text',
                            'title' => __('Name'),
                        ],
                        'image' => [
                            'type' => 'image',
                            'title' => __('Image'),
                            'required' => true,
                        ],
                        'url' => [
                            'type' => 'text',
                            'title' => __('URL'),
                        ],
                        'open_in_new_tab' => [
                            'type' => 'onOff',
                            'title' => __('Open URL in a new tab'),
                        ],
                    ])
                    ->attrs($attributes)
            )
            ->addSliderFields();
    });

    Shortcode::register(
        'services',
        __('Services'),
        __('Displays a set of services in a tabbed format. Each tab represents a service and includes fields for title, description, icon, ...'),
        function (ShortcodeCompiler $shortcode) {
            $services = Shortcode::fields()->getTabsData(['title', 'description', 'icon', 'icon_image', 'button_label', 'button_url'], $shortcode, 'services');
            $counters = Shortcode::fields()->getTabsData(['number', 'label'], $shortcode, 'counters');

            $iconImageSize = $shortcode->icon_image_size ?: 80;

            return Theme::partial('shortcodes.services.index', compact('shortcode', 'services', 'counters', 'iconImageSize'));
        }
    );

    Shortcode::setPreviewImage('services', Theme::asset()->url('images/shortcodes/services/style-1.png'));

    Shortcode::setAdminConfig('services', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->label(__('Style'))
                    ->choices(
                        collect(range(1, 5))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/services/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
                    ->selected(Arr::get($attributes, 'style', 1))
                    ->defaultValue(1)
            )
            ->addSectionHeadingFields()
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Description'))
            )
            ->add(
                'checklist',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Checklist'))
                    ->helperText(__('Enter checklist here, separated by commas (,)'))
                    ->collapsible('style', '3', Arr::get($attributes, 'style', 1))
            )
            ->addBackgroundColorField()
            ->addOpenFieldset('services')
            ->add(
                'service_label',
                LabelField::class,
                LabelFieldOption::make()->label(__('Services'))
            )
            ->add(
                'services',
                ShortcodeTabsField::class,
                ShortcodeTabsFieldOption::make()
                    ->min(0)
                    ->fields([
                        'title' => [
                            'type' => 'text',
                            'title' => __('Title'),
                        ],
                        'description' => [
                            'type' => 'textarea',
                            'title' => __('Description'),
                        ],
                        'icon' => [
                            'type' => 'coreIcon',
                            'title' => __('Icon'),
                        ],
                        'icon_image' => [
                            'type' => 'image',
                            'title' => __('Icon image'),
                            'helper' => __('This will replace the icon if it set.'),
                        ],
                        'button_label' => [
                            'type' => 'text',
                            'title' => __('Button label'),
                        ],
                        'button_url' => [
                            'type' => 'text',
                            'title' => __('Button URL'),
                        ],
                    ], 'services')
                    ->attrs($attributes)
            )
            ->add(
                'icon_image_size',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Icon image size (px)'))
                    ->helperText(__('Enter the size of the icon image in pixels. It is used when the icon image is set.'))
                    ->defaultValue(80)
            )
            ->addCloseFieldset('services')
            ->addOpenFieldset('counters')
            ->add(
                'counter_label',
                LabelField::class,
                LabelFieldOption::make()->label(__('Counters'))
            )
            ->add(
                'counters',
                ShortcodeTabsField::class,
                ShortcodeTabsFieldOption::make()
                    ->fields([
                        'label' => [
                            'type' => 'text',
                            'title' => __('Label'),
                        ],
                        'number' => [
                            'type' => 'number',
                            'title' => __('Number'),
                        ],
                    ], 'counters')
                    ->attrs($attributes)
            )
            ->addCloseFieldset('counters')
            ->add(
                'background_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Background image'))
                    ->collapsible('style', '4', Arr::get($attributes, 'style', '1'))
            )
            ->addSectionButtonAction()
            ->add(
                'centered_content',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Center content'))
            );
    });

    Shortcode::register(
        'hero-banner',
        __('Hero Banner'),
        __('Hero Banner'),
        function (ShortcodeCompiler $shortcode) {
            $property = null;

            if (is_plugin_active('real-estate') && $shortcode->style == 5 && ($propertyId = $shortcode->property_id)) {
                $property = Property::query()
                    ->where('moderation_status', ModerationStatusEnum::APPROVED)
                    ->find($propertyId);
            }

            return Theme::partial('shortcodes.hero-banner.index', compact('shortcode', 'property'));
        }
    );

    Shortcode::setPreviewImage('hero-banner', Theme::asset()->url('images/shortcodes/hero-banner/style-1.png'));

    Shortcode::setAdminConfig('hero-banner', function (array $attributes) {
        $selectedTabs = explode(',', (Arr::get($attributes, 'tabs', '') ?: 'project,rent,sale'));

        $form = ShortcodeForm::createFromArray($attributes)
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->label(__('Style'))
                    ->choices(
                        collect(range(1, 5))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/hero-banner/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
                    ->selected(Arr::get($attributes, 'style', 1))
            )
            ->when(is_plugin_active('real-estate'), function (ShortcodeForm $form) use ($attributes): void {
                $form->add(
                    'property_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Property'))
                        ->searchable()
                        ->collapsible('style', '5', Arr::get($attributes, 'style', '1'))
                        ->choices(
                            [
                                '' => __('Select a property'),
                                ...Property::query()
                                    ->where('moderation_status', ModerationStatusEnum::APPROVED)
                                    ->pluck('name', 'id')
                                    ->all(),
                            ]
                        )
                        ->helperText(__('Select a property to display on the banner.'))
                );
            })
            ->add(
                'transparent_header',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Make header transparent?'))
                    ->collapsible('style', '5', Arr::get($attributes, 'style', '1'))
            )
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
            )
            ->add(
                'animation_text',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Animation text'))
                    ->placeholder('Text 1, Text 2')
                    ->helperText(__('Separated by commas (,)'))
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
            )
            ->add(
                'search_box_enabled',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Enable search box'))
                    ->defaultValue(true)
            )
            ->add(
                'tabs',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Tabs'))
                    ->helperText(__('Select search tabs. Tips: if you want to sort tabs, please add tab one by one, add the first tab then save it, then add the second tab then save it...'))
                    ->searchable()
                    ->multiple()
                    ->choices(collect([
                        'rent' => __('For Rent'),
                        'project' => __('Project'),
                        'sale' => __('For Sale'),
                    ])->sortBy(fn ($tab, $key) => array_search($key, $selectedTabs))->all())
                    ->selected($selectedTabs)
            )
            ->add(
                'default_search_type',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(__('Default search type'))
                    ->choices([
                        'project' => __('Project'),
                        'rent' => __('Property for rent'),
                        'sale' => __('Property for sale'),
                    ])
            )
            ->addSectionButtonAction();

        foreach (range(1, 4) as $i) {
            $form->add(
                "slider_image_$i",
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->collapsible('style', '2', Arr::get($attributes, 'style', 1))
                    ->label(__('Slider Image :number', ['number' => $i]))
            );
        }

        return $form;
    });

    Shortcode::register('content-quote', __('Content Quote'), __('Content Quote'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.content-quote.index', compact('shortcode'));
    });

    Shortcode::setPreviewImage('content-quote', Theme::asset()->url('images/shortcodes/content-quote.png'));

    Shortcode::setAdminConfig('content-quote', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add(
                'message',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->required()
                    ->label(__('Content'))
            )
            ->add(
                'author',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Author'))
            );
    });

    Shortcode::register('content-image', __('Content image'), __('Content image'), function (ShortcodeCompiler $shortcode): ?string {
        $tabs = Shortcode::fields()->getTabsData(['image', 'caption'], $shortcode);

        return Theme::partial('shortcodes.content-image.index', compact('shortcode', 'tabs'));
    });

    Shortcode::setAdminConfig('content-image', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add(
                'number_of_columns',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Number of columns'))
                    ->choices([
                        'col-1' => __(':number Column', ['number' => 1]),
                        'col-2' => __(':number Columns', ['number' => 2]),
                        'col-3' => __(':number Columns', ['number' => 3]),
                        'col-4' => __(':number Columns', ['number' => 4]),
                        'col-5' => __(':number Columns', ['number' => 5]),
                        'col-6' => __(':number Columns', ['number' => 6]),
                    ])
                    ->addAttribute('class', 'shortcode-field-select-style')
                    ->toArray()
            )
            ->add(
                'tabs',
                ShortcodeTabsField::class,
                ShortcodeTabsFieldOption::make()
                    ->attrs($attributes)
                    ->fields([
                        'image' => [
                            'type' => 'image',
                            'title' => __('Image'),
                            'required' => true,
                        ],
                        'caption' => [
                            'title' => __('Caption'),
                        ],
                    ])
                    ->toArray()
            );
    });

    Shortcode::register('call-to-action', __('Call To Action'), __('Create engaging call-to-action sections with customizable headings, buttons, and images.'), function (ShortcodeCompiler $shortcode) {
        return Theme::partial('shortcodes.call-to-action.index', compact('shortcode'));
    });

    Shortcode::setPreviewImage('call-to-action', Theme::asset()->url('images/shortcodes/call-to-action.png'));

    Shortcode::setAdminConfig('call-to-action', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->addSectionHeadingFields()
            ->addSectionButtonAction()
            ->add(
                'image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image')),
            );
    });

    Shortcode::register('about-us', __('About Us'), __('About Us'), function (ShortcodeCompiler $shortcode) {
        Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
        Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');

        return Theme::partial('shortcodes.about-us.index', compact('shortcode'));
    });

    Shortcode::setPreviewImage('about-us', Theme::asset()->url('images/shortcodes/about-us.png'));

    Shortcode::setAdminConfig('about-us', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title')),
            )
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Description'))
            )
            ->addSectionButtonAction()
            ->add(
                'image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image')),
            )
            ->add(
                'video_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Video URL'))
                    ->helperText(__('If a Video URL is provided, a play icon will appear on the image, allowing users to click and play the video.')),
            );
    });

    Shortcode::register(
        'content-tab',
        __('Content Tab'),
        __('A content tab to display information with a title.'),
        function (ShortcodeCompiler $shortcode): ?string {
            $tabs = Shortcode::fields()->getTabsData(['title', 'content'], $shortcode);

            if (empty($tabs)) {
                return null;
            }

            return Theme::partial('shortcodes.content-tab.index', compact('shortcode', 'tabs'));
        }
    );

    Shortcode::setPreviewImage('content-tab', Theme::asset()->url('images/shortcodes/content-tab.png'));

    Shortcode::setAdminConfig('content-tab', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title')),
            )
            ->add(
                'tabs',
                ShortcodeTabsField::class,
                ShortcodeTabsFieldOption::make()
                    ->label(__('Tabs'))
                    ->attrs($attributes)
                    ->fields([
                        'title' => [
                            'type' => 'text',
                            'title' => __('Title'),
                            'required' => true,
                        ],
                        'content' => [
                            'type' => 'textarea',
                            'title' => __('Content'),
                            'required' => true,
                        ],
                    ])
            );
    });

    Shortcode::register('coming-soon', __('Coming Soon'), __('Coming Soon'), function (ShortcodeCompiler $shortcode): string {
        try {
            $countdownTime = Carbon::parse($shortcode->countdown_time);
            Theme::asset()->container('footer')->usePath()->add('countdown', 'js/jquery.countdown.min.js');
        } catch (Exception) {
            $countdownTime = null;
        }

        $form = null;

        if (is_plugin_active('newsletter')) {
            $form = NewsletterForm::create();
        }

        return Theme::partial('shortcodes.coming-soon.index', compact('shortcode', 'countdownTime', 'form'));
    });

    Shortcode::setAdminConfig('coming-soon', function (array $attributes): ShortcodeForm {
        return ShortcodeForm::createFromArray($attributes)
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
            )
            ->add(
                'countdown_time',
                'datetime',
                [
                    'label' => __('Countdown time'),
                    'default_value' => Carbon::now()->addDays(7)->format('Y-m-d H:i'),
                ]
            )
            ->add(
                'address',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Address'))
            )
            ->add(
                'hotline',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Hotline'))
            )
            ->add(
                'business_hours',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Business hours'))
            )
            ->add(
                'show_social_links',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Show social links'))
                    ->defaultValue(true)
            )
            ->add(
                'image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Image'))
            );
    });
});
