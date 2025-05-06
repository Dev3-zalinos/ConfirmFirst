@php
    $announcements = apply_filters('announcement_display_html', null);
    $canRenderAnnouncements = is_plugin_active('announcement') && $announcements && \ArchiElite\Announcement\Models\Announcement::query()->exists();
@endphp

<div class="top-header">
    <div class="top-header-left">
        @if($canRenderAnnouncements)
            {!! $announcements !!}
        @else
            @if($hotline = theme_option('hotline'))
                <div class="top-header-item">
                    <x-core::icon name="ti ti-phone" style="width: 1.25rem; height: 1.25rem" />
                    <a href="tel:{{ $hotline }}">{{ $hotline }}</a>
                </div>
            @endif
            @if($email = theme_option('email'))
                <div class="top-header-item">
                    <x-core::icon name="ti ti-mail" style="width: 1.25rem; height: 1.25rem" />
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </div>
            @endif
        @endif
    </div>

    <div class="top-header-right">
        @if (is_plugin_active('real-estate'))
            @if (RealEstateHelper::isEnabledWishlist())
                <a href="{{ route('public.wishlist') }}" class="my-wishlist-link">
                    {{ __('My Wishlist') }}
                    (<span data-bb-toggle="wishlist-count" class="fw-medium">0</span>)
                </a>
            @endif

            {!! Theme::partial('currency-switcher') !!}
        @endif

        {!! Theme::partial('language-switcher') !!}

        @if (is_plugin_active('real-estate') && RealEstateHelper::isLoginEnabled())
            @auth('account')
                <a href="{{ route('public.account.dashboard') }}" class="d-flex gap-2 align-items-center me-3">
                    {{ RvMedia::image(auth('account')->user()->avatar_url, auth('account')->user()->name, attributes: ['class' => 'rounded-circle', 'style' => 'width: 22px']) }}
                    <span class="text-body-2 fw-semibold">{{ auth('account')->user()->name }}</span>
                </a>
            @else
                <div class="register">
                    <ul class="d-flex">
                        <li>
                            <a
                                @if(theme_option('use_modal_for_authentication', true))
                                    href="#modalLogin"
                                    data-bs-toggle="modal"
                                @else
                                    href="{{ route('public.account.login') }}"
                                @endif
                            >
                                {{ __('Login') }}
                            </a>
                        </li>
                        @if (RealEstateHelper::isRegisterEnabled())
                            <li>/</li>
                            <li>
                                <a
                                    @if(theme_option('use_modal_for_authentication', true))
                                        href="#modalRegister"
                                    data-bs-toggle="modal"
                                    @else
                                        href="{{ route('public.account.register') }}"
                                    @endif
                                >
                                    {{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endauth
        @endif
    </div>
</div>
