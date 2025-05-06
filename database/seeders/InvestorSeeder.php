<?php

namespace Database\Seeders;

use Botble\RealEstate\Models\Investor;
use Illuminate\Database\Seeder;

class InvestorSeeder extends Seeder
{
    public function run(): void
    {
        Investor::query()->truncate();

        $investors = [
            'National Pension Service',
            'Generali',
            'Temasek',
            'China Investment Corporation',
            'Government Pension Fund Global',
            'PSP Investments',
            'MEAG Munich ERGO',
            'HOOPP',
            'BT Group',
            'New York City ERS',
            'New Jersey Division of Investment',
            'State Super',
            'Shinkong',
            'Rest Super',
        ];

        foreach ($investors as $investor) {
            Investor::query()->create([
                'name' => $investor,
            ]);
        }
    }
}
