<?php

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Newsletter\Forms\Fronts\NewsletterForm;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class NewsletterWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Newsletter form'),
            'description' => __('Display Newsletter form on sidebar'),
            'title' => null,
            'subtitle' => null,
        ]);
    }

    protected function data(): array|Collection
    {
        $form = NewsletterForm::create()
            ->formClass('mt-12')
            ->modify('wrapper_before', HtmlField::class, HtmlFieldOption::make()->content('<div class="mb-3 position-relative">'))
            ->addBefore(
                'email',
                'icon',
                HtmlField::class,
                HtmlFieldOption::make()->content('<span class="icon-left icon-mail"></span>')
            )
            ->modify('email', 'email', ['attr' => ['class' => '']])
            ->modify('submit', 'submit', [
                'attr' => ['class' => '', 'title' => __('Subscribe')],
                'label' => '<i class="icon icon-send"></i>',
            ])
            ->setFormEndKey('messages');

        return compact('form');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        $form = WidgetForm::createFromArray($this->getConfig())
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Title'))
                    ->toArray(),
            )
            ->add(
                'subtitle',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Subtitle'))
                    ->toArray(),
            );

        return $form;
    }

    protected function requiredPlugins(): array
    {
        return ['newsletter'];
    }
}
