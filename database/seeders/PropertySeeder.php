<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Facades\SlugHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySeeder extends BaseSeeder
{
    public function run(): void
    {
        Property::query()->truncate();
        DB::table('re_property_features')->truncate();
        DB::table('re_property_categories')->truncate();
        DB::table('re_facilities_distances')->where('reference_type', Property::class)->delete();
        DB::table('slugs')->where('reference_type', Property::class)->delete();

        $properties = [
            '3 Beds Villa Calpe, Alicante',
            'Lavida Plus Office-tel 1 Bedroom',
            'Vinhomes Grand Park Studio 1 Bedroom',
            'The Sun Avenue Office-tel 1 Bedroom',
            'Property For sale, Johannesburg, South Africa',
            'Stunning French Inspired Manor',
            'Villa for sale at Bermuda Dunes',
            'Walnut Park Apartment',
            '5 beds luxury house',
            'Family Victorian "View" Home',
            'Osaka Heights Apartment',
            'Private Estate Magnificent Views',
            'Thompson Road House for rent',
            'Brand New 1 Bedroom Apartment In First Class Location',
            'Elegant family home presents premium modern living',
            'Luxury Apartments in Singapore for Sale',
            '5 room luxury penthouse for sale in Kuala Lumpur',
            '2 Floor house in Compound Pejaten Barat Kemang',
            'Apartment Muiderstraatweg in Diemen',
            'Nice Apartment for rent in Berlin',
            'Pumpkin Key - Private Island',
            'Maplewood Estates',
            'Pine Ridge Manor',
            'Oak Hill Residences',
            'Sunnybrook Villas',
            'Riverstone Condominiums',
            'Cedar Park Apartments',
            'Lakeside Retreat',
            'Willow Creek Homes',
            'Grandview Heights',
            'Forest Glen Cottages',
            'Harborview Towers',
            'Meadowlands Estates',
            'Highland Meadows',
            'Brookfield Gardens',
            'Silverwood Villas',
            'Evergreen Terrace',
            'Golden Gate Residences',
            'Spring Blossom Park',
            'Horizon Pointe',
            'Whispering Pines Lodge',
            'Sunset Ridge',
            'Timberline Estates',
            'Crystal Lake Condos',
            'Briarwood Apartments',
            'Summit View',
            'Elmwood Park',
            'Stonegate Homes',
            'Rosewood Villas',
            'Prairie Meadows',
            'Hawthorne Heights',
            'Sierra Vista',
            'Autumn Leaves',
            'Blue Sky Residences',
            'Pebble Creek',
            'Magnolia Manor',
            'Cherry Blossom Estates',
            'Windsor Park',
            'Seaside Villas',
            'Mountain View Retreat',
            'Amberwood Apartments',
        ];

        $floorPlans = collect(
            [
                [
                    'name' => 'First Floor',
                    'bedrooms' => 3,
                    'bathrooms' => 2,
                    'image' => $this->filePath('properties/floor.png'),
                ],
                [
                    'name' => 'Second Floor',
                    'bedrooms' => 2,
                    'bathrooms' => 1,
                    'image' => $this->filePath('properties/floor.png'),
                ],
            ]
        )
            ->map(function ($floorPlan) {
                return collect($floorPlan)->map(function ($value, $key) {
                    return [
                        'key' => $key,
                        'value' => (string) $value,
                    ];
                })->toArray();
            })
            ->toArray();

        $projects = Project::query()->pluck('id');
        $states = State::query()->with(['country', 'cities'])->limit(6)->oldest()->get();
        $accounts = Account::query()->pluck('id');
        $categories = Category::query()->pluck('id');
        $features = Feature::query()->pluck('id');
        $featuresCount = $features->count();
        $facilitiesCount = Facility::query()->count();

        $faker = $this->fake();

        foreach ($properties as $property) {
            $type = $faker->randomElement(['sale', 'rent']);

            $images = [];

            foreach ($faker->randomElements(range(1, 12), rand(5, 12)) as $image) {
                $images[] = $this->filePath("properties/$image.jpg");
            }

            $state = $states->random();

            /**
             * @var Property $property
             */
            $property = Property::query()->forceCreate([
                'unique_id' => strtoupper(Str::random(6)),
                'name' => $property,
                'description' => $faker->paragraph(),
                'content' => $faker->paragraph(10),
                'location' => $faker->address(),
                'images' => $images,
                'project_id' => $projects->isNotEmpty() ? $projects->random() : null,
                'author_id' => $accounts->random(),
                'author_type' => Account::class,
                'number_bedroom' => rand(1, 10),
                'number_bathroom' => rand(1, 10),
                'number_floor' => rand(1, 100),
                'square' => rand(1, 100) * 10,
                'price' => rand(100, 10000) * 100,
                'is_featured' => $faker->boolean(),
                'status' => $type === 'sale' ? 'selling' : 'renting',
                'type' => $type,
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'expire_date' => Carbon::now()->days(rand(30, 365)),
                'never_expired' => true,
                'latitude' => $faker->latitude(42.4772, 44.0153),
                'longitude' => $faker->longitude(-74.7624, -76.7517),
                'views' => rand(0, 100000),
                'country_id' => $state->country->id,
                'state_id' => $state->id,
                'city_id' => $state->cities->isNotEmpty() ? $state->cities->random()->id : null,
                'floor_plans' => $floorPlans,
            ]);

            MetaBox::saveMetaBoxData($property, 'video_url', 'https://youtu.be/tRxGSHL8bI0?si=kbumCspOMG-kJvTT');

            $property->categories()->attach($categories->random(rand(1, 3)));
            $property->features()->attach($features->random(rand($featuresCount - 8, $featuresCount)));

            foreach (range(1, $facilitiesCount) as $facilityId) {
                $distance = sprintf('%skm', rand(1, 20));
                $property->facilities()->attach($facilityId, ['distance' => $distance]);
            }

            SlugHelper::createSlug($property);
        }
    }
}
