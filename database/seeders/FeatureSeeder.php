<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Feature;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends BaseSeeder
{
    public function run(): void
    {
        Feature::query()->truncate();
        DB::table('re_project_features')->truncate();

        $features = [
            [
                'name' => 'Wifi',
                'icon' => 'ti ti-wifi',
            ],
            [
                'name' => 'Parking',
                'icon' => 'ti ti-parking',
            ],
            [
                'name' => 'Swimming pool',
                'icon' => 'ti ti-pool',
            ],
            [
                'name' => 'Balcony',
                'icon' => 'ti ti-building-skyscraper',
            ],
            [
                'name' => 'Garden',
                'icon' => 'ti ti-trees',
            ],
            [
                'name' => 'Security',
                'icon' => 'ti ti-shield-lock',
            ],
            [
                'name' => 'Fitness center',
                'icon' => 'ti ti-stretching',
            ],
            [
                'name' => 'Air Conditioning',
                'icon' => 'ti ti-air-conditioning',
            ],
            [
                'name' => 'Central Heating',
                'icon' => 'ti ti-thermometer',
            ],
            [
                'name' => 'Laundry Room',
                'icon' => 'ti ti-wash-machine',
            ],
            [
                'name' => 'Pets Allow',
                'icon' => 'ti ti-paw',
            ],
            [
                'name' => 'Spa & Massage',
                'icon' => 'ti ti-bath',
            ],
        ];

        Feature::query()->insert($features);
    }
}
