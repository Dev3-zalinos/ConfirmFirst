@extends('core/base::forms.form')

@section('form_end')
    @if ($form->getModel()->id)
        <x-core::modal
            type="info"
            id="add-credit-modal"
            :title="__('Add credit to account')"
            button-id="confirm-add-credit-button"
            :button-label="__('Add')"
        >
            @include('plugins/real-estate::account.admin.credit-form', ['account' => $form->getModel()])
        </x-core::modal>
    @endif
@endsection
