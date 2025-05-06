@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::tab class="card-header-tabs">
                <x-core::tab.item
                    id="profile-tab"
                    :label="trans('plugins/real-estate::dashboard.sidebar_information')"
                    :is-active="true"
                />
                <x-core::tab.item
                    id="avatar-tab"
                    :label="trans('plugins/real-estate::dashboard.profile-picture')"
                />
                <x-core::tab.item
                    id="change-password-tab"
                    :label="trans('plugins/real-estate::dashboard.sidebar_change_password')"
                />
                {!! apply_filters('account_settings_register_content_tabs', null) !!}
            </x-core::tab>
        </x-core::card.header>

        <x-core::card.body>
            <x-core::tab.content>
                <x-core::tab.pane id="profile-tab" :is-active="true">
                    {!! $profileForm !!}
                </x-core::tab.pane>
                <x-core::tab.pane id="avatar-tab">
                    <x-core::crop-image
                        :label="trans('plugins/real-estate::dashboard.profile-picture')"
                        name="avatar_file"
                        :value="auth('account')->user()->avatar_url"
                        :action="route('public.account.avatar')"
                    />
                </x-core::tab.pane>
                <x-core::tab.pane id="change-password-tab">
                    {!! $changePasswordForm !!}
                </x-core::tab.pane>
                {!! apply_filters('account_settings_register_content_tab_inside', null) !!}
            </x-core::tab.content>
        </x-core::card.body>
    </x-core::card>
@stop

@push('scripts')
    {!! JsValidator::formRequest(\Botble\RealEstate\Http\Requests\SettingRequest::class) !!}
    {!! JsValidator::formRequest(\Botble\RealEstate\Http\Requests\UpdatePasswordRequest::class) !!}
@endpush
