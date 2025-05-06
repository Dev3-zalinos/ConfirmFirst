<?php

namespace Botble\RealEstate\Forms\Fronts;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Forms\Fronts\Auth\Concerns\HasSubmitButton;
use Botble\RealEstate\Http\Requests\UpdatePasswordRequest;

class ChangePasswordForm extends FormAbstract
{
    use HasSubmitButton;

    public function setup(): void
    {
        $this
            ->model(BaseModel::class)
            ->setMethod('PUT')
            ->setValidatorClass(UpdatePasswordRequest::class)
            ->setFormOption('template', 'core/base::forms.form-content-only')
            ->setUrl(route('public.account.post.security'))
            ->add('old_password', 'password', [
                'label' => trans('plugins/real-estate::dashboard.current_password'),
            ])
            ->add('password', 'password', [
                'label' => trans('plugins/real-estate::dashboard.password_new'),
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/real-estate::dashboard.password_new_confirmation'),
            ])
            ->submitButton(trans('plugins/real-estate::dashboard.password_update_btn'), isWrapped: false, attributes: [
                'class' => 'btn btn-primary',
            ]);
    }
}
