@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form :url="route('coupons.edit', $coupon)" method="post" class="coupon-form">
        @csrf

        @include('plugins/real-estate::coupons.partials.coupon-form')
    </x-core::form>
@endsection
