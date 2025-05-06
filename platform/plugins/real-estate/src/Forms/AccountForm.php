<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\AccountCreateRequest;
use Botble\RealEstate\Models\Account;
use Carbon\Carbon;

class AccountForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addStylesDirectly('vendor/core/plugins/real-estate/css/account-admin.css')
            ->addScriptsDirectly(['/vendor/core/plugins/real-estate/js/account-admin.js']);

        $this
            ->model(Account::class)
            ->setValidatorClass(AccountCreateRequest::class)
            ->template('plugins/real-estate::account.admin.form')
            ->add('first_name', 'text', [
                'label' => trans('plugins/real-estate::account.first_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.first_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('last_name', 'text', [
                'label' => trans('plugins/real-estate::account.last_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.last_name'),
                    'data-counter' => 120,
                ],
            ])
            ->add('username', 'text', [
                'label' => trans('plugins/real-estate::account.username'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.username_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('company', 'text', [
                'label' => trans('plugins/real-estate::account.company'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.company_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add('phone', 'text', [
                'label' => trans('plugins/real-estate::account.phone'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('dob', 'datePicker', [
                'label' => trans('plugins/real-estate::account.dob'),
                'default_value' => BaseHelper::formatDate(Carbon::now()),
            ])
            ->add('email', 'text', [
                'label' => trans('plugins/real-estate::account.form.email'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->when(is_plugin_active('location'), function (FormAbstract $form): void {
                $form->add(
                    'location_data',
                    SelectLocationField::class,
                    SelectLocationFieldOption::make()->toArray()
                );
            })
            ->add('is_change_password', 'onOff', [
                'label' => trans('plugins/real-estate::account.form.change_password'),
                'value' => 0,
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '#change-password',
                ],
            ])
            ->add('openRow', 'html', [
                'html' => '<div id="change-password" class="row"' . ($this->getModel()->id ? ' style="display: none"' : null) . '>',
            ])
            ->add('password', 'password', [
                'label' => trans('plugins/real-estate::account.form.password'),
                'required' => true,
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/real-estate::account.form.password_confirmation'),
                'required' => true,
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ' col-md-6',
                ],
            ])
            ->add('closeRow', 'html', [
                'html' => '</div>',
            ])
            ->add(
                'avatar_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/real-estate::dashboard.profile-picture'))
                    ->value($this->getModel()->avatar->url)
            )
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->when(! RealEstateHelper::isDisabledPublicProfile(), function (FormAbstract $form): void {
                $form
                    ->add('is_public_profile', 'onOff', [
                        'label' => trans('plugins/real-estate::account.form.is_public_profile'),
                        'default_value' => false,
                    ]);
            })
            ->when($this->getModel()->id && RealEstateHelper::isEnabledCreditsSystem(), function (FormAbstract $form): void {
                /**
                 * @var Account $account
                 */
                $account = $this->getModel();

                $form->addMetaBoxes([
                    'credits' => [
                        'attributes' => [
                            'id' => 'credit-histories',
                        ],
                        'title' => __('Transactions'),
                        'subtitle' => __('Credits: :count', ['count' => number_format($this->getModel()->credits)]),
                        'header_actions' => view(
                            'plugins/real-estate::partials.forms.header-actions.button',
                            [
                                'className' => 'btn-trigger-add-credit',
                                'label' => __('Manual Transaction'),
                            ]
                        )->render(),
                        'content' => view('plugins/real-estate::account.admin.credits', [
                            'account' => $account,
                            'transactions' => $account->transactions()->orderBy('created_at', 'DESC')->get(),
                        ])->render(),
                    ],
                ]);
            })
            ->setBreakFieldPoint('avatar_image');
    }
}
