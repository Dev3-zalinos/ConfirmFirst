<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Review;
use Carbon\Carbon;

class ReviewSeeder extends BaseSeeder
{
    public function run(): void
    {
        Review::query()->truncate();

        $accounts = Account::query()->pluck('id');
        $projects = Project::query()->pluck('id');
        $properties = Property::query()->pluck('id');

        $faker = $this->fake();

        $now = Carbon::now();

        for ($i = 1; $i <= count($properties) * 10; $i++) {
            Review::query()->insertOrIgnore([
                'id' => (new Review())->newUniqueId(),
                'account_id' => $accounts->random(),
                'reviewable_type' => Property::class,
                'reviewable_id' => $properties->random(),
                'content' => $faker->realText(rand(30, 300)),
                'star' => rand(1, 5),
                'created_at' => Carbon::now()->subDays(rand(0, 120)),
                'updated_at' => $now,
            ]);

            Review::query()->insertOrIgnore([
                'id' => (new Review())->newUniqueId(),
                'account_id' => $accounts->random(),
                'reviewable_type' => Project::class,
                'reviewable_id' => $projects->random(),
                'content' => $faker->realText(rand(30, 300)),
                'star' => rand(1, 5),
                'created_at' => Carbon::now()->subDays(rand(0, 120)),
                'updated_at' => $now,
            ]);
        }
    }
}
