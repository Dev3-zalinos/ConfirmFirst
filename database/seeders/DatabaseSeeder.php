<?php

namespace Database\Seeders;

use Botble\ACL\Database\Seeders\UserSeeder;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Database\Seeders\LanguageSeeder;
use Botble\RealEstate\Database\Seeders\CurrencySeeder;

class DatabaseSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->prepareRun();

        $this->call([
            FaqSeeder::class,
            LocationSeeder::class,
            WidgetSeeder::class,
            LanguageSeeder::class,
            CurrencySeeder::class,
            CategorySeeder::class,
            FacilitySeeder::class,
            FeatureSeeder::class,
            PackageSeeder::class,
            InvestorSeeder::class,
            UserSeeder::class,
            AccountSeeder::class,
            ProjectSeeder::class,
            PropertySeeder::class,
            TestimonialSeeder::class,
            BlogSeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
            ThemeOptionSeeder::class,
            ReviewSeeder::class,
            CareerSeeder::class,
            ConsultSeeder::class,
            AnnouncementSeeder::class,
        ]);

        $this->finished();
    }
}
