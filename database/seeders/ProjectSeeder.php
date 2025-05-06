<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\Base\Supports\BaseSeeder;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Models\Project;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('properties');

        Project::query()->truncate();

        DB::table('re_project_categories')->truncate();
        DB::table('re_project_features')->truncate();
        DB::table('re_facilities_distances')->where('reference_type', Project::class)->delete();

        $users = User::query()->pluck('id');
        $categories = Category::query()->pluck('id');
        $investors = Investor::query()->pluck('id');
        $features = Feature::query()->pluck('id');
        $featuresCount = $features->count();
        $facilitiesCount = Facility::query()->count();
        $states = State::query()->with(['country', 'cities'])->limit(6)->oldest()->get();

        $projects = [
            'Walnut Park Apartments',
            'Sunshine Wonder Villas',
            'Diamond Island',
            'The Nassim',
            'Vinhomes Grand Park',
            'The Metropole Thu Thiem',
            'Villa on Grand Avenue',
            'Traditional Food Restaurant',
            'Villa on Hollywood Boulevard',
            'Office Space at Northwest 107th',
            'Home in Merrick Way',
            'Adarsh Greens',
            'Rustomjee Evershine Global City',
            'Godrej Exquisite',
            'Godrej Prime',
            'PS Panache',
            'Upturn Atmiya Centria',
            'Brigade Oasis',
        ];

        $faker = $this->fake();

        foreach ($projects as $project) {
            $images = [];

            foreach ($faker->randomElements(range(1, 12), rand(5, 12)) as $image) {
                $images[] = $this->filePath("properties/$image.jpg");
            }

            $price = rand(100, 10000);

            $state = $states->random();

            /**
             * @var Project $project
             */
            $project = Project::query()->create([
                'name' => $project,
                'description' => $faker->paragraph(),
                'content' => $faker->paragraph(10),
                'images' => $images,
                'location' => $faker->address(),
                'investor_id' => $investors->random(),
                'number_block' => rand(1, 10),
                'number_floor' => rand(1, 50),
                'number_flat' => rand(10, 5000),
                'is_featured' => rand(0, 1),
                'date_finish' => $faker->dateTime(),
                'date_sell' => $faker->dateTime(),
                'latitude' => $faker->latitude(42.4772, 44.0153),
                'longitude' => $faker->longitude(-74.7624, -76.7517),
                'country_id' => $state->country->id,
                'state_id' => $state->id,
                'city_id' => $state->cities->isNotEmpty() ? $state->cities->random()->id : null,
                'status' => ProjectStatusEnum::SELLING,
                'price_from' => $price,
                'price_to' => $price + rand(500, 10000),
                'views' => rand(100, 10000),
                'currency_id' => 1,
                'author_id' => $users->random(),
                'author_type' => User::class,
            ]);

            $project->categories()->attach($categories->random(rand(1, 3)));
            $project->features()->attach($features->random(rand($featuresCount - 8, $featuresCount)));

            foreach (range(1, $facilitiesCount) as $facilityId) {
                $distance = sprintf('%skm', rand(1, 20));
                $project->facilities()->attach($facilityId, ['distance' => $distance]);
            }

            SlugHelper::createSlug($project);
        }
    }
}
