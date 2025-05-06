@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    {!! apply_filters(ACCOUNT_TOP_STATISTIC_FILTER, null) !!}

    <div class="mb-3 row row-cards">
        <div class="col-12 col-md-6 col-lg-4 dashboard-widget-item">
            <a class="overflow-hidden text-white rounded d-block position-relative text-decoration-none bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="px-4 py-3 details d-flex flex-column justify-content-between">
                        <div class="desc fw-medium">{{ trans('plugins/real-estate::dashboard.approved_properties') }}</div>
                        <div class="number fw-bolder">
                            {{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::APPROVED)->count() }}
                        </div>
                    </div>
                    <div class="pb-5 visual ps-1 position-absolute end-0">
                        <x-core::icon name="ti ti-circle-check" class="me-n2" style="opacity: 0.1;" />
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4 dashboard-widget-item">
            <a class="overflow-hidden text-white rounded d-block position-relative text-decoration-none bg-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="px-4 py-3 details d-flex flex-column justify-content-between">
                        <div class="desc fw-medium">{{ trans('plugins/real-estate::dashboard.pending_approve_properties') }}</div>
                        <div class="number fw-bolder">
                            {{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::PENDING)->count() }}
                        </div>
                    </div>
                    <div class="pb-5 visual ps-1 position-absolute end-0">
                        <x-core::icon name="ti ti-clock-hour-8" class="me-n2" style="opacity: 0.1;" />
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4 dashboard-widget-item">
            <a class="overflow-hidden text-white rounded d-block position-relative text-decoration-none bg-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="px-4 py-3 details d-flex flex-column justify-content-between">
                        <div class="desc fw-medium">{{ trans('plugins/real-estate::dashboard.rejected_properties') }}</div>
                        <div class="number fw-bolder">
                            {{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::REJECTED)->count() }}
                        </div>
                    </div>
                    <div class="pb-5 visual ps-1 position-absolute end-0">
                        <x-core::icon name="ti ti-edit" class="me-n2" style="opacity: 0.1;" />
                    </div>
                </div>
            </a>
        </div>
    </div>

    <activity-log-component ajax-url="{{ route('public.account.activity-logs') }}" v-slot="{ activityLogs, loading }">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    {{ trans('plugins/real-estate::dashboard.activity_logs') }}
                </h4>
            </div>
            <div class="card-body" style="min-height: 15rem" v-if="loading">
                <div class="loading-spinner"></div>
            </div>

            <template v-else>
                <div class="empty" v-if="(!activityLogs?.meta || activityLogs?.meta?.total === 0)">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-ghost" />
                    </div>
                    <p class="empty-title">
                        {{ trans('plugins/real-estate::dashboard.no_activity_logs_title') }}
                    </p>
                    <p class="empty-subtitle text-muted">
                        {{ trans('plugins/real-estate::dashboard.no_activity_logs') }}
                    </p>
                </div>

                <div v-if="activityLogs?.meta?.total !== 0" class="list-group list-group-flush">
                    <div v-for="activityLog in activityLogs.data" :key="activityLog.id" class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <x-core::icon name="ti ti-clock" />
                            </div>
                            <div class="col text-truncate">
                                <div class="text-reset d-block">
                                    <span :title="$sanitize(activityLog.description, { allowedTags: [] })" v-html="$sanitize(activityLog.description)"></span>
                                    <a :href="'https://whatismyipaddress.com/ip/' + activityLog.ip_address" target="_blank" :title="activityLog.ip_address">
                                        (@{{ activityLog.ip_address }})
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activityLogs?.links?.next" class="card-footer">
                    <a href="javascript:void(0)" v-if="!loading" @click="getActivityLogs(activityLogs.links.next)">
                        {{ trans('plugins/real-estate::dashboard.load_more') }}
                    </a>

                    <a href="javascript:void(0)" v-if="loading">{{ trans('plugins/real-estate::dashboard.loading_more') }}</a>
                </div>
            </template>
        </div>
    </activity-log-component>
@stop
