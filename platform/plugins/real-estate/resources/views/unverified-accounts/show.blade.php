@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="warning">
        {{ trans('plugins/real-estate::account.unverified_account.moderate_alert_message') }}
    </x-core::alert>

    <div class="row flex-lg-row-reverse">
        <div class="col-md-4">
            <x-core::card class="mb-2">
                <x-core::card.body>
                    <div class="btn-list">
                        <x-core::button
                            type="button"
                            color="primary"
                            data-bs-toggle="modal"
                            data-bs-target="#approveModal"
                            icon="ti ti-check"
                        >
                            {{ trans('plugins/real-estate::account.unverified_account.approve') }}
                        </x-core::button>
                        <x-core::button
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal"
                            icon="ti ti-x"
                        >
                            {{ trans('plugins/real-estate::account.unverified_account.reject') }}
                        </x-core::button>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
        <div class="col-md-8">
            <x-core::card>
                <x-core::card.body>
                    <x-core::datagrid>
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('core/base::tables.name') }}</x-slot:title>
                            {{ $account->name }}
                        </x-core::datagrid.item>
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('plugins/real-estate::account.form.email') }}</x-slot:title>
                            {{ $account->email }}
                        </x-core::datagrid.item>
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('plugins/real-estate::account.phone') }}</x-slot:title>
                            {{ $account->phone }}
                        </x-core::datagrid.item>
                        @if($account->address)
                            <x-core::datagrid.item>
                                <x-slot:title>{{ trans('plugins/real-estate::account.address') }}</x-slot:title>
                                {{ $account->address }}
                            </x-core::datagrid.item>
                        @endif
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('plugins/real-estate::account.dob') }}</x-slot:title>
                            {{ BaseHelper::formatDate($account->dob) }}
                        </x-core::datagrid.item>
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('core/base::tables.created_at') }}</x-slot:title>
                            {{ BaseHelper::formatDateTime($account->created_at) }}
                        </x-core::datagrid.item>
                        <x-core::datagrid.item>
                            <x-slot:title>{{ trans('plugins/real-estate::account.credits') }}</x-slot:title>
                            {{ number_format($account->credits) }}
                        </x-core::datagrid.item>
                        @if($account->description)
                            <x-core::datagrid.item>
                                <x-slot:title>{{ trans('plugins/real-estate::account.description') }}</x-slot:title>
                                {{ $account->description }}
                            </x-core::datagrid.item>
                        @endif
                    </x-core::datagrid>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
@endsection

@push('footer')
    <x-core::modal.action
        id="approveModal"
        type="primary"
        :title="trans('plugins/real-estate::account.unverified_account.approve_modal.title')"
        :description="trans('plugins/real-estate::account.unverified_account.approve_modal.description')"
        :form-action="route('unverified-accounts.approve', $account->getKey())"
    >
        <x-slot:footer>
            <div class="w-100">
                <div class="row">
                    <div class="col">
                        <button type="submit" class="w-100 btn btn-primary">
                            {{ trans('plugins/real-estate::account.unverified_account.approve') }}
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            class="w-100 btn"
                            data-bs-dismiss="modal"
                        >
                            {{ trans('core/base::base.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </x-slot:footer>
    </x-core::modal.action>

    <x-core::modal.action
        id="rejectModal"
        type="danger"
        :title="trans('plugins/real-estate::account.unverified_account.reject_modal.title')"
        :form-action="route('unverified-accounts.reject', $account->getKey())"
    >
        <div class="text-muted text-break">
            {{ trans('plugins/real-estate::account.unverified_account.reject_modal.description') }}
        </div>

        <textarea name="reason" rows="3" class="form-control mt-3" required></textarea>

        <x-slot:footer>
            <div class="w-100">
                <div class="row">
                    <div class="col">
                        <button type="submit" class="w-100 btn btn-danger">
                            {{ trans('plugins/real-estate::account.unverified_account.reject') }}
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            class="w-100 btn"
                            data-bs-dismiss="modal"
                        >
                            {{ trans('core/base::base.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </x-slot:footer>
    </x-core::modal.action>
@endpush
