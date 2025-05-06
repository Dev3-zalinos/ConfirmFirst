<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Widget\Database\Traits\HasWidgetSeeder;
use Botble\Widget\Widgets\CoreSimpleMenu;
use NewsletterWidget;
use RelatedPostsWidget;
use SiteCopyrightWidget;
use SiteInformationWidget;
use SiteLogoWidget;
use SocialLinksWidget;

class WidgetSeeder extends BaseSeeder
{
    use HasWidgetSeeder;
    use HasPageSeeder;

    public function run(): void
    {
        $this->createWidgets([
            [
                'widget_id' => SiteLogoWidget::class,
                'sidebar_id' => 'top_footer_sidebar',
                'position' => 1,
                'data' => [],
            ],
            [
                'widget_id' => SocialLinksWidget::class,
                'sidebar_id' => 'top_footer_sidebar',
                'position' => 2,
                'data' => [
                    'title' => 'Follow Us:',
                ],
            ],
            [
                'widget_id' => SiteInformationWidget::class,
                'sidebar_id' => 'inner_footer_sidebar',
                'position' => 1,
                'data' => [
                    'about' => 'Specializes in providing high-class tours for those in need. Contact Us',
                    'items' => [
                        [
                            [
                                'key' => 'icon',
                                'value' => 'ti ti-map-pin',
                            ],
                            [
                                'key' => 'text',
                                'value' => '101 E 129th St, East Chicago, IN 46312, US',
                            ],
                        ],
                        [
                            [
                                'key' => 'icon',
                                'value' => 'ti ti-phone-call',
                            ],
                            [
                                'key' => 'text',
                                'value' => '1-333-345-6868',
                            ],
                        ],
                        [
                            [
                                'key' => 'icon',
                                'value' => 'ti ti-mail',
                            ],
                            [
                                'key' => 'text',
                                'value' => 'contact@botble.com',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'widget_id' => CoreSimpleMenu::class,
                'sidebar_id' => 'inner_footer_sidebar',
                'position' => 2,
                'data' => [
                    'id' => CoreSimpleMenu::class,
                    'name' => 'Categories',
                    'items' => [
                        [
                            [
                                'key' => 'label',
                                'value' => 'Pricing Plans',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/pricing-plans',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Our Services',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/our-services',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'About Us',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/about-us',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Contact Us',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/contact-us',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'widget_id' => CoreSimpleMenu::class,
                'sidebar_id' => 'inner_footer_sidebar',
                'position' => 3,
                'data' => [
                    'id' => CoreSimpleMenu::class,
                    'name' => 'Our Company',
                    'items' => [
                        [
                            [
                                'key' => 'label',
                                'value' => 'Property For Sale',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/properties?type=sale',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Property For Rent',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/properties?type=rent',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Privacy Policy',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/privacy-policy',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Our Agents',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/agents',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'widget_id' => NewsletterWidget::class,
                'sidebar_id' => 'inner_footer_sidebar',
                'position' => 4,
                'data' => [
                    'title' => 'Newsletter',
                    'subtitle' => 'Your Weekly/Monthly Dose of Knowledge and Inspiration',
                ],
            ],
            [
                'widget_id' => SiteCopyrightWidget::class,
                'sidebar_id' => 'bottom_footer_sidebar',
                'position' => 1,
                'data' => [],
            ],
            [
                'widget_id' => CoreSimpleMenu::class,
                'sidebar_id' => 'bottom_footer_sidebar',
                'position' => 2,
                'data' => [
                    'id' => CoreSimpleMenu::class,
                    'items' => [
                        [
                            [
                                'key' => 'label',
                                'value' => 'Terms Of Services',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/our-services',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Privacy Policy',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/privacy-policy',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                        [
                            [
                                'key' => 'label',
                                'value' => 'Cookie Policy',
                            ],
                            [
                                'key' => 'url',
                                'value' => '/cookie-policy',
                            ],
                            [
                                'key' => 'attributes',
                                'value' => '',
                            ],
                            [
                                'key' => 'is_open_new_tab',
                                'value' => '0',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'widget_id' => 'BlogSearchWidget',
                'sidebar_id' => 'blog_sidebar',
                'position' => 1,
                'data' => [
                    'name' => 'Search',
                ],
            ],
            [
                'widget_id' => 'BlogPostsWidget',
                'sidebar_id' => 'blog_sidebar',
                'position' => 2,
                'data' => [
                    'name' => 'Recent Posts',
                    'limit' => 3,
                ],
            ],
            [
                'widget_id' => 'BlogCategoriesWidget',
                'sidebar_id' => 'blog_sidebar',
                'position' => 3,
                'data' => [
                    'name' => 'By Categories',
                    'number_display' => 8,
                ],
            ],
            [
                'widget_id' => 'BlogTagsWidget',
                'sidebar_id' => 'blog_sidebar',
                'position' => 4,
                'data' => [
                    'name' => 'Popular Tag',
                    'number_display' => 9,
                ],
            ],
            [
                'widget_id' => RelatedPostsWidget::class,
                'sidebar_id' => 'bottom_post_detail_sidebar',
                'position' => 1,
                'data' => [
                    'title' => 'News insight',
                    'subtitle' => 'Related Posts',
                    'limit' => 3,
                ],
            ],
        ]);
    }
}
