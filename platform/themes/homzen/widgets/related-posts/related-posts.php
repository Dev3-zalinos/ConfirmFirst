<?php

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Theme\Facades\Theme;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;

class RelatedPostsWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Related Posts'),
            'description' => __('Displays a list of posts related to the current content.'),
            'title' => null,
            'subtitle' => 'Related Posts',
            'limit' => 3,
        ]);
    }

    public function data(): array
    {
        $currentPostId = Theme::get('currentPostId');

        if (! $currentPostId) {
            return [
                'posts' => collect(),
            ];
        }

        $posts = get_related_posts($currentPostId, (int) $this->getConfig('limit') ?: 3);

        return compact('posts');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()->label(__('Title')),
            )
            ->add(
                'subtitle',
                TextField::class,
                TextFieldOption::make()->label(__('Subtitle')),
            )
            ->add(
                'limit',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Limit'))
                    ->defaultValue(3)
            );
    }
}
