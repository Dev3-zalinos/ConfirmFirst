<?php

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

if (! is_plugin_active('blog')) {
    return;
}

Event::listen(RouteMatched::class, function (): void {
    Shortcode::register('blog-posts', __('Blog Posts'), __('Blog Posts'), function (ShortcodeCompiler $shortcode) {
        $limit = (int) $shortcode->limit ?: 3;

        /**
         * @var Collection<\Botble\Blog\Models\Post> $posts
         */
        $posts = match ($shortcode->type) {
            'featured' => get_featured_posts($limit),
            'popular' => get_popular_posts($limit),
            default => get_recent_posts($limit),
        };

        if ($posts->isEmpty()) {
            return null;
        }

        $posts->loadMissing(['author', 'slugable']);

        return Theme::partial('shortcodes.blog-posts.index', compact('shortcode', 'posts'));
    });

    Shortcode::setPreviewImage('blog-posts', Theme::asset()->url('images/shortcodes/blog-posts/style-1.png'));

    Shortcode::setAdminConfig('blog-posts', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->label(__('Style'))
                    ->choices(
                        collect(range(1, 2))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/blog-posts/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
                    ->defaultValue(1)
            )
            ->addSectionHeadingFields()
            ->add(
                'type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Post type'))
                    ->choices([
                        'recent' => __('Recent'),
                        'featured' => __('Featured'),
                        'popular' => __('Popular'),
                    ])
                    ->defaultValue('recent')
            )
            ->addLimitField(defaultValue: 3)
            ->addBackgroundColorField(defaultValue: '#f7f7f7');
    });
});
