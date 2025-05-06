<?php

namespace Botble\RealEstate\Providers;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\Form;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\TwigCompiler;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Language\Facades\Language;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\RealEstate\Enums\InvoiceStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Events\PaymentCompleted;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\Invoice;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\CouponService;
use Botble\RealEstate\Services\HandleFrontPages;
use Botble\RealEstate\Supports\InvoiceHelper;
use Botble\RealEstate\Supports\TwigExtension;
use Botble\RealEstate\Tables\PropertyTable;
use Botble\Slug\Models\Slug;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Http\Requests\UpdateOptionsRequest;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\ThemeOption\Fields\NumberField;
use Botble\Theme\ThemeOption\Fields\SelectField;
use Botble\Theme\ThemeOption\Fields\TextField;
use Botble\Theme\ThemeOption\ThemeOptionSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function (): void {
            add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 130);
            add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'getUnReadCount'], 130, 2);
            add_filter(BASE_FILTER_MENU_ITEMS_COUNT, [$this, 'getMenuItemCount'], 130);

            add_filter('cms_twig_compiler', function (TwigCompiler $twigCompiler) {
                if (! array_key_exists(TwigExtension::class, $twigCompiler->getExtensions())) {
                    $twigCompiler->addExtension(new TwigExtension());
                }

                return $twigCompiler;
            }, 130);

            Menu::addMenuOptionModel(Category::class);

            if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
                add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 13);
            }

            if (defined('PAYMENT_FILTER_PAYMENT_PARAMETERS')) {
                add_filter(PAYMENT_FILTER_PAYMENT_PARAMETERS, function ($html) {
                    if (! auth('account')->check()) {
                        return $html;
                    }

                    return $html . Form::hidden('customer_id', auth('account')->id())->toHtml() .
                        Form::hidden('customer_type', Account::class)->toHtml();
                }, 123);
            }

            if (defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
                add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function ($data): void {
                    $payment = PaymentHelper::storeLocalPayment($data);

                    InvoiceHelper::store([
                        ...$data,
                        'discount_amount' => Session::get('coupon_discount_amount', 0),
                        'coupon_code' => Session::get('applied_coupon_code'),
                    ]);

                    if ($payment instanceof Model) {
                        MetaBox::saveMetaBoxData($payment, 'subscribed_packaged_id', session('subscribed_packaged_id'));
                    }

                    $this->app->make(CouponService::class)->forgotCouponSession();
                }, 123);

                add_action(BASE_ACTION_META_BOXES, function ($context, $payment): void {
                    if (get_class($payment) == Payment::class && $context == 'advanced' && Route::currentRouteName() == 'payments.show') {
                        MetaBox::addMetaBox('additional_payment_data', __('Package information'), function () use ($payment) {
                            $subscribedPackageId = MetaBox::getMetaData($payment, 'subscribed_packaged_id', true);

                            $package = Package::query()->find($subscribedPackageId);

                            if (! $package) {
                                return null;
                            }

                            return view('plugins/real-estate::partials.payment-extras', compact('package'));
                        }, get_class($payment), $context);
                    }
                }, 128, 2);
            }

            if (defined('PAYMENT_FILTER_REDIRECT_URL')) {
                add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken) {
                    $checkoutToken = $checkoutToken ?: session('subscribed_packaged_id');

                    if (! $checkoutToken) {
                        return route('public.index');
                    }

                    if (str_contains($checkoutToken, url(''))) {
                        return $checkoutToken;
                    }

                    return route('public.account.package.subscribe.callback', $checkoutToken);
                }, 123);
            }

            if (defined('PAYMENT_FILTER_CANCEL_URL')) {
                add_filter(PAYMENT_FILTER_CANCEL_URL, function ($checkoutToken) {
                    $checkoutToken = $checkoutToken ?: session('subscribed_packaged_id');

                    if (! $checkoutToken) {
                        return route('public.index');
                    }

                    if (str_contains($checkoutToken, url(''))) {
                        return $checkoutToken;
                    }

                    return route('public.account.package.subscribe', $checkoutToken) . '?' . http_build_query(['error' => true, 'error_type' => 'payment']);
                }, 123);
            }

            if (defined('ACTION_AFTER_UPDATE_PAYMENT')) {
                add_action(ACTION_AFTER_UPDATE_PAYMENT, function ($request, $payment): void {
                    if ($request->input('status') == PaymentStatusEnum::COMPLETED) {
                        PaymentCompleted::dispatch($payment);
                    }

                    if (in_array($payment->payment_channel, [PaymentMethodEnum::COD, PaymentMethodEnum::BANK_TRANSFER])
                        && $request->input('status') == PaymentStatusEnum::COMPLETED
                    ) {
                        $subscribedPackageId = MetaBox::getMetaData($payment, 'subscribed_packaged_id', true);

                        if (! $subscribedPackageId) {
                            return;
                        }

                        $package = Package::query()->find($subscribedPackageId);

                        if (! $package) {
                            return;
                        }

                        /**
                         * @var Account $account
                         */
                        $account = Account::query()->find($payment->customer_id);

                        if (! $account) {
                            return;
                        }

                        if ($payment->status == PaymentStatusEnum::PENDING) {
                            $account->credits += $package->number_of_listings;
                            $account->save();

                            $account->packages()->attach($package);
                        }

                        $payment->status = PaymentStatusEnum::COMPLETED;

                        Invoice::query()
                            ->where('reference_id', $package->getKey())
                            ->where('reference_type', Package::class)
                            ->update(['status' => InvoiceStatusEnum::COMPLETED]);
                    }
                }, 123, 2);
            }

            if (defined('PAYMENT_FILTER_PAYMENT_DATA')) {
                add_filter(PAYMENT_FILTER_PAYMENT_DATA, function (array $data, Request $request) {
                    $orderIds = [session('subscribed_packaged_id')];

                    $package = Package::query()->whereIn('id', $orderIds)->first();

                    if (! $package) {
                        return $data;
                    }

                    $discountAmount = 0;

                    $couponService = $this->app->make(CouponService::class);

                    if (Session::has('applied_coupon_code')) {
                        $coupon = $couponService->getCouponByCode(Session::get('applied_coupon_code'));

                        if ($coupon) {
                            $discountAmount = $couponService->getDiscountAmount(
                                $coupon->type->getValue(),
                                $coupon->value,
                                $package->price
                            );

                            $coupon->increment('total_used');
                        }
                    }

                    $price = $couponService->getAmountAfterDiscount($discountAmount, $package->price);

                    $products = [
                        [
                            'id' => $package->id,
                            'name' => $package->name,
                            'price' => $this->convertOrderAmount($package->price - $discountAmount),
                            'price_per_order' => $this->convertOrderAmount($package->price - $discountAmount),
                            'qty' => 1,
                        ],
                    ];

                    /**
                     * @var Account $account
                     */
                    $account = auth('account')->user();

                    $address = [
                        'name' => $account->name,
                        'email' => $account->email,
                        'phone' => $account->phone,
                        'country' => null,
                        'state' => null,
                        'city' => null,
                        'address' => null,
                        'zip' => null,
                    ];

                    return [
                        'amount' => $this->convertOrderAmount($price),
                        'email' => $account->email,
                        'shipping_amount' => 0,
                        'shipping_method' => null,
                        'tax_amount' => 0,
                        'currency' => strtoupper(get_application_currency()->title),
                        'order_id' => $orderIds,
                        'description' => trans('plugins/payment::payment.payment_description', ['order_id' => Arr::first($orderIds), 'site_url' => request()->getHost()]),
                        'customer_id' => $account->getKey(),
                        'customer_type' => Account::class,
                        'return_url' => $request->input('return_url'),
                        'callback_url' => $request->input('callback_url'),
                        'products' => $products,
                        'orders' => [$package],
                        'address' => $address,
                        'checkout_token' => session('subscribed_packaged_id'),
                    ];
                }, 120, 2);
            }

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets) {
                foreach ($widgets as $key => $widget) {
                    if (in_array($key, [
                        'widget_total_themes',
                        'widget_total_users',
                        'widget_total_plugins',
                        'widget_total_pages',
                    ]) && $widget['type'] == 'stats') {
                        Arr::forget($widgets, $key);
                    }
                }

                return $widgets;
            }, 150);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = Property::query()
                    ->active()
                    ->count();

                return (new DashboardWidgetInstance())
                    ->setType('stats')
                    ->setPermission('property.index')
                    ->setTitle(trans('plugins/real-estate::property.active_properties'))
                    ->setKey('widget_total_1')
                    ->setIcon('ti ti-briefcase')
                    ->setColor('#8e44ad')
                    ->setStatsTotal($items)
                    ->setRoute(route('property.index', [
                        'filter_table_id' => strtolower(Str::slug(Str::snake(PropertyTable::class))),
                        'class' => PropertyTable::class,
                        'filter_columns' => [
                            'status',
                        ],
                        'filter_operators' => [
                            '=',
                        ],
                        'filter_values' => [
                            'active',
                        ],
                    ]))
                    ->setColumn('col-12 col-md-6 col-lg-3')
                    ->init($widgets, $widgetSettings);
            }, 2, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = Property::query()
                    ->notExpired()
                    ->where('moderation_status', ModerationStatusEnum::PENDING)
                    ->count();

                return (new DashboardWidgetInstance())
                    ->setType('stats')
                    ->setPermission('property.index')
                    ->setTitle(trans('plugins/real-estate::property.pending_properties'))
                    ->setKey('widget_total_2')
                    ->setIcon('ti ti-briefcase')
                    ->setColor('#32c5d2')
                    ->setStatsTotal($items)
                    ->setRoute(route('property.index', [
                        'filter_table_id' => strtolower(Str::slug(Str::snake(PropertyTable::class))),
                        'class' => PropertyTable::class,
                        'filter_columns' => [
                            'moderation_status',
                        ],
                        'filter_operators' => [
                            '=',
                        ],
                        'filter_values' => [
                            ModerationStatusEnum::PENDING,
                        ],
                    ]))
                    ->setColumn('col-12 col-md-6 col-lg-3')
                    ->init($widgets, $widgetSettings);
            }, 3, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = Property::query()
                    ->expired()
                    ->count();

                return (new DashboardWidgetInstance())
                    ->setType('stats')
                    ->setPermission('property.index')
                    ->setTitle(trans('plugins/real-estate::property.expired_properties'))
                    ->setKey('widget_total_3')
                    ->setIcon('ti ti-briefcase')
                    ->setColor('#e7505a')
                    ->setStatsTotal($items)
                    ->setRoute(route('property.index', [
                        'filter_table_id' => strtolower(Str::slug(Str::snake(PropertyTable::class))),
                        'class' => PropertyTable::class,
                        'filter_columns' => [
                            'status',
                        ],
                        'filter_operators' => [
                            '=',
                        ],
                        'filter_values' => [
                            'expired',
                        ],
                    ]))
                    ->setColumn('col-12 col-md-6 col-lg-3')
                    ->init($widgets, $widgetSettings);
            }, 4, 2);

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, function ($widgets, $widgetSettings) {
                $items = Account::query()->count();

                return (new DashboardWidgetInstance())
                    ->setType('stats')
                    ->setPermission('account.index')
                    ->setTitle(trans('plugins/real-estate::account.agents'))
                    ->setKey('widget_total_4')
                    ->setIcon('fas fa-users')
                    ->setColor('#3598dc')
                    ->setStatsTotal($items)
                    ->setRoute(route('account.index'))
                    ->setColumn('col-12 col-md-6 col-lg-3')
                    ->init($widgets, $widgetSettings);
            }, 5, 2);

            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                add_action(BASE_ACTION_META_BOXES, [$this, 'addLanguageChooser'], 55, 2);
            }

            add_filter('social_login_before_saving_account', function ($data, $oAuth, $providerData) {
                if (Arr::get($providerData, 'model') == Account::class && Arr::get($providerData, 'guard') == 'account') {
                    $firstName = implode(' ', explode(' ', $oAuth->getName(), -1));
                    Arr::forget($data, 'name');
                    $data = array_merge($data, [
                        'first_name' => $firstName,
                        'last_name' => $lastName = trim(str_replace($firstName, '', $oAuth->getName())),
                        'username' => Account::generateUsername(
                            ($nickName = $oAuth->getNickname()) ?: $firstName,
                            $nickName ? '' : $lastName
                        ),
                    ]);
                }

                return $data;
            }, 49, 3);

            add_filter('social_login_before_creating_account', function ($data) {
                if (! RealEstateHelper::isRegisterEnabled()) {
                    return (new BaseHttpResponse())
                        ->setError()
                        ->setMessage(trans('auth.failed'));
                }

                return $data;
            }, 49);

            if (is_plugin_active('language') && is_plugin_active('language-advanced')) {
                add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
                    if (is_in_admin() &&
                        request()->segment(1) === 'account' &&
                        Auth::guard('account')->check() &&
                        Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
                        $data &&
                        $data->id &&
                        LanguageAdvancedManager::isSupported($data)
                    ) {
                        $refLang = null;

                        if (Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode()) {
                            $refLang = '?ref_lang=' . Language::getCurrentAdminLocaleCode();
                        }

                        $form->setFormOption(
                            'url',
                            route('public.account.language-advanced.save', $data->id) . $refLang
                        );
                    }

                    return $form;
                }, 9999, 2);
            }

            add_filter('account_dashboard_header', function ($html) {
                $customCSSFile = public_path(Theme::path() . '/css/style.integration.css');
                if (File::exists($customCSSFile)) {
                    $html .= Html::style(Theme::asset()
                        ->url('css/style.integration.css?v=' . filectime($customCSSFile)));
                }

                return $html . ThemeSupport::getCustomJS('header');
            }, 15);

            if (defined('PAGE_MODULE_SCREEN_NAME')) {
                add_filter(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, function (?string $name, Page $page) {
                    $subTitle = null;

                    switch ($page->getKey()) {
                        case theme_option('properties_list_page_id'):
                            $subTitle = __('Properties List');

                            break;
                        case theme_option('projects_list_page_id'):
                            if (RealEstateHelper::isEnabledProjects()) {
                                $subTitle = __('Projects List');
                            }

                            break;
                    }

                    if (! $subTitle) {
                        return $name;
                    }

                    $subTitle = Html::tag('span', $subTitle, ['class' => 'additional-page-name'])
                        ->toHtml();

                    if (Str::contains($name, ' —')) {
                        return $name . ', ' . $subTitle;
                    }

                    return $name . ' —' . $subTitle;
                }, 124, 2);
            }

            add_filter('core_request_rules', function (array $rules, Request $request): array {
                if (! $request instanceof UpdateOptionsRequest) {
                    return $rules;
                }

                $fields = $request->collect()->filter(function ($value, $key) {
                    return Str::startsWith($key, 'real_estate_') && Str::endsWith($key, '_page_slug');
                });

                if (empty($fields)) {
                    return $rules;
                }

                $locale = is_plugin_active('language') && Language::getRefLang() ? Language::getRefLang() : null;
                $themeName = Theme::getThemeName();

                $themeOptions = collect(ThemeOption::getOptions())
                    ->filter(
                        function ($value, $key) use ($themeName, $locale) {
                            $prefix = sprintf('theme-%s-real-estate', $themeName);

                            if ($locale) {
                                $prefix = sprintf('theme-%s-%s-real-estate', $themeName, $locale);
                            }

                            return Str::startsWith($key, $prefix) && Str::endsWith($key, '_page_slug');
                        }
                    );

                $rules = $fields->mapWithKeys(fn ($value, $key) => [$key => ['nullable', 'string']])->all();

                foreach ($fields as $key => $value) {
                    $rules[$key][] = function ($attribute, $value, $fail) use ($locale, $fields, $key, $themeOptions): void {
                        if (
                            collect($fields)->reject(fn ($v, $k) => $k === $key)->contains($value)
                            || $themeOptions
                                ->reject(fn ($value, $k) => $k === ThemeOption::getOptionKey($key, $locale))
                                ->contains($value)
                        ) {
                            $fail(trans('plugins/real-estate::real-estate.theme_options.page_slug_already_exists', [
                                'slug' => $value,
                            ]));
                        }
                    };
                }

                return $rules;
            }, 999, 2);

            add_filter('core_slug_can_be_reviewed', function (bool $canBeReviewed) {
                return $canBeReviewed || (auth('account')->check() && AdminHelper::isInAdmin());
            }, 999, 2);

            $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
                $pages = Page::query()->wherePublished()->pluck('name', 'id')->all();

                ThemeOption::setSection(
                    ThemeOptionSection::make('opt-text-subsection-real-estate')
                        ->icon('ti ti-briefcase')
                        ->title(__('Real Estate'))
                        ->description(__('Theme options for Real Estate'))
                        ->fields([
                            SelectField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('projects_list_page_id')
                                ->options(['' => __('-- Select --')] + $pages)
                                ->label(__('Projects List page')),
                            SelectField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('properties_list_page_id')
                                ->options(['' => __('-- Select --')] + $pages)
                                ->label(__('Properties List page')),
                            NumberField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('number_of_projects_per_page')
                                ->defaultValue(12)
                                ->label(__('Number of projects per page')),
                            NumberField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('number_of_properties_per_page')
                                ->defaultValue(15)
                                ->label(__('Number of properties per page')),
                            NumberField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('number_of_related_projects')
                                ->defaultValue(8)
                                ->label(__('Number of related projects')),
                            NumberField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('number_of_related_properties')
                                ->defaultValue(8)
                                ->label(__('Number of related properties')),
                            TextField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('latitude_longitude_center_on_properties_page')
                                ->defaultValue('43.615134, -76.393186')
                                ->label(__('Latitude longitude center on properties page')),
                            TextField::make()
                                ->sectionId('opt-text-subsection-real-estate')
                                ->name('term_and_privacy_policy_url')
                                ->label(__('Term and Privacy Policy URL')),
                        ])
                )
                    ->setSection(
                        ThemeOptionSection::make('opt-text-subsection-real-estate-slug')
                            ->title(trans('plugins/real-estate::real-estate.theme_options.slug_name'))
                            ->description(trans('plugins/real-estate::real-estate.theme_options.slug_description'))
                            ->icon('ti ti-link')
                            ->fields(
                                collect(RealEstateHelper::getDefaultPageSlug())
                                    ->map(fn ($value, $key) => [
                                        'id' => sprintf('real_estate_%s_page_slug', $key),
                                        'type' => 'text',
                                        'label' => trans(
                                            'plugins/real-estate::real-estate.theme_options.page_slug_name',
                                            [
                                                'page' => trans(
                                                    "plugins/real-estate::real-estate.theme_options.page_slugs.$key"
                                                ),
                                            ]
                                        ),
                                        'attributes' => [
                                            'name' => sprintf('real_estate_%s_page_slug', $key),
                                            'value' => $value,
                                            'options' => [
                                                'class' => 'form-control',
                                            ],
                                        ],
                                        'helper' => trans(
                                            'plugins/real-estate::real-estate.theme_options.page_slug_description',
                                            [
                                                'slug' => Html::link(
                                                    url(
                                                        apply_filters(
                                                            FILTER_SLUG_PREFIX,
                                                            RealEstateHelper::getPageSlug($key)
                                                        )
                                                    ),
                                                    attributes: ['target' => '_blank']
                                                ),
                                                'default' => "<code>$value</code>",
                                            ]
                                        ),
                                    ])
                                    ->all()
                            )
                    );
            });

            add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 30);

            if (defined('PAGE_MODULE_SCREEN_NAME')) {
                add_filter(PAGE_FILTER_FRONT_PAGE_CONTENT, function (?string $content, Page $page) {
                    if ($content && (str_contains($content, '[properties-list ') || str_contains($content, '[projects-list '))) {
                        return $content;
                    }

                    if ($page->getKey() == theme_option('projects_list_page_id')) {
                        $projects = RealEstateHelper::getProjectsFilter((int) theme_option('number_of_projects_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

                        $view = Theme::getThemeNamespace() . '::views.real-estate.projects';

                        if (! view()->exists($view)) {
                            $view = 'plugins/real-estate::themes.projects';
                        }

                        return view($view, compact('projects'))->render();
                    }

                    if ($page->getKey() == theme_option('properties_list_page_id')) {
                        $properties = RealEstateHelper::getPropertiesFilter((int) theme_option('number_of_properties_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

                        $view = Theme::getThemeNamespace() . '::views.real-estate.properties';

                        if (! view()->exists($view)) {
                            $view = 'plugins/real-estate::themes.properties';
                        }

                        return view($view, compact('properties'))->render();
                    }

                    return $content;
                }, 2, 2);
            }

            add_action(
                BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION,
                function (Request $request, Model|string|null $data = null): void {
                    if (! setting('verify_account_email', false)) {
                        return;
                    }

                    if (! $data instanceof Account || Route::currentRouteName() !== 'account.edit') {
                        return;
                    }

                    if (Auth::user()->hasPermission('account.edit')) {
                        echo view(
                            'plugins/real-estate::account.admin.notification',
                            compact('data')
                        )->render();
                    }
                },
                45,
                2
            );
        });
    }

    protected function convertOrderAmount(float $amount): float
    {
        $currentCurrency = get_application_currency();

        if ($currentCurrency->is_default) {
            return $amount;
        }

        return (float) format_price($amount * $currentCurrency->exchange_rate, $currentCurrency, true);
    }

    public function registerTopHeaderNotification(?string $options): ?string
    {
        if (Auth::user()->hasPermission('consults.edit')) {
            $consults = Consult::query()
                ->where('status', ConsultStatusEnum::UNREAD)
                ->select(['id', 'name', 'email', 'phone', 'created_at'])->latest()
                ->paginate(10);

            if ($consults->count() == 0) {
                return $options;
            }

            return $options . view('plugins/real-estate::notification', compact('consults'))->render();
        }

        return $options;
    }

    public function getUnReadCount(?string $number, string $menuId): ?string
    {
        switch ($menuId) {
            case 'cms-plugins-consult':
                return view('core/base::partials.navbar.badge-count', ['class' => 'unread-consults'])->render();
            case 'cms-plugins-real-estate-unverified-accounts':
            case 'cms-plugins-real-estate-accounts':
                if (! Auth::user()->hasPermission('unverified-accounts.index')) {
                    return $number;
                }

                if (! setting('real_estate_enable_account_verification', false)) {
                    return $number;
                }

                return view('core/base::partials.navbar.badge-count', ['class' => 'unverified-accounts'])->render();
        }

        return $number;
    }

    public function getMenuItemCount(array $data = []): array
    {
        if (Auth::user()->hasPermission('consult.index')) {
            $data[] = [
                'key' => 'unread-consults',
                'value' => Consult::query()->where('status', ConsultStatusEnum::UNREAD)->count(),
            ];
        }

        if (Auth::user()->hasPermission('unverified-accounts.index')) {
            $data[] = [
                'key' => 'unverified-accounts',
                'value' => Account::query()->whereNull('approved_at')->count(),
            ];
        }

        return $data;
    }

    public function registerMenuOptions(): void
    {
        if (Auth::user()->hasPermission('property_category.index')) {
            Menu::registerMenuOptions(Category::class, trans('plugins/real-estate::category.menu'));
        }
    }

    public function addLanguageChooser(string $priority, ?Model $model): void
    {
        if ($priority == 'head' && $model instanceof Category) {
            $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);

            if ($languages->count() < 2) {
                return;
            }

            echo view('plugins/language::partials.admin-list-language-chooser', [
                'route' => 'property_category.index',
                'languages' => $languages,
            ])->render();
        }
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new HandleFrontPages())->handle($slug);
    }
}
