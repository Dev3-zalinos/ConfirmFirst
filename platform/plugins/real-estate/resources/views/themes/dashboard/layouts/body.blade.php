<header class="header--mobile">
    <div class="header__left">
        <button class="navbar-toggler">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="header__center">
        @include('plugins/real-estate::themes.dashboard.layouts.logo', ['cssClass' => 'ps-logo'])
    </div>
    <div class="header__right">
        <a
            href="{{ route('public.account.logout') }}"
            title="{{ trans('plugins/real-estate::dashboard.header_logout_link') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        >
            <x-core::icon name="ti ti-logout" />
        </a>

        <form id="logout-form" style="display: none;" action="{{ route('public.account.logout') }}" method="POST">
            @csrf
        </form>
    </div>
</header>
<aside class="ps-drawer--mobile">
    <div class="ps-drawer__header py-3">
        <h4 class="fs-3 mb-0">Menu</h4>
        <button class="ps-drawer__close">
            <x-core::icon name="ti ti-x" />
        </button>
    </div>
    <div class="ps-drawer__content">
        @include('plugins/real-estate::themes.dashboard.layouts.menu')
    </div>
</aside>

<div class="ps-site-overlay"></div>

<main class="ps-main">
    <div class="ps-main__sidebar">
        <div class="ps-sidebar">
            <div class="ps-sidebar__top">
                {!! apply_filters('real_estate_account_dashboard_sidebar_top_before', null) !!}

                @include(apply_filters('real_estate_account_dashboard_sidebar_top', 'plugins/real-estate::themes.dashboard.layouts.sidebar-top'))

                {!! apply_filters('real_estate_account_dashboard_sidebar_top_after', null) !!}
            </div>
            <div class="ps-sidebar__content">
                <div class="ps-sidebar__center">
                    @include('plugins/real-estate::themes.dashboard.layouts.menu')
                </div>
                <div class="ps-sidebar__footer">
                    <div class="ps-copyright">
                        @include('plugins/real-estate::themes.dashboard.layouts.logo')

                        <p>{!! Theme::getSiteCopyright() !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="ps-main__wrapper"
        id="vendor-dashboard"
    >
        <header class="d-sm-flex justify-content-between align-items-center mb-3">
            <h3 class="fs-1">{{ PageTitle::getTitle(false) }}</h3>

            <div class="d-flex align-items-center gap-4">
                {!! apply_filters('real_estate_account_dashboard_header_buttons_before', null) !!}

                <a href="{{ route('public.index') }}" target="_blank" class="text-uppercase">
                    {{ __('Go to homepage') }}
                    <x-core::icon name="ti ti-arrow-right" />
                </a>

                {!! apply_filters('real_estate_account_dashboard_header_buttons_after', null) !!}
            </div>
        </header>

        <div id="app">
            @if (auth('account')->check() && !auth('account')->user()->canPost())
                <x-core::alert :title="trans('plugins/real-estate::package.add_credit_warning')">
                    <a href="{{ route('public.account.packages') }}">
                        {{ trans('plugins/real-estate::package.add_credit') }}
                    </a>
                </x-core::alert>
            @endif

            @yield('content')
        </div>
    </div>
</main>
