<?php

use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\ShortcodeField;
use Botble\Testimonial\Models\Testimonial;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

if (! is_plugin_active('testimonial')) {
    return;
}

Event::listen(RouteMatched::class, function (): void {
    Shortcode::register(
        'testimonials',
        __('Testimonials'),
        __('Testimonials'),
        function (ShortcodeCompiler $shortcode) {
            if (! $testimonialIds = Shortcode::fields()->getIds('testimonial_ids', $shortcode)) {
                return null;
            }

            $testimonials = Testimonial::query()
                ->wherePublished()
                ->whereIn('id', $testimonialIds)
                ->get();

            if ($testimonials->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.testimonials.index', compact('shortcode', 'testimonials'));
        }
    );

    Shortcode::setPreviewImage('testimonials', Theme::asset()->url('images/shortcodes/testimonials/style-1.png'));

    Shortcode::setAdminConfig('testimonials', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->choices(
                        collect(range(1, 4))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/testimonials/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
                    ->selected(Arr::get($attributes, 'style', 1))
            )
            ->addSectionHeadingFields()
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Description')),
            )
            ->add(
                'testimonial_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Testimonials'))
                    ->choices(
                        Testimonial::query()
                            ->wherePublished()
                            ->select(['id', 'name', 'company'])
                            ->get()
                            ->mapWithKeys(fn (Testimonial $item) => [$item->getKey() => sprintf('%s - %s', $item->name, $item->company)]) // @phpstan-ignore-line
                            ->all()
                    )
                    ->multiple()
                    ->searchable()
                    ->selected(ShortcodeField::parseIds(Arr::get($attributes, 'testimonial_ids')))
            )
            ->addBackgroundColorField(defaultValue: '#f7f7f7')
            ->add(
                'background_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Background Image'))
                    ->collapsible('style', '4', Arr::get($attributes, 'style', '1'))
            )
            ->addSliderFields();
    });
});
