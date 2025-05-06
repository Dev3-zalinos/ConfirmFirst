<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Package;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends BaseSeeder
{
    public function run(): void
    {
        Package::query()->truncate();
        DB::table('re_account_packages')->truncate();

        $data = [
            [
                'name' => 'Free Trial',
                'price' => 0,
                'currency_id' => 1,
                'percent_save' => 0,
                'order' => 1,
                'number_of_listings' => 1,
                'account_limit' => 1,
                'is_default' => false,
                'features' => [
                    [['key' => 'text', 'value' => 'Limited time trial period']],
                    [['key' => 'text', 'value' => '1 listing allowed']],
                    [['key' => 'text', 'value' => 'Basic support']],
                ],
            ],
            [
                'name' => 'Basic Listing',
                'price' => 250,
                'currency_id' => 1,
                'percent_save' => 0,
                'order' => 2,
                'number_of_listings' => 1,
                'account_limit' => 5,
                'is_default' => true,
                'features' => [
                    [['key' => 'text', 'value' => '1 listing allowed']],
                    [['key' => 'text', 'value' => '5 photos per listing']],
                    [['key' => 'text', 'value' => 'Basic support']],
                ],
            ],
            [
                'name' => 'Standard Package',
                'price' => 1000,
                'currency_id' => 1,
                'percent_save' => 20,
                'order' => 3,
                'number_of_listings' => 5,
                'account_limit' => 10,
                'is_default' => false,
                'features' => [
                    [['key' => 'text', 'value' => '5 listings allowed']],
                    [['key' => 'text', 'value' => '10 photos per listing']],
                    [['key' => 'text', 'value' => 'Priority support']],
                ],
            ],
            [
                'name' => 'Professional Package',
                'price' => 1800,
                'currency_id' => 1,
                'percent_save' => 28,
                'order' => 4,
                'number_of_listings' => 10,
                'account_limit' => 15,
                'is_default' => false,
                'features' => [
                    [['key' => 'text', 'value' => '10 listings allowed']],
                    [['key' => 'text', 'value' => '15 photos per listing']],
                    [['key' => 'text', 'value' => 'Premium support']],
                    [['key' => 'text', 'value' => 'Featured listings']],
                ],
            ],
            [
                'name' => 'Premium Package',
                'price' => 2500,
                'currency_id' => 1,
                'percent_save' => 33,
                'order' => 5,
                'number_of_listings' => 15,
                'account_limit' => 20,
                'is_default' => false,
                'features' => [
                    [['key' => 'text', 'value' => '15 listings allowed']],
                    [['key' => 'text', 'value' => '20 photos per listing']],
                    [['key' => 'text', 'value' => 'Premium support']],
                    [['key' => 'text', 'value' => 'Featured listings']],
                    [['key' => 'text', 'value' => 'Priority listing placement']],
                ],
            ],
        ];

        foreach ($data as $item) {
            Package::query()->create($item);
        }
    }
}
