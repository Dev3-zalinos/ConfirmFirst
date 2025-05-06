<?php

namespace ArchiElite\Career\Http\Controllers;

use ArchiElite\Career\Models\Career;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    public function careers()
    {
        $title = theme_option('career_careers_seo_title') ?: __('Careers');
        $description = theme_option('career_careers_seo_description');

        SeoHelper::setTitle($title);

        if ($description) {
            SeoHelper::setDescription($description);
        }

        Theme::breadcrumb()->add(__('Careers'), route('public.careers'));

        $careers = Career::query()
            ->wherePublished()
            ->orderByDesc('created_at')
            ->paginate(10);

        return Theme::scope('career.careers', compact('careers'), 'plugins/career::themes.careers')->render();
    }
}
