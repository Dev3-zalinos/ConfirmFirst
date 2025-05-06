@php
    Theme::set('breadcrumbEnabled', $page->getMetaData('breadcrumb', 'yes'));
    Theme::set('breadcrumbBackgroundColor', $page->getMetaData('breadcrumb_background_color', true));
    Theme::set('breadcrumbTextColor', $page->getMetaData('breadcrumb_text_color', true));
    Theme::set('breadcrumbBackgroundImage', $page->getMetaData('breadcrumb_background_image', true));
    Theme::set('pageTitle', $page->name);
@endphp

{!! apply_filters(
    PAGE_FILTER_FRONT_PAGE_CONTENT,
    Html::tag(
        'div',
        BaseHelper::clean($page->content),
        ['class' => sprintf('ck-content %s', BaseHelper::isHomepage($page->id) ? '' : 'single-detail')]
    )->toHtml(),
    $page
) !!}
