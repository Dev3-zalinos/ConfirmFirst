<header
    id="header"
    @class(['main-header', 'fixed-header' => theme_option('sticky_header_enabled', true), Theme::get('headerClass')])
>
    <div class="header-lower">
        <div class="row">
            <div class="col-lg-12">
                <div class="inner-container d-flex justify-content-between align-items-center">
                    <div class="logo-box">
                        <div class="logo">
                            <a href="{{ BaseHelper::getHomepageUrl() }}">
                                {{ Theme::getLogoImage(maxHeight: 44) }}
                            </a>
                        </div>
                    </div>
                    <div class="nav-outer">
                        <nav class="main-menu show navbar-expand-md">
                            <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                {!! Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'navigation clearfix'],
                                    'view' => 'main-menu',
                                ]) !!}
                            </div>
                        </nav>
                    </div>
                    <div class="header-account">
                       
                    </div>
                    <div class="mobile-nav-toggler mobile-button">
                        <x-core::icon name="ti ti-menu-2" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="close-btn">
        <x-core::icon name="ti ti-x" />
    </div>

    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <nav class="menu-box">
            <div class="nav-logo">
                <a href="{{ BaseHelper::getHomepageUrl() }}">
                    {{ Theme::getLogoImage(maxHeight: 44) }}
                </a>
            </div>
            <div class="bottom-canvas">
                @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                    @auth('account')
                        <div class="mb-3">
                            <a href="{{ route('public.account.dashboard') }}" class="d-flex gap-2 align-items-center">
                                {{ RvMedia::image(auth('account')->user()->avatar_url, auth('account')->user()->name, attributes: ['width' => 40, 'class' => 'rounded-circle']) }}
                                <span class="text-body-2 fw-semibold">{{ auth('account')->user()->name }}</span>
                            </a>
                        </div>
                    @else
                        <div class="login-box flex align-items-center">
                            <a
                                @if (theme_option('use_modal_for_authentication', true))
                                    href="#modalLogin"
                                data-bs-toggle="modal"
                                @else
                                    href="{{ route('public.account.login') }}"
                                @endif
                            >{{ __('Login') }}</a>
                            @if (RealEstateHelper::isRegisterEnabled())
                                <span>/</span>
                                <a
                                    @if (theme_option('use_modal_for_authentication', true))
                                        href="#modalRegister"
                                    data-bs-toggle="modal"
                                    @else
                                        href="{{ route('public.account.register') }}"
                                    @endif
                                >{{ __('Register') }}</a>
                            @endif
                        </div>
                    @endauth
                @endif

                    <div class="menu-outer"></div>

                @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
                    <div class="button-mobi-sell">
                    </div>
                @endif


                <div class="mobi-icon-box">
                    @if (is_plugin_active('real-estate'))
                        @if (RealEstateHelper::isEnabledWishlist())
                            <div class="box">
                                <a href="{{ route('public.wishlist') }}">
                                    {{ __('My Wishlist') }}
                                    (<span data-bb-toggle="wishlist-count" class="fw-medium">0</span>)
                                </a>
                            </div>
                        @endif

                        <div class="box">
                            {!! Theme::partial('currency-switcher') !!}
                        </div>
                    @endif

                    @if ($languageSwitcher = Theme::partial('language-switcher'))
                        <div class="box">
                            {!! $languageSwitcher !!}
                        </div>
                    @endif

                    @if($hotline = theme_option('hotline'))
                        <div class="box d-flex align-items-center">
                            <x-core::icon name="ti ti-phone" style="width: 1.25rem; height: 1.25rem" />
                            <div><a href="tel:{{ $hotline }}" title="{{ __('Phone') }}">{{ $hotline }}</a></div>
                        </div>
                    @endif
                    @if($email = theme_option('email'))
                        <div class="box d-flex align-items-center">
                            <x-core::icon name="ti ti-mail" style="width: 1.25rem; height: 1.25rem" />
                            <div><a href="mailto:{{ $email }}" title="{{ __('Email') }}">{{ $email }}</a></div>
                        </div>
                    @endif
                </div>
            </div>
        </nav>
    </div>
</header>
