<?php

use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SiteInformationWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Site information'),
            'description' => __('Widget display site information'),
            'about' => null,
        ]);
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'about',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('About'))
                    ->toArray()
            )
            ->add(
                'items',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label('Information')
                    ->fields([
                        [
                            'type' => 'coreIcon',
                            'label' => __('Icon'),
                            'attributes' => [
                                'name' => 'icon',
                                'value' => null,
                            ],
                        ],
                        [
                            'type' => 'textarea',
                            'label' => __('Text'),
                            'attributes' => [
                                'name' => 'text',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                    ])
            );
    }

    protected function data(): array|Collection
    {
        $items = collect($this->getConfig('items'))
            ->transform(fn (array $item): array => Arr::pluck($item, 'value', 'key'));

        return compact('items');
    }
}
