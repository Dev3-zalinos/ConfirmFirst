@php
    $layout = 'plugins/real-estate::themes.dashboard.layouts.master';
@endphp

@extends('core/table::table')

@section('content')
    @parent
@stop

@push('footer')
    <x-core::modal.action
        class="modal-confirm-renew"
        :title="__('Renew confirmation')"
        :description="(RealEstateHelper::isEnabledCreditsSystem()
            ? __('Are you sure you want to renew this property, it will takes 1 credit from your credits')
            : __('Are you sure you want to renew this property')) . '?'"
        :submit-button-label="__('Yes')"
        :submit-button-attrs="['class' => 'button-confirm-renew']"
    />
@endpush
