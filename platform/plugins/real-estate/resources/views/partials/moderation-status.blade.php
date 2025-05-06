@if ($model->is_pending_moderation)
    <div class="btn-list">
        <x-core::button type="button" color="success" icon="ti ti-check" size="sm" data-bs-toggle="modal" data-bs-target="#approve-property-modal">
            {{ trans('plugins/real-estate::property.status_moderation.approve') }}
        </x-core::button>
        <x-core::button type="button" color="danger" icon="ti ti-x" size="sm" data-bs-toggle="modal" data-bs-target="#reject-property-modal">
            {{ trans('plugins/real-estate::property.status_moderation.reject') }}
        </x-core::button>
    </div>
@else
    {!! BaseHelper::clean($model->moderation_status->toHtml()) !!}
    @if($model->moderation_status == \Botble\RealEstate\Enums\ModerationStatusEnum::REJECTED)
        <p class="mt-2 mb-0">
            <span class="text-muted">{{ trans('plugins/real-estate::property.status_moderation.reason_rejected') }}: </span>
            <strong>{{ $model->reject_reason }}</strong>
        </p>
    @endif
@endif
