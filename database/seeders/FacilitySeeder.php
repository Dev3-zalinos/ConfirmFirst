<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Facility;

class FacilitySeeder extends BaseSeeder
{
    public function run(): void
    {
        Facility::query()->truncate();

        $facilities = [
            [
                'name' => 'Hospital',
                'icon' => 'ti ti-hospital',
            ],
            [
                'name' => 'Super Market',
                'icon' => 'ti ti-shopping-cart',
            ],
            [
                'name' => 'School',
                'icon' => 'ti ti-school',
            ],
            [
                'name' => 'Entertainment',
                'icon' => 'ti ti-movie',
            ],
            [
                'name' => 'Pharmacy',
                'icon' => 'ti ti-pill',
            ],
            [
                'name' => 'Airport',
                'icon' => 'ti ti-plane-departure',
            ],
            [
                'name' => 'Railways',
                'icon' => 'ti ti-train',
            ],
            [
                'name' => 'Bus Stop',
                'icon' => 'ti ti-bus',
            ],
            [
                'name' => 'Beach',
                'icon' => 'ti ti-beach',
            ],
            [
                'name' => 'Mall',
                'icon' => 'ti ti-shopping-cart',
            ],
            [
                'name' => 'Bank',
                'icon' => 'ti ti-building-bank',
            ],
        ];

        Facility::query()->insert($facilities);
    }
}
