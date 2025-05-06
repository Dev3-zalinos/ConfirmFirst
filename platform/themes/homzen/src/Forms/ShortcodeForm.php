<?php

namespace Theme\Homzen\Forms;

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Shortcode\Forms\ShortcodeForm as BaseShortcodeForm;

class ShortcodeForm extends BaseShortcodeForm
{
    public function lazyLoading(bool $lazy = true): static
    {
        return $this->withLazyLoading($lazy);
    }

    public function addBackgroundColorField(string $name = 'background_color', ?string $defaultValue = null, ?string $label = null): static
    {
        return $this->add(
            $name,
            ColorField::class,
            ColorFieldOption::make()
                ->label($label ?: __('Background color'))
                ->defaultValue($defaultValue)
        );
    }

    public function addSectionHeadingFields(bool $hasTitle = true, bool $hasSubtitle = true): static
    {
        return $this
            ->when($hasTitle, function (self $form): void {
                $form->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title')),
                );
            })
            ->when($hasSubtitle, function (self $form): void {
                $form->add(
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()->label(__('Subtitle')),
                );
            });
    }

    public function addSectionButtonAction(): static
    {
        return $this
            ->add(
                'button_label',
                TextField::class,
                TextFieldOption::make()->label(__('Button label')),
            )
            ->add(
                'button_url',
                TextField::class,
                TextFieldOption::make()->label(__('Button URL')),
            );
    }

    public function addLimitField(int $defaultValue = 4): static
    {
        return $this->add(
            'limit',
            NumberField::class,
            NumberFieldOption::make()
                ->label(__('Limit'))
                ->helperText(__('Number of items to display'))
                ->defaultValue($defaultValue),
        );
    }

    public function addSliderFields(): static
    {
        return $this
            ->add(
                'is_autoplay',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Is autoplay?'))
                    ->choices(['yes' => __('Yes'), 'no' => __('No')])
            )
            ->add(
                'autoplay_speed',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Autoplay speed (if autoplay enabled)'))
                    ->choices(
                        array_combine(
                            [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000],
                            [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000]
                        )
                    )
            )
            ->add(
                'is_loop',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Loop?'))
                    ->choices(['yes' => __('Continuously'), 'no' => __('Stop on the last slide')])
            );
    }
}
