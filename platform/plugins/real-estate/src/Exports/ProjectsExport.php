<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return Project::query()
            ->with([
                'investor',
                'categories',
                'features',
                'facilities',
                'customFields',
                'metadata',
            ])
            ->get();
    }

    public function headings(): array
    {
        return apply_filters('real_estate_projects_export_headings', [
            'id' => 'ID',
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
            'unique_id' => 'Unique ID',
            'video_url' => 'Video URL',
            'video_thumbnail' => 'Video Thumbnail',
        ]);
    }

    public function map($row): array
    {
        $facilities = $row->facilities->pluck('pivot.distance', 'name')->all();
        array_walk(
            $facilities,
            function (&$v, $k): void {
                $v = $k . ':' . $v;
            }
        );

        $customFields = [];
        if (isset($row['customFields'])) {
            if ($row['customFields'] instanceof Collection) {
                $customFields = $row['customFields']->map(function ($field) {
                    return $field->name . ':' . $field->value;
                })->all();
            } elseif (is_array($row['customFields'])) {
                $customFields = collect($row['customFields'])->map(function ($value, $name) {
                    return $name . ':' . $value;
                })->all();
            }
        }

        return apply_filters('real_estate_projects_export_row_data', [
            $row->id,
            $row->name,
            $row->description,
            $row->content,
            is_array($row->images) ? implode(',', $row->images) : [],
            $row->location,
            $row->investor?->name ?: $row->investor_id,
            $row->number_block,
            $row->number_floor,
            $row->number_flat,
            $row->is_featured ? 'Yes' : 'No',
            $row->date_finish,
            $row->date_sell,
            $row->price_from,
            $row->price_to,
            $row->currency?->title ?: cms_currency()->getDefaultCurrency()->title,
            $row->city_name,
            $row->country_name,
            $row->state_name,
            $row->author_id,
            $row->author_type,
            $row->longitude,
            $row->latitude,
            $row->status,
            implode(',', $row->categories->pluck('name')->all()),
            implode(',', $row->features->pluck('name')->all()),
            implode(',', $facilities),
            implode(',', $customFields),
            $row->unique_id,
            $row->getMetaData('video_url', true),
            $row->getMetaData('video_thumbnail', true),
        ], $row);
    }
}
