<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        Category::query()->truncate();
        Slug::query()->where('reference_type', Category::class)->delete();
        DB::table('meta_boxes')->where('reference_type', Category::class)->delete();

        $categories = [
            'Apartment',
            'Villa',
            'Condo',
            'House',
            'Land',
            'Commercial property',
        ];

        $icons = [
            'ti ti-home',
            'ti ti-calendar',
            'ti ti-shopping-cart',
            'ti ti-chart-bar',
            'ti ti-mail',
            'ti ti-map',
            'ti ti-bell',
        ];

        foreach ($categories as $key => $category) {
            $category = Category::query()->create([
                'name' => $category,
                'order' => $key,
                'is_default' => $key === 0,
            ]);

            MetaBox::saveMetaBoxData($category, 'icon', $this->fake()->unique()->randomElement($icons));

            SlugHelper::createSlug($category);
        }

        $properties = Property::query()->get();

        foreach ($properties as $property) {
            $property->categories()->sync([Category::query()->inRandomOrder()->value('id')]);
            $property->save();
        }

        $projects = Project::query()->get();

        foreach ($projects as $project) {
            $project->categories()->sync([Category::query()->inRandomOrder()->value('id')]);
            $project->save();
        }
    }
}
