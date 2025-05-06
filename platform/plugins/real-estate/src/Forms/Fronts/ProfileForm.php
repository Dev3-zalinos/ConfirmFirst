<?php

namespace Botble\RealEstate\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\HiddenField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Supports\Language;
use Botble\RealEstate\Forms\AccountForm;
use Botble\RealEstate\Forms\Fronts\Auth\Concerns\HasSubmitButton;
use Botble\RealEstate\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\App;

class ProfileForm extends AccountForm
{
    use HasSubmitButton;

    public function setup(): void
    {
        parent::setup();

        $languages = Language::getAvailableLocales();

        $this
            ->setValidatorClass(SettingRequest::class)
            ->contentOnly()
            ->addBefore('slug', 'company', TextField::class, [
                'label' => trans('plugins/real-estate::account.company'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::account.company_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->modify('description', 'textarea', [
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->modify('email', 'text', [
                'required' => false,
                'attr' => [
                    'disabled' => true,
                ],
            ], true)
            ->modify('first_name', HiddenField::class)
            ->modify('last_name', HiddenField::class)
            ->modify('dob', HiddenField::class)
            ->remove([
                'is_change_password', 
                'password', 
                'password_confirmation', 
                'avatar_image', 
                'is_featured', 
                'is_public_profile',
                'gender'
            ])
            ->when(count($languages) > 1, function (FormAbstract $form) use ($languages): void {
                $languages = collect($languages)
                    ->pluck('name', 'locale')
                    ->map(fn ($item, $key) => $item . ' - ' . $key)
                    ->all();

                $form
                    ->add(
                        'locale',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Language'))
                            ->choices($languages)
                            ->selected($form->getModel()->getMetaData('locale', true) ?: App::getLocale())
                            ->metadata()
                    );
            })
            ->submitButton(trans('plugins/real-estate::dashboard.save'), isWrapped: false, attributes: [
                'class' => 'btn btn-primary',
            ]);
    }
}
