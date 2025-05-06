<?php

namespace Botble\RealEstate\Exports;

use Botble\RealEstate\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertiesExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return Property::query()
            ->with([
                'project',
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
        return apply_filters('real_estate_properties_export_headings', [
            'ID',
            'Name',
            'Type',
            'Description',
            'Content',
            'Location',
            'Images',
            'Project',
            'Number bedroom',
            'Number bathroom',
            'Number floor',
            'Square',
            'Price',
            'Currency',
            'Is Featured?',
            'City',
            'Country',
            'State',
            'Period',
            'Author ID',
            'Author Type',
            'Auto renew',
            'Never Expired',
            'Latitude',
            'Longitude',
            'Views',
            'Status',
            'Moderation status',
            'Private Notes',
            'Unique ID',
            'Video URL',
            'Video Thumbnail',
            'Categories',
            'Features',
            'Facilities',
            'Custom Fields',
        ]);
    }

    public function map($row): array
    {
        $facilities = [];
        if (isset($row['facilities'])) {
            $facilities = $row['facilities'] instanceof Collection
                ? $row['facilities']->pluck('pivot.distance', 'name')->all()
                : $row['facilities'];
            array_walk(
                $facilities,
                function (&$v, $k): void {
                    $v = $k . ':' . $v;
                }
            );
        }

        $categories = [];
        if (isset($row['categories'])) {
            $categories = $row['categories'] instanceof Collection
                ? $row['categories']->pluck('name')->all()
                : $row['categories'];
        }

        $features = [];
        if (isset($row['features'])) {
            $features = $row['features'] instanceof Collection
                ? $row['features']->pluck('name')->all()
                : $row['features'];
        }

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

        return apply_filters('real_estate_properties_export_row_data', [
            $row->id,
            $row->name,
            $row->type,
            $row->description,
            $row->content,
            $row->location,
            is_array($row->images) ? implode(',', $row->images) : [],
            $row->project?->name,
            $row->number_bedroom,
            $row->number_bathroom,
            $row->number_floor,
            $row->square,
            $row->price,
            $row->currency?->title ?: cms_currency()->getDefaultCurrency()->title,
            $row->is_featured ? 'Yes' : 'No',
            $row->city_name,
            $row->country_name,
            $row->state_name,
            $row->period,
            $row->author_id,
            $row->author_type,
            $row->auto_renew ? 'Yes' : 'No',
            $row->never_expired ? 'Yes' : 'No',
            $row->latitude,
            $row->longitude,
            $row->views,
            $row->status,
            $row->moderation_status,
            $row->private_notes,
            $row->unique_id,
            $row->getMetaData('video_url', true),
            $row->getMetaData('video_thumbnail', true),
            implode(',', $categories),
            implode(',', $features),
            implode(',', $facilities),
            implode(',', $customFields),
        ], $row);
    }
}
