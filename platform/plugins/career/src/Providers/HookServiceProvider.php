<?php

namespace ArchiElite\Career\Providers;

use Botble\Base\Supports\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->addThemeOptions();
    }

    public function addThemeOptions(): void
    {
        $seoFields = [];
        $seoPages = [
            'careers' => __('Careers'),
        ];

        foreach ($seoPages as $pageId => $pageName) {
            $seoFields[] = [
                'id' => sprintf('career_%s_seo_title', $pageId),
                'type' => 'text',
                'label' => trans('plugins/career::career.theme_options.page_seo_title', ['page' => $pageName]),
                'attributes' => [
                    'name' => sprintf('career_%s_seo_title', $pageId),
                    'value' => __($pageName),
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ];

            $seoFields[] = [
                'id' => sprintf('career_%s_seo_description', $pageId),
                'type' => 'textarea',
                'label' => trans('plugins/career::career.theme_options.page_seo_description', ['page' => $pageName]),
                'attributes' => [
                    'name' => sprintf('career_%s_seo_description', $pageId),
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'rows' => 3,
                    ],
                ],
                'helper' => __('Leave it empty to use the default description from Theme options -> General.'),
            ];
        }

        theme_option()
            ->setSection([
                'title' => trans('plugins/career::career.theme_options.seo_name'),
                'id' => 'opt-text-subsection-career-seo',
                'subsection' => true,
                'icon' => 'ti ti-briefcase-2',
                'priority' => 700,
                'fields' => $seoFields,
            ]);
    }
}
