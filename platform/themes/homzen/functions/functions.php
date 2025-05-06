<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\PhoneNumberField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\Base\Rules\MediaImageRule;
use Botble\Contact\Forms\Fronts\ContactForm;
use Botble\Media\Facades\RvMedia;
use Botble\Newsletter\Facades\Newsletter;
use Botble\Page\Forms\PageForm;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Forms\AccountPropertyForm;
use Botble\RealEstate\Forms\CategoryForm;
use Botble\RealEstate\Forms\Fronts\Auth\LoginForm;
use Botble\RealEstate\Forms\Fronts\Auth\RegisterForm;
use Botble\RealEstate\Forms\ProjectForm;
use Botble\RealEstate\Forms\PropertyForm;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Typography\TypographyItem;
use Botble\Widget\Facades\WidgetGroup;

if (! function_exists('get_max_properties_price')) {
    function get_max_properties_price(): int
    {
        if (setting('real_estate_fixed_maximum_price_for_filter')) {
            $maxPrice = setting('real_estate_maximum_price_for_filter');

            if ($maxPrice) {
                return (int) ceil($maxPrice);
            }
        }

        $price = Property::query()->max('price');

        return $price ? (int) ceil($price) : 0;
    }
}

if (! function_exists('get_max_projects_price')) {
    function get_max_projects_price(): int
    {
        if (setting('real_estate_fixed_maximum_price_for_filter')) {
            $maxPrice = setting('real_estate_maximum_price_for_filter');

            if ($maxPrice) {
                return (int) ceil($maxPrice);
            }
        }

        $price = Project::query()->max('price_to');

        return $price ? (int) ceil($price) : 0;
    }
}

if (! function_exists('get_min_square')) {
    function get_min_square(): int
    {
        $square = Property::query()->min('square');

        return $square ? (int) ceil($square) : 0;
    }
}

if (! function_exists('get_max_square')) {
    function get_max_square(): int
    {
        $square = Property::query()->max('square');

        return $square ? (int) ceil($square) : 0;
    }
}

if (! function_exists('get_min_flat')) {
    function get_min_flat(): int
    {
        $flat = Project::query()->min('number_flat');

        return $flat ? (int) ceil($flat) : 0;
    }
}

if (! function_exists('get_max_flat')) {
    function get_max_flat(): int
    {
        $flat = Project::query()->max('number_flat');

        return $flat ? (int) ceil($flat) : 0;
    }
}

if (! function_exists('get_property_listing_page_layout')) {
    function get_property_listing_page_layout(string $default = 'top-map'): string
    {
        $layout = theme_option('real_estate_property_listing_layout', $default);

        return in_array($layout, ['top-map', 'half-map', 'sidebar']) ? $layout : $default;
    }
}

app()->booted(function (): void {
    register_page_template([
        'default' => __('Default'),
        'full-width' => __('Full Width'),
        'no-layout' => __('No Layout'),
    ]);

    // We're using nice-select which is already included in the theme
    // No need to add Select2 library

    register_sidebar([
        'id' => 'top_footer_sidebar',
        'name' => __('Top Footer Sidebar'),
        'description' => __('Top section of the footer for logo and social links.'),
    ]);

    register_sidebar([
        'id' => 'inner_footer_sidebar',
        'name' => __('Inner Footer Sidebar'),
        'description' => __('Inner footer section for site info, menus, and newsletter.'),
    ]);

    register_sidebar([
        'id' => 'bottom_footer_sidebar',
        'name' => __('Bottom Footer Sidebar'),
        'description' => __('Bottom footer section for legal notices and credits.'),
    ]);

    register_sidebar([
        'id' => 'blog_sidebar',
        'name' => __('Blog Sidebar'),
        'description' => __('Add widgets here to appear in the sidebar of your blog pages.'),
    ]);

    register_sidebar([
        'id' => 'bottom_post_detail_sidebar',
        'name' => __('Bottom Post Detail Sidebar'),
        'description' => __('Place widgets here to display additional content below individual blog posts.'),
    ]);

    WidgetGroup::removeGroup('primary_sidebar');

    Theme::typography()
        ->registerFontFamilies([
            new TypographyItem('primary', __('Primary'), 'DM Sans'),
            new TypographyItem('heading', __('Heading'), 'Josefin Sans'),
        ])
        ->registerFontSizes([
            new TypographyItem('h1', __('Heading 1'), 80),
            new TypographyItem('h2', __('Heading 2'), 56),
            new TypographyItem('h3', __('Heading 3'), 44),
            new TypographyItem('h4', __('Heading 4'), 36),
            new TypographyItem('h5', __('Heading 5'), 30),
            new TypographyItem('h6', __('Heading 6'), 24),
            new TypographyItem('body', __('Body'), 16),
        ]);

    ThemeSupport::registerSocialLinks();
    ThemeSupport::registerSocialSharing();
    ThemeSupport::registerToastNotification();
    ThemeSupport::registerPreloader();
    ThemeSupport::registerSiteCopyright();
    ThemeSupport::registerDateFormatOption();
    ThemeSupport::registerLazyLoadImages();
    ThemeSupport::registerSiteLogoHeight(44);

    if (is_plugin_active('newsletter')) {
        Newsletter::registerNewsletterPopup();
    }

    add_filter('ads_locations', function (array $locations) {
        return [
            ...$locations,
            'header_before' => __('Header (before)'),
            'header_after' => __('Header (after)'),
            'footer_before' => __('Footer (before)'),
            'footer_after' => __('Footer (after)'),
            'listing_page_before' => __('Listing Page (before)'),
            'listing_page_after' => __('Listing Page (after)'),
            'detail_page_before' => __('Detail Page (before)'),
            'detail_page_after' => __('Detail Page (after)'),
            'detail_page_sidebar_before' => __('Detail Page Sidebar (before)'),
            'detail_page_sidebar_after' => __('Detail Page Sidebar (after)'),
            'blog_list_before' => __('Blog List (before)'),
            'blog_list_after' => __('Blog List (after)'),
            'blog_sidebar_before' => __('Blog Sidebar (before)'),
            'blog_sidebar_after' => __('Blog Sidebar (after)'),
            'post_detail_before' => __('Post Detail (before)'),
            'post_detail_after' => __('Post Detail (after)'),
        ];
    }, 128);

    Theme::addBodyAttributes(['class' => 'body counter-scroll']);

    RvMedia::addSize('medium-square', 400, 400)
        ->addSize('medium-rectangle-column', 400, 560)
        ->addSize('medium-rectangle', 400, 260);

    add_filter('theme_preloader_versions', function (array $versions): array {
        return [
            ...$versions,
            'v2' => __('Theme built-in'),
        ];
    }, 999);

    add_filter('theme_preloader', function (?string $html): ?string {
        if (theme_option('preloader_version', 'v1') === 'v2') {
            return $html . Theme::partial('preloader');
        }

        return $html;
    }, 999);

    if (is_plugin_active('real-estate')) {
        add_filter('theme_front_footer_content', function (?string $html): ?string {
            if (RealEstateHelper::isLoginEnabled() && theme_option('use_modal_for_authentication', true)) {
                $loginForm = LoginForm::create()
                    ->setFormOption('has_wrapper', 'no');

                $registerForm = null;

                if (RealEstateHelper::isRegisterEnabled()) {
                    $registerForm = RegisterForm::create()
                        ->columns()
                        ->when(! setting('real_estate_hide_username_in_registration_page', false), function (RegisterForm $form): void {
                            $form->modify('phone', PhoneNumberField::class, ['colspan' => 2]);
                        })
                        ->modify('agree_terms_and_policy', OnOffCheckboxField::class, ['colspan' => 2])
                        ->modify('login', HtmlField::class, [
                            'colspan' => 2,
                            'html' => sprintf(
                                '<div class="mt-3">%s <a href="%s" class="text-decoration-underline">%s</a></div>',
                                __('Already have an account?'),
                                route('public.account.login'),
                                __('Login')
                            ),
                        ])
                        ->setFormOption('has_wrapper', 'no');
                }

                $html .= Theme::partial(
                    'modal-authentication',
                    compact('loginForm', 'registerForm')
                );
            }

            if (theme_option('enabled_back_to_top', 'yes') === 'yes') {
                $html .= Theme::partial('go-to-top');
            }

            return $html;
        }, 999);

        add_filter('real_estate_property_status_html', function (?string $html, ?string $value = null): string {
            $color = match ($value) {
                PropertyStatusEnum::SELLING, PropertyStatusEnum::RENTING => 'primary',
                default => null,
            };

            return sprintf('<span class="flag-tag %s">%s</span>', $color, PropertyStatusEnum::getLabel($value));
        }, 999, 2);

        add_filter('real_estate_project_status_html', function (?string $html, string $value): string {
            $color = match ($value) {
                ProjectStatusEnum::SELLING => 'primary',
                default => null,
            };

            return sprintf('<span class="flag-tag %s">%s</span>', $color, ProjectStatusEnum::getLabel($value));
        }, 999, 2);

        CategoryForm::extend(function (CategoryForm $form): void {
            $form
                ->addAfter(
                    'is_default',
                    'icon',
                    CoreIconField::class,
                    CoreIconFieldOption::make()
                        ->label(__('Icon'))
                        ->metadata(),
                )
                ->addAfter(
                    'icon',
                    'icon_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Icon image'))
                        ->helperText(__('If icon image is set, it will be used instead of the icon above.'))
                        ->metadata()
                );
        });

        FormAbstract::extend(function (FormAbstract $form): void {
            if (! $form instanceof PropertyForm && ! $form instanceof ProjectForm) {
                return;
            }

            if ($form->has('video_url') || $form->has('video_thumbnail')) {
                return;
            }

            $form
                ->addAfter(
                    'unique_id',
                    'video_url',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Video URL'))
                        ->placeholder('https://youtu.be/xxxx')
                        ->helperText(__('Use the YouTube video link to be able to watch the video directly on the website.'))
                        ->metadata()
                )
                ->addAfter(
                    'video_url',
                    'video_thumbnail',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Video thumbnail'))
                        ->helperText(__('If you use the YouTube video link above, the thumbnail will be automatically obtained.'))
                        ->metadata()
                );
        });

        if (RealEstateHelper::isLoginEnabled()) {
            AccountPropertyForm::afterSaving(function (AccountPropertyForm $form): void {
                $request = $form->getRequest();

                $request->validate([
                    'video_url' => ['nullable', 'string', 'url', 'max:255'],
                    'video_thumbnail_input' => ['nullable', new MediaImageRule()],
                ]);

                /**
                 * @var Property $model
                 */
                $model = $form->setRequest($request)->getModel();

                $model->saveMetaDataFromFormRequest('video_thumbnail', $request);
            }, 175);

            AccountForm::extend(function (AccountForm $form) {
                return $form
                    ->addAfter(
                        'closeRow',
                        'social_links',
                        RepeaterField::class,
                        RepeaterFieldOption::make()
                            ->fields(Theme::getSocialLinksRepeaterFields())
                            ->label(__('Social links'))
                            ->metadata()
                            ->toArray()
                    );
            });
        }
    }

    if (is_plugin_active('contact')) {
        ContactForm::extend(function (ContactForm $form): void {
            $form
                ->setFormInputClass('form-control style-1')
                ->modify(
                    'submit',
                    'submit',
                    [
                        'attr' => ['class' => 'tf-btn primary size-1'],
                        'label' => __('Send Message'),
                    ]
                );
        });
    }

    PageForm::extend(function (PageForm $form): void {
        $form
            ->add(
                'breadcrumb',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Breadcrumb'))
                    ->metadata()
                    ->choices([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ]),
            )
            ->add(
                'breadcrumb_background_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(__('Breadcrumb background color'))
                    ->defaultValue(theme_option('breadcrumb_background_color', '#f7f7f7'))
                    ->metadata()
            )
            ->add(
                'breadcrumb_text_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(__('Breadcrumb text color'))
                    ->defaultValue(theme_option('breadcrumb_text_color', '#161e2d'))
                    ->metadata()
            )
            ->add(
                'breadcrumb_background_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Breadcrumb background image (1920x200px)'))
                    ->defaultValue(theme_option('breadcrumb_background_image'))
                    ->metadata()
            );
    });

    add_filter(THEME_FRONT_FOOTER, function (?string $html): string {
        if (AdminHelper::isInAdmin()) {
            return $html;
        }

        if (
            theme_option('facebook_comment_enabled_in_property', 'no') == 'yes'
            || theme_option('facebook_comment_enabled_in_project', 'no') == 'yes'
        ) {
            return $html . view('packages/theme::partials.facebook-integration')->render();
        }

        return $html;
    }, 120);

    add_filter(BASE_FILTER_PUBLIC_COMMENT_AREA, function ($html, ?BaseModel $model = null) {
        if (
            (theme_option('facebook_comment_enabled_in_property', 'yes') == 'yes' && $model instanceof Property) ||
            (theme_option('facebook_comment_enabled_in_project', 'yes') == 'yes' && $model instanceof Project)
        ) {
            return $html . view('packages/theme::partials.facebook-comments')->render();
        }

        return $html;
    }, 120, 2);
});
