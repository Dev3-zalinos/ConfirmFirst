<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection(): Collection
    {
        // Try to get real data first
        $properties = Property::query()
            ->with([
                'project',
                'categories',
                'features',
                'facilities',
                'customFields',
                'metadata',
            ])
            ->latest()
            ->limit(3)
            ->get();

        if ($properties->isNotEmpty()) {
            return new Collection($properties->map(function ($property) {
                $customFields = [];
                if ($property->customFields->isNotEmpty()) {
                    if ($property->customFields instanceof \Illuminate\Database\Eloquent\Collection) {
                        $customFields = $property->customFields->map(function ($field) {
                            return $field->name . ':' . $field->value;
                        })->all();
                    } elseif (is_array($property->customFields)) {
                        $customFields = collect($property->customFields)->map(function ($value, $name) {
                            return $name . ':' . $value;
                        })->all();
                    }
                }

                return [
                    'id' => $property->id,
                    'unique_id' => $property->unique_id,
                    'name' => $property->name,
                    'type' => $property->type,
                    'description' => $property->description,
                    'content' => $property->content,
                    'location' => $property->location,
                    'images' => is_array($property->images) ? implode(',', $property->images) : $property->images,
                    'project' => $property->project?->name,
                    'number_bedroom' => $property->number_bedroom,
                    'number_bathroom' => $property->number_bathroom,
                    'number_floor' => $property->number_floor,
                    'square' => $property->square,
                    'price' => $property->price,
                    'currency' => $property->currency?->title ?: cms_currency()->getDefaultCurrency()->title,
                    'is_featured' => $property->is_featured ? 'Yes' : 'No',
                    'city' => $property->city_name,
                    'country' => $property->country_name,
                    'state' => $property->state_name,
                    'period' => $property->period,
                    'author_id' => $property->author_id,
                    'author_type' => $property->author_type,
                    'auto_renew' => $property->auto_renew ? 'Yes' : 'No',
                    'never_expired' => $property->never_expired ? 'Yes' : 'No',
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'views' => $property->views,
                    'status' => $property->status,
                    'moderation_status' => $property->moderation_status,
                    'private_notes' => $property->private_notes,
                    'video_url' => $property->getMetaData('video_url', true),
                    'video_thumbnail' => $property->getMetaData('video_thumbnail', true),
                    'categories' => $property->categories->pluck('name')->implode(','),
                    'features' => $property->features->pluck('name')->implode(','),
                    'facilities' => $property->facilities->map(function ($facility) {
                        return $facility->name . ':' . $facility->pivot->distance;
                    })->implode(','),
                    'custom_fields' => implode(',', $customFields),
                ];
            }));
        }

        // Fallback to sample data if no real data exists
        $currency = Currency::query()->inRandomOrder()->value('title');
        $project = Project::query()->inRandomOrder()->value('name');
        $author = Account::query()->inRandomOrder()->value('id');

        $sampleProperties = [
            [
                'id' => 1,
                'unique_id' => 'PR-' . rand(1000, 9999),
                'name' => 'Luxury Villa with Ocean View',
                'type' => PropertyTypeEnum::SALE(),
                'description' => 'Stunning luxury villa with panoramic ocean views, modern amenities, and spacious living areas.',
                'content' => 'This magnificent villa features 5 bedrooms, 6 bathrooms, a gourmet kitchen, infinity pool, and private beach access. Perfect for those seeking luxury living.',
                'location' => '123 Ocean Drive, Malibu, CA 90265',
                'images' => 'properties/villa-1.jpg,properties/villa-2.jpg,properties/villa-3.jpg',
                'project' => $project,
                'number_bedroom' => 5,
                'number_bathroom' => 6,
                'number_floor' => 2,
                'square' => 4500,
                'price' => 2500000,
                'currency' => $currency,
                'is_featured' => 'Yes',
                'city' => null,
                'country' => null,
                'state' => null,
                'period' => PropertyPeriodEnum::YEAR,
                'author_id' => $author,
                'author_type' => Account::class,
                'auto_renew' => 'Yes',
                'never_expired' => 'No',
                'latitude' => '34.025922',
                'longitude' => '-118.779757',
                'views' => 0,
                'status' => PropertyStatusEnum::SELLING(),
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'private_notes' => 'High-end client interested. Follow up next week.',
                'video_url' => 'https://www.youtube.com/watch?v=example1',
                'video_thumbnail' => 'properties/video-thumbnail-1.jpg',
                'categories' => 'Luxury,Villa,Ocean View',
                'features' => 'Swimming Pool,Garden,Garage,Security System',
                'facilities' => 'Beach:0.1,Shopping Mall:2.5,School:1.2',
                'custom_fields' => 'ABC:123,XYZ:456',
            ],
            [
                'id' => 2,
                'unique_id' => 'PR-' . rand(1000, 9999),
                'name' => 'Modern Downtown Apartment',
                'type' => PropertyTypeEnum::RENT(),
                'description' => 'Contemporary apartment in the heart of downtown with stunning city views.',
                'content' => 'This modern 2-bedroom apartment features floor-to-ceiling windows, high-end finishes, and access to building amenities including gym and rooftop terrace.',
                'location' => '456 Main Street, New York, NY 10001',
                'images' => 'properties/apartment-1.jpg,properties/apartment-2.jpg',
                'project' => $project,
                'number_bedroom' => 2,
                'number_bathroom' => 2,
                'number_floor' => 15,
                'square' => 1200,
                'price' => 3500,
                'currency' => $currency,
                'is_featured' => 'No',
                'city' => null,
                'country' => null,
                'state' => null,
                'period' => PropertyPeriodEnum::MONTH,
                'author_id' => $author,
                'author_type' => Account::class,
                'auto_renew' => 'Yes',
                'never_expired' => 'No',
                'latitude' => '40.712776',
                'longitude' => '-74.005974',
                'views' => 0,
                'status' => PropertyStatusEnum::RENTING(),
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'private_notes' => 'Tenant prefers 2-year lease. Negotiate price.',
                'video_url' => 'https://www.youtube.com/watch?v=example2',
                'video_thumbnail' => 'properties/video-thumbnail-2.jpg',
                'categories' => 'Apartment,Downtown,Modern',
                'features' => 'Gym,Rooftop Terrace,Concierge,Parking',
                'facilities' => 'Subway:0.2,Restaurant:0.1,Park:0.5',
                'custom_fields' => 'ABC:123,XYZ:456',
            ],
            [
                'id' => 3,
                'unique_id' => 'PR-' . rand(1000, 9999),
                'name' => 'Family Home in Suburbs',
                'type' => PropertyTypeEnum::SALE(),
                'description' => 'Spacious family home in a quiet suburban neighborhood with excellent schools.',
                'content' => 'This charming 4-bedroom home features a large backyard, modern kitchen, and plenty of space for family living. Located in a family-friendly neighborhood with top-rated schools nearby.',
                'location' => '789 Oak Avenue, Chicago, IL 60601',
                'images' => 'properties/home-1.jpg,properties/home-2.jpg,properties/home-3.jpg',
                'project' => $project,
                'number_bedroom' => 4,
                'number_bathroom' => 3,
                'number_floor' => 2,
                'square' => 2800,
                'price' => 750000,
                'currency' => $currency,
                'is_featured' => 'Yes',
                'city' => null,
                'country' => null,
                'state' => null,
                'period' => PropertyPeriodEnum::YEAR,
                'author_id' => $author,
                'author_type' => Account::class,
                'auto_renew' => 'No',
                'never_expired' => 'No',
                'latitude' => '41.878113',
                'longitude' => '-87.629799',
                'views' => 0,
                'status' => PropertyStatusEnum::SELLING(),
                'moderation_status' => ModerationStatusEnum::APPROVED,
                'private_notes' => 'Seller motivated. Open to offers below asking price.',
                'video_url' => 'https://www.youtube.com/watch?v=example3',
                'video_thumbnail' => 'properties/video-thumbnail-3.jpg',
                'categories' => 'House,Family,Suburban',
                'features' => 'Backyard,Garage,Fireplace,Central AC',
                'facilities' => 'School:0.3,Park:0.4,Shopping Center:1.0',
                'custom_fields' => 'ABC:123,XYZ:456',
            ],
        ];

        return new Collection($sampleProperties);
    }

    public function headings(): array
    {
        return apply_filters('real_estate_properties_export_headings', [
            'id' => 'ID',
            'unique_id' => 'Unique ID',
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'content' => 'Content',
            'location' => 'Location',
            'images' => 'Images',
            'project' => 'Project',
            'number_bedroom' => 'Number bedroom',
            'number_bathroom' => 'Number bathroom',
            'number_floor' => 'Number floor',
            'square' => 'Square',
            'price' => 'Price',
            'currency' => 'Currency',
            'is_featured' => 'Is Featured?',
            'city' => 'City',
            'country' => 'Country',
            'state' => 'State',
            'period' => 'Period',
            'author_id' => 'Author ID',
            'author_type' => 'Author Type',
            'auto_renew' => 'Auto renew',
            'never_expired' => 'Never Expired',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'views' => 'Views',
            'status' => 'Status',
            'moderation_status' => 'Moderation status',
            'private_notes' => 'Private Notes',
            'video_url' => 'Video URL',
            'video_thumbnail' => 'Video Thumbnail',
            'categories' => 'Categories',
            'features' => 'Features',
            'facilities' => 'Facilities',
            'custom_fields' => 'Custom Fields',
        ]);
    }

    public function map($row): array
    {
        $facilities = '';
        if (isset($row['facilities'])) {
            if ($row['facilities'] instanceof \Illuminate\Database\Eloquent\Collection) {
                $facilities = $row['facilities']->map(function ($facility) {
                    return $facility->name . ':' . $facility->pivot->distance;
                })->implode(',');
            } elseif (is_array($row['facilities'])) {
                $facilities = collect($row['facilities'])->map(function ($distance, $name) {
                    return $name . ':' . $distance;
                })->implode(',');
            } else {
                $facilities = $row['facilities'];
            }
        }

        $categories = '';
        if (isset($row['categories'])) {
            if ($row['categories'] instanceof \Illuminate\Database\Eloquent\Collection) {
                $categories = $row['categories']->pluck('name')->implode(',');
            } elseif (is_array($row['categories'])) {
                $categories = implode(',', $row['categories']);
            } else {
                $categories = $row['categories'];
            }
        }

        $features = '';
        if (isset($row['features'])) {
            if ($row['features'] instanceof \Illuminate\Database\Eloquent\Collection) {
                $features = $row['features']->pluck('name')->implode(',');
            } elseif (is_array($row['features'])) {
                $features = implode(',', $row['features']);
            } else {
                $features = $row['features'];
            }
        }

        $customFields = '';
        if (isset($row['custom_fields'])) {
            if ($row['custom_fields'] instanceof \Illuminate\Database\Eloquent\Collection) {
                $customFields = $row['custom_fields']->pluck('id')->implode(',');
            } elseif (is_array($row['custom_fields'])) {
                $customFields = implode(',', $row['custom_fields']);
            } else {
                $customFields = $row['custom_fields'];
            }
        }

        return apply_filters('real_estate_properties_export_row_data', [
            $row['id'] ?? null,
            $row['unique_id'] ?? null,
            $row['name'] ?? null,
            $row['type'] ?? null,
            $row['description'] ?? null,
            $row['content'] ?? null,
            $row['location'] ?? null,
            is_array($row['images'] ?? null) ? implode(',', $row['images']) : ($row['images'] ?? null),
            $row['project'] ?? null,
            $row['number_bedroom'] ?? null,
            $row['number_bathroom'] ?? null,
            $row['number_floor'] ?? null,
            $row['square'] ?? null,
            $row['price'] ?? null,
            $row['currency'] ?? null,
            $row['is_featured'] ? 'Yes' : 'No',
            $row['city'] ?? null,
            $row['country'] ?? null,
            $row['state'] ?? null,
            $row['period'] ?? null,
            $row['author_id'] ?? null,
            $row['author_type'] ?? null,
            $row['auto_renew'] ? 'Yes' : 'No',
            $row['never_expired'] ? 'Yes' : 'No',
            $row['latitude'] ?? null,
            $row['longitude'] ?? null,
            $row['views'] ?? null,
            $row['status'] ?? null,
            $row['moderation_status'] ?? null,
            $row['private_notes'] ?? null,
            $row['video_url'] ?? null,
            $row['video_thumbnail'] ?? null,
            $categories,
            $features,
            $facilities,
            $customFields,
        ], $row);
    }

    public function rules(): array
    {
        return apply_filters('real_estate_properties_import_template_rules', [
            'id' => 'nullable|integer',
            'unique_id' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|enum:rent,sale',
            'description' => 'nullable|string|max:400',
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'images' => 'nullable|string|multiple',
            'project' => 'nullable|string',
            'number_bedroom' => 'nullable|integer|min:0|max:100000',
            'number_bathroom' => 'nullable|integer|min:0|max:100000',
            'number_floor' => 'nullable|integer|min:0|max:100000',
            'square' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string',
            'is_featured' => 'required|boolean (Yes or No)',
            'city' => 'nullable|integer|exists:cities,id',
            'country' => 'nullable|integer|exists:countries,id',
            'state' => 'nullable|integer|exists:states,id',
            'period' => 'nullable|enum:day,month,year',
            'author_id' => 'nullable|integer',
            'author_type' => 'nullable|string',
            'auto_renew' => 'required|boolean (Yes or No)',
            'never_expired' => 'required|boolean (Yes or No)',
            'latitude' => 'nullable|string|max:20|regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            'longitude' => 'nullable|string|max:20|regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            'views' => 'nullable|integer|min:0',
            'status' => 'required|enum:not_available,pre_sale,selling,sold,renting,rented,building',
            'moderation_status' => 'required|enum:approved,pending,rejected (default: pending)',
            'private_notes' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'video_thumbnail' => 'nullable|string|max:255',
            'categories' => 'nullable|string',
            'features' => 'nullable|string',
            'facilities' => 'nullable|string',
            'custom_fields' => 'nullable|string',
        ]);
    }
}
