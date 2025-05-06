<?php

use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

if (! is_plugin_active('location')) {
    return;
}

Event::listen(RouteMatched::class, function (): void {
    Shortcode::register('location', __('Location'), __('Location'), function (ShortcodeCompiler $shortcode) {
        $cityIds = Shortcode::fields()->getIds('city_ids', $shortcode);
        $stateIds = Shortcode::fields()->getIds('state_ids', $shortcode);
        $destination = $shortcode->destination === 'property' ? 'properties' : 'projects';

        $locations = match ($shortcode->type) {
            'city' => City::query()
                ->wherePublished()
                ->whereIn('id', $cityIds)
                ->select(['id', 'name', 'image', 'slug'])
                ->oldest('order')
                ->oldest('name')
                ->get()
                ->when(is_plugin_active('real-estate'), function (Collection $collection) use ($destination): void {
                    $collection->transform(fn (City $city) => $city->setAttribute('url', route("public.$destination-by-city", $city->slug))); // @phpstan-ignore-line
                }),
            'state' => State::query()
                ->wherePublished()
                ->whereIn('id', $stateIds)
                ->select(['id', 'name', 'image', 'slug'])
                ->oldest('order')
                ->oldest('name')
                ->get()
                ->when(is_plugin_active('real-estate'), function (Collection $collection) use ($destination): void {
                    $collection->transform(fn (State $state) => $state->setAttribute('url', route("public.$destination-by-state", $state->slug))); // @phpstan-ignore-line
                }),
            default => collect(),
        };

        if ($locations->isEmpty()) {
            return null;
        }

        return Theme::partial('shortcodes.location.index', compact('shortcode', 'locations'));
    });

    Shortcode::setPreviewImage('location', Theme::asset()->url('images/shortcodes/location/style-1.png'));

    Shortcode::setAdminConfig('location', function (array $attributes) {
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
                                    'image' => Theme::asset()->url("images/shortcodes/location/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
            )
            ->addSectionHeadingFields()
            ->add(
                'type',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(__('Location type'))
                    ->choices([
                        'city' => __('City'),
                        'state' => __('State'),
                    ])
                    ->defaultValue('city')
                    ->selected(Arr::get($attributes, 'type')),
            )
            ->add(
                'city_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Cities'))
                    ->collapsible('type', 'city', Arr::get($attributes, 'type', 'city'))
                    ->searchable()
                    ->multiple()
                    ->choices(
                        City::query()
                            ->wherePublished()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->selected(explode(',', Arr::get($attributes, 'city_ids', '')))
            )
            ->add(
                'state_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('States'))
                    ->collapsible('type', 'state', Arr::get($attributes, 'type', 'city'))
                    ->searchable()
                    ->multiple()
                    ->choices(
                        State::query()
                            ->wherePublished()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->selected(explode(',', Arr::get($attributes, 'state_ids', '')))
            )
            ->add(
                'destination',
                RadioField::class,
                RadioFieldOption::make()
                    ->choices([
                        'property' => __('Property'),
                        'project' => __('Project'),
                    ])
                    ->label(__('Destination type'))
                    ->helperText(__('Selecting an option will redirect you to a list of properties or projects page.'))
                    ->defaultValue('property')
                    ->selected(Arr::get($attributes, 'destination')),
            )
            ->addBackgroundColorField(defaultValue: '#f7f7f7')
            ->addSectionButtonAction()
            ->addSliderFields();
    });
});
