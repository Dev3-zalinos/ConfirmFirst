<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection(): Collection
    {
        // Try to get real data first
        $projects = Project::query()
            ->with([
                'investor',
                'categories',
                'features',
                'facilities',
                'customFields',
                'metadata',
            ])
            ->latest()
            ->limit(3)
            ->get();

        if ($projects->isNotEmpty()) {
            return new Collection($projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'unique_id' => $project->unique_id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'content' => $project->content,
                    'images' => is_array($project->images) ? implode(',', $project->images) : $project->images,
                    'location' => $project->location,
                    'investor' => $project->investor?->name,
                    'number_block' => $project->number_block,
                    'number_floor' => $project->number_floor,
                    'number_flat' => $project->number_flat,
                    'is_featured' => $project->is_featured ? 'Yes' : 'No',
                    'date_finish' => $project->date_finish,
                    'date_sell' => $project->date_sell,
                    'price_from' => $project->price_from,
                    'price_to' => $project->price_to,
                    'currency' => $project->currency?->title,
                    'city' => $project->city_name,
                    'country' => $project->country_name,
                    'state' => $project->state_name,
                    'author_id' => $project->author_id,
                    'author_type' => $project->author_type,
                    'longitude' => $project->longitude,
                    'latitude' => $project->latitude,
                    'status' => $project->status,
                    'categories' => $project->categories->pluck('name')->implode(','),
                    'features' => $project->features->pluck('name')->implode(','),
                    'facilities' => $project->facilities->map(function ($facility) {
                        return $facility->name . ':' . $facility->pivot->distance;
                    })->implode(','),
                    'custom_fields' => $project->customFields->map(function ($field) {
                        return $field->name . ':' . $field->value;
                    })->implode(','),
                    'video_url' => $project->getMetaData('video_url', true),
                    'video_thumbnail' => $project->getMetaData('video_thumbnail', true),
                ];
            }));
        }

        // Fallback to sample data if no real data exists
        $currency = Currency::query()->inRandomOrder()->value('title');
        $investor = Investor::query()->inRandomOrder()->value('name');
        $author = Account::query()->inRandomOrder()->value('id');

        $sampleProjects = [
            [
                'id' => 1,
                'unique_id' => 'PJ-' . rand(1000, 9999),
                'name' => 'Luxury Apartment Complex',
                'description' => 'Modern luxury apartment complex with premium amenities',
                'content' => 'Detailed project content here',
                'images' => 'projects/1.png',
                'location' => '300 Goyette Overpass Lake Kailyn, DC 19522',
                'investor' => $investor,
                'number_block' => rand(1, 10),
                'number_floor' => rand(1, 50),
                'number_flat' => rand(100, 5000),
                'is_featured' => 'Yes',
                'date_finish' => Carbon::now()->addDays(61)->toDateString(),
                'date_sell' => Carbon::now()->subMonths(24)->toDateString(),
                'price_from' => rand(100, 10000),
                'price_to' => rand(1000, 100000),
                'currency' => $currency,
                'city' => null,
                'country' => null,
                'state' => null,
                'author_id' => $author,
                'author_type' => Account::class,
                'longitude' => '-76.72488',
                'latitude' => '43.478881',
                'status' => ProjectStatusEnum::SELLING,
                'categories' => 'Apartment,House,Villa,Land,Condo',
                'features' => 'Wifi,Parking,Garden,Security,Fitness center,Laundry Room,Pets Allow',
                'facilities' => 'Hospital:13km,Super Market:2km,School:3km',
                'custom_fields' => 'Year Built:2015,Total Units:100,Construction Type:Concrete',
                'video_url' => 'https://www.youtube.com/watch?v=project-example',
                'video_thumbnail' => 'projects/video-thumbnail.jpg',
            ],
        ];

        return new Collection($sampleProjects);
    }

    public function headings(): array
    {
        return apply_filters('real_estate_projects_import_template_headings', [
            'id' => 'ID',
            'unique_id' => 'Unique ID',
            'name' => 'Name',
            'description' => 'Description',
            'content' => 'Content',
            'images' => 'Images',
            'location' => 'Location',
            'investor' => 'Investor',
            'number_block' => 'Number Block',
            'number_floor' => 'Number Floor',
            'number_flat' => 'Number Flat',
            'is_featured' => 'Is Featured?',
            'date_finish' => 'Date Finish',
            'date_sell' => 'Date Sell',
            'price_from' => 'Price from',
            'price_to' => 'Price to',
            'currency' => 'Currency',
            'city' => 'City',
            'country' => 'Country',
            'state' => 'State',
            'author_id' => 'Author',
            'author_type' => 'Author Type',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
            'categories' => 'Categories',
            'features' => 'Features',
            'facilities' => 'Facilities',
            'custom_fields' => 'Custom Fields',
            'video_url' => 'Video URL',
            'video_thumbnail' => 'Video Thumbnail',
        ]);
    }

    public function map($row): array
    {
        return $row;
    }

    public function rules(): array
    {
        return apply_filters('real_estate_projects_import_template_rules', [
            'id' => 'nullable|integer',
            'unique_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:400',
            'content' => 'nullable|string',
            'images' => 'nullable|string|multiple',
            'location' => 'nullable|string|max:255',
            'investor' => 'nullable|string',
            'number_block' => 'nullable|integer|min:0|max:100000',
            'number_floor' => 'nullable|integer|min:0|max:100000',
            'number_flat' => 'nullable|integer|min:0|max:100000',
            'is_featured' => 'required|boolean (Yes or No)',
            'date_finish' => 'nullable|date_format:Y-m-d',
            'date_sell' => 'nullable|date_format:Y-m-d',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'author_id' => 'nullable|integer',
            'author_type' => 'nullable|string',
            'longitude' => 'nullable|string|max:20|regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            'latitude' => 'nullable|string|max:20|regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            'status' => 'required|enum:not_available,pre_sale,selling,sold,building',
            'categories' => 'nullable|string',
            'features' => 'nullable|string',
            'facilities' => 'nullable|string',
            'custom_fields' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'video_thumbnail' => 'nullable|string|max:255',
        ]);
    }
}
