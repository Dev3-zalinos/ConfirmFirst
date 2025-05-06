<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Theme\Database\Traits\HasThemeOptionSeeder;
use Botble\Theme\Supports\ThemeSupport;

class ThemeOptionSeeder extends BaseSeeder
{
    use HasThemeOptionSeeder;
    use HasPageSeeder;

    public function run(): void
    {
        $this->uploadFiles('general');

        $this->createThemeOptions([
            'site_title' => 'Homzen',
            'seo_description' => 'Find your favorite homes at Homzen',
            'copyright' => '©%Y Homzen is Proudly Powered by Botble Team.',
            'favicon' => $this->filePath('general/favicon.png'),
            'logo' => $this->filePath('general/logo.png'),
            'logo_light' => $this->filePath('general/logo-light.png'),
            'preloader_enabled' => 'yes',
            'preloader_version' => 'v2',
            'social_links' => ThemeSupport::getDefaultSocialLinksData(),
            'social_sharing' => ThemeSupport::getDefaultSocialSharingData(),
            'primary_color' => '#db1d23',
            'hover_color' => '#cd380f',
            'footer_background_color' => '#161e2d',
            'footer_background_image' => $this->filePath('general/banner-footer.png'),
            'use_modal_for_authentication' => true,
            'homepage_id' => $this->getPageId('Homepage 1'),
            'blog_page_id' => $this->getPageId('Blog'),
            'hotline' => '0123 456 789',
            'email' => 'contact@botble.com',
            'breadcrumb_background_color' => '#f7f7f7',
            'breadcrumb_text_color' => '#161e2d',
            'lazy_load_images' => true,
            'lazy_load_placeholder_image' => $this->filePath('general/placeholder.png'),
            'newsletter_popup_enable' => true,
            'newsletter_popup_image' => $this->filePath('general/newsletter-image.jpg'),
            'newsletter_popup_title' => 'Let’s join our newsletter!',
            'newsletter_popup_subtitle' => 'Weekly Updates',
            'newsletter_popup_description' => 'Do not worry we don’t spam!',
            'properties_list_page_id' => $this->getPageId('Properties'),
            'projects_list_page_id' => $this->getPageId('Projects'),
        ]);
    }
}
