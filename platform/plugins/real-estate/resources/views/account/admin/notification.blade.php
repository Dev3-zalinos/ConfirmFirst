@if (! $data->confirmed_at)
    <x-core::alert
        type="warning"
    >
        {!! BaseHelper::clean(
        trans('plugins/real-estate::account.verify_email.notification', [
            'approve_link' => Html::link(
                route('account.verify-email', $data->id),
                trans('plugins/real-estate::account.verify_email.approve_here'),
                ['class' => 'verify-account-email-button'],
            ),
        ])) !!}
    </x-core::alert>

    @push('footer')
        <x-core::modal
            id="verify-account-email-modal"
            type="warning"
            :title="trans('plugins/real-estate::account.verify_email.confirm_heading')"
            button-id="confirm-verify-account-email-button"
            :button-label="trans('plugins/real-estate::account.verify_email.confirm_button')"
        >
            {!! trans('plugins/real-estate::account.verify_email.confirm_description') !!}
        </x-core::modal>
    @endpush
@endif
