<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Illuminate\Support\Str;

class LocationSeeder extends BaseSeeder
{
    public function run(): void
    {
        Country::query()->truncate();
        State::query()->truncate();
        City::query()->truncate();

        $this->uploadFiles('locations');

        $countries = [
            [
                'name' => 'France',
                'nationality' => 'French',
                'code' => 'FRA',
            ],
            [
                'name' => 'England',
                'nationality' => 'English',
                'code' => 'UK',
            ],
            [
                'name' => 'USA',
                'nationality' => 'Americans',
                'code' => 'US',
            ],
            [
                'name' => 'Holland',
                'nationality' => 'Dutch',
                'code' => 'HL',
            ],
            [
                'name' => 'Denmark',
                'nationality' => 'Danish',
                'code' => 'DN',
            ],
            [
                'name' => 'Germany',
                'nationality' => 'German',
                'code' => 'DE',
            ],
            [
                'name' => 'Japan',
                'nationality' => 'Japanese',
                'code' => 'JP',
            ],
            [
                'name' => 'Canada',
                'nationality' => 'Canadian',
                'code' => 'CA',
            ],
            [
                'name' => 'Australia',
                'nationality' => 'Australian',
                'code' => 'AU',
            ],
            [
                'name' => 'Italy',
                'nationality' => 'Italian',
                'code' => 'IT',
            ],
        ];

        $states = [
            [
                'name' => 'France',
                'abbreviation' => 'FR',
                'country_id' => 1,
            ],
            [
                'name' => 'England',
                'abbreviation' => 'EN',
                'country_id' => 2,
            ],
            [
                'name' => 'New York',
                'abbreviation' => 'NY',
                'country_id' => 3,
            ],
            [
                'name' => 'Holland',
                'abbreviation' => 'HL',
                'country_id' => 4,
            ],
            [
                'name' => 'Denmark',
                'abbreviation' => 'DN',
                'country_id' => 5,
            ],
            [
                'name' => 'Bavaria',
                'abbreviation' => 'BY',
                'country_id' => 6,
            ],
            [
                'name' => 'Tokyo',
                'abbreviation' => 'TK',
                'country_id' => 7,
            ],
            [
                'name' => 'Ontario',
                'abbreviation' => 'ON',
                'country_id' => 8,
            ],
            [
                'name' => 'New South Wales',
                'abbreviation' => 'NSW',
                'country_id' => 9,
            ],
            [
                'name' => 'Lombardy',
                'abbreviation' => 'LO',
                'country_id' => 10,
            ],
        ];

        $cities = [
            [
                'name' => 'Paris',
                'state_id' => 1,
                'country_id' => 1,
            ],
            [
                'name' => 'London',
                'state_id' => 2,
                'country_id' => 2,
            ],
            [
                'name' => 'New York City',
                'state_id' => 3,
                'country_id' => 3,
            ],
            [
                'name' => 'Amsterdam',
                'state_id' => 4,
                'country_id' => 4,
            ],
            [
                'name' => 'Copenhagen',
                'state_id' => 5,
                'country_id' => 5,
            ],
            [
                'name' => 'Munich',
                'state_id' => 6,
                'country_id' => 6,
            ],
            [
                'name' => 'Tokyo',
                'state_id' => 7,
                'country_id' => 7,
            ],
            [
                'name' => 'Toronto',
                'state_id' => 8,
                'country_id' => 8,
            ],
            [
                'name' => 'Sydney',
                'state_id' => 9,
                'country_id' => 9,
            ],
            [
                'name' => 'Milan',
                'state_id' => 10,
                'country_id' => 10,
            ],
            [
                'name' => 'Los Angeles',
                'state_id' => 11,
                'country_id' => 3,
            ],
            [
                'name' => 'Berlin',
                'state_id' => 12,
                'country_id' => 6,
            ],
            [
                'name' => 'Osaka',
                'state_id' => 13,
                'country_id' => 7,
            ],
            [
                'name' => 'Vancouver',
                'state_id' => 14,
                'country_id' => 8,
            ],
            [
                'name' => 'Melbourne',
                'state_id' => 15,
                'country_id' => 9,
            ],
            [
                'name' => 'Rome',
                'state_id' => 16,
                'country_id' => 10,
            ],
            [
                'name' => 'Marseille',
                'state_id' => 17,
                'country_id' => 1,
            ],
            [
                'name' => 'Liverpool',
                'state_id' => 18,
                'country_id' => 2,
            ],
            [
                'name' => 'Chicago',
                'state_id' => 19,
                'country_id' => 3,
            ],
            [
                'name' => 'Rotterdam',
                'state_id' => 20,
                'country_id' => 4,
            ],
            [
                'name' => 'Aarhus',
                'state_id' => 21,
                'country_id' => 5,
            ],
            [
                'name' => 'Frankfurt',
                'state_id' => 22,
                'country_id' => 6,
            ],
            [
                'name' => 'Kyoto',
                'state_id' => 23,
                'country_id' => 7,
            ],
            [
                'name' => 'Montreal',
                'state_id' => 24,
                'country_id' => 8,
            ],
            [
                'name' => 'Brisbane',
                'state_id' => 25,
                'country_id' => 9,
            ],
            [
                'name' => 'Naples',
                'state_id' => 26,
                'country_id' => 10,
            ],
        ];

        foreach ($countries as $country) {
            Country::query()->insert($country);
        }

        foreach ($states as $state) {
            State::query()->insert([
                ...$state,
                'slug' => Str::slug($state['name']),
                'image' => $this->filePath(sprintf('locations/%s.jpg', rand(1, 5))),
            ]);
        }

        foreach ($cities as $city) {
            City::query()->forceCreate([
                ...$city,
                'slug' => Str::slug($city['name']),
                'image' => $this->filePath(sprintf('locations/%s.jpg', rand(1, 5))),
            ]);
        }
    }
}
