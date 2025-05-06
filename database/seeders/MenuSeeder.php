<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Menu\Database\Traits\HasMenuSeeder;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Page\Models\Page;

class MenuSeeder extends BaseSeeder
{
    use HasMenuSeeder;
    use HasPageSeeder;

    public function run(): void
    {
        $data = [
            [
                'name' => 'Main menu',
                'slug' => 'main-menu',
                'location' => 'main-menu',
                'items' => [
                    [
                        'title' => 'Home',
                        'children' => [
                            [
                                'title' => 'Homepage 1',
                                'reference_type' => Page::class,
                                'reference_id' => $this->getPageId('Homepage 1'),
                            ],
                            [
                                'title' => 'Homepage 2',
                                'reference_type' => Page::class,
                                'reference_id' => $this->getPageId('Homepage 2'),
                            ],
                            [
                                'title' => 'Homepage 3',
                                'reference_type' => Page::class,
                                'reference_id' => $this->getPageId('Homepage 3'),
                            ],
                            [
                                'title' => 'Homepage 4',
                                'reference_type' => Page::class,
                                'reference_id' => $this->getPageId('Homepage 4'),
                            ],
                            [
                                'title' => 'Homepage 5',
                                'reference_type' => Page::class,
                                'reference_id' => $this->getPageId('Homepage 5'),
                            ],
                        ],
                    ],
                    [
                        'title' => 'Projects',
                        'url' => '/projects',
                    ],
                    [
                        'title' => 'Properties',
                        'url' => '/properties',
                    ],
                    [
                        'title' => 'Pages',
                        'url' => '#',
                        'children' => [
                            [
                                'title' => 'Agents',
                                'url' => '/agents',
                            ],
                            [
                                'title' => 'Careers',
                                'url' => '/careers',
                            ],
                            [
                                'title' => 'Wishlist',
                                'url' => '/wishlist',
                            ],
                            [
                                'title' => 'About Us',
                                'reference_id' => $this->getPageId('About Us'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Our Services',
                                'reference_id' => $this->getPageId('Our Services'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Pricing',
                                'reference_id' => $this->getPageId('Pricing Plans'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Contact Us',
                                'reference_id' => $this->getPageId('Contact Us'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'FAQs',
                                'reference_id' => $this->getPageId('FAQs'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Privacy Policy',
                                'reference_id' => $this->getPageId('Privacy Policy'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Coming Soon',
                                'reference_id' => $this->getPageId('Coming Soon'),
                                'reference_type' => Page::class,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Blog',
                        'url' => '#',
                        'children' => [
                            [
                                'title' => 'Blog List',
                                'reference_id' => $this->getPageId('Blog'),
                                'reference_type' => Page::class,
                            ],
                            [
                                'title' => 'Blog Detail',
                                'url' => '/news/the-benefits-of-smart-home-technology',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->createMenus($data);
    }
}
