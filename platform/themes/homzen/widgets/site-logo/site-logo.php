<?php

use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;

class SiteLogoWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Site logo'),
            'description' => __('Widget display site logo'),
        ]);
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'alert',
                AlertField::class,
                AlertFieldOption::make()
                    ->content(__('You can change logo in Appearance → Theme Options → Logo.'))
                    ->toArray()
            );
    }
}
