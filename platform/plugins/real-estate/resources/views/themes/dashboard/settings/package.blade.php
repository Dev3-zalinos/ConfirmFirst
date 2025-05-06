@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <packages-component
        ajax-url="{{ route('public.account.ajax.packages') . (is_plugin_active('language') ? '?ref_lang=' . Language::getCurrentLocaleCode() : null) }}"
        subscribe-url="{{ route('public.account.ajax.package.subscribe') }}"
        v-slot="{ data, account, isLoading, isSubscribing, postSubscribe }"
        v-cloak
    >
        <x-core::alert class="current-package">
            {{ trans('plugins/real-estate::dashboard.your_credits') }}: <strong>@{{ account.formatted_credits }}</strong>
        </x-core::alert>

        <div class="packages-listing mb-3">
            <x-core::loading v-if="isLoading" />

            <template v-if="!isLoading && data.length && account">
                <div class="row flex-items-xs-middle flex-items-xs-center space-x-4">
                    <div
                        class="col-lg-3 col-md-6"
                        v-for="item in data"
                        :key="item.id"
                    >
                        <div class="box-package h-100 position-relative" :class="{ active: item.is_default }">
                            <div class="box-package-price d-flex align-items-end">
                                <h4 v-if="item.price">@{{ item.price_text }}</h4>
                                <h4 v-else>@{{ item.number_posts_free }}</h4>
                                <span class="text-muted" v-if="item.price">/@{{ item.number_of_listings }} {{ __('post(s)') }}</span>
                            </div>
                            <div class="ribbon ribbon-top ribbon-bookmark bg-green" v-if="item.percent_save_text">
                                @{{ item.percent_save_text }}
                            </div>
                            <div class="box-package-title">
                                @{{ item.name }}
                                <p v-if="item.description" class="text-muted desc">@{{ item.description }}</p>
                            </div>
                            <ul class="box-package-features">
                                <li class="item" v-for="(feature, index) in item.features" :key="index">
                                    <x-core::icon name="ti ti-check" />
                                    <span>@{{ feature }}</span>
                                </li>
                            </ul>

                            <div class="card-body text-center">
                                <div class="text-center mt-4">
                                    <x-core::button
                                        class="w-100"
                                        v-bind:class="isSubscribing && currentPackageId === item.id ? 'btn button-loading mt-2' : (item.is_default ? 'btn mt-2' : 'btn mt-2')"
                                        v-on:click="postSubscribe(item.id)"
                                        v-bind:disabled="isSubscribing"
                                    >
                                        {{ trans('plugins/real-estate::dashboard.purchase') }}
                                    </x-core::button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </packages-component>

    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/real-estate::dashboard.transactions_title') }}
            </x-core::card.title>
        </x-core::card.header>

        <payment-history-component
            ajax-url="{{ route('public.account.ajax.transactions') }}"
            v-slot="{ isLoading, isLoadingMore, data, getData }"
        >
            <x-core::loading v-if="isLoading" />

            <template v-else>
                <div class="empty" v-if="data.meta.total === 0">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-ghost" />
                    </div>
                    <p class="empty-title">
                        {{ trans('plugins/real-estate::dashboard.no_transactions_title') }}
                    </p>
                    <p class="empty-subtitle text-muted">
                        {{ trans('plugins/real-estate::dashboard.no_transactions') }}
                    </p>
                </div>

                <div v-if="data.meta.total !== 0" class="list-group list-group-flush">
                    <div v-for="item in data.data" :key="item.id" class="list-group-item">
                        <x-core::icon name="ti ti-clock" class="me-2" />
                        <span
                            :title="$sanitize(item.description, { allowedTags: [] })"
                            v-html="$sanitize(item.description)"
                        ></span>
                    </div>
                </div>

                <x-core::card.footer v-if="data.links.next">
                    <a href="javascript:void(0)" v-if="!isLoadingMore" @click="getData(data.links.next)">
                        {{  trans('plugins/real-estate::dashboard.load_more') }}
                    </a>
                    <a href="javascript:void(0)" v-if="isLoadingMore">
                        {{ trans('plugins/real-estate::dashboard.loading_more') }}
                    </a>
                </x-core::card.footer>
            </template>
        </payment-history-component>
    </x-core::card>
@endsection
