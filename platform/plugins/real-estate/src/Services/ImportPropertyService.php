<?php

namespace Botble\RealEstate\Services;

use Botble\ACL\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Botble\RealEstate\Concerns\ChunkImportable;
use Botble\RealEstate\Contracts\ChunkImportable as ChunkImportableContract;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportPropertyService implements ChunkImportableContract
{
    use ChunkImportable;

    protected bool $updateExisting = false;

    public function setUpdateExisting(bool $updateExisting): self
    {
        $this->updateExisting = $updateExisting;

        return $this;
    }

    public function save(array $row): void
    {
        $uniqueId = Arr::get($row, 'unique_id');
        $id = Arr::get($row, 'id');
        $property = null;

        // Check if we should update existing properties and if the property exists
        if ($this->updateExisting) {
            // First try to find by ID if provided
            if ($id) {
                $property = Property::query()->find($id);
            }

            // If not found by ID and unique_id is provided, try to find by unique_id
            if (! $property && $uniqueId) {
                $property = Property::query()->where('unique_id', $uniqueId)->first();
            }
        }

        // If unique_id is empty, set it to null to avoid unique constraint issues
        if (empty($uniqueId)) {
            $row['unique_id'] = null;
        }

        // If property doesn't exist or we're not updating existing properties, create a new one
        if (! $property) {
            $property = new Property();

            // Remove ID from data when creating a new property
            $itemData = apply_filters(
                'real_estate_properties_import_row_data_for_saving',
                Arr::except($row, ['id', 'categories', 'facilities', 'features', 'custom_fields', 'video_url', 'video_thumbnail']),
                $row
            );
        } else {
            $itemData = apply_filters(
                'real_estate_properties_import_row_data_for_saving',
                Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields', 'video_url', 'video_thumbnail']),
                $row
            );
        }

        $property->forceFill($itemData);
        $property->save();

        $property->categories()->sync(Arr::get($row, 'categories', []));

        // For facilities, we need to detach all existing ones first to avoid duplicate key errors
        if ($this->updateExisting && $property->exists) {
            $property->facilities()->detach();
        }

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $property->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        $property->features()->sync(Arr::get($row, 'features', []));

        if ($customFields = Arr::get($row, 'custom_fields')) {
            // Delete existing custom fields if we're updating an existing property
            if ($this->updateExisting && $property->exists) {
                $property->customFields()->delete();
            }

            foreach ($customFields as $field) {
                $property->customFields()->insert([
                    'name' => $field['name'],
                    'value' => $field['value'],
                    'reference_id' => $property->id,
                    'reference_type' => Property::class,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Delete existing metadata if we're updating an existing property
        if ($this->updateExisting && $property->exists) {
            $property->metadata()->whereIn('meta_key', ['video_url', 'video_thumbnail'])->delete();
        }

        if ($videoUrl = Arr::get($row, 'video_url')) {
            $property->metadata()->create([
                'meta_key' => 'video_url',
                'meta_value' => $videoUrl,
            ]);
        }

        if ($videoThumbnail = Arr::get($row, 'video_thumbnail')) {
            $property->metadata()->create([
                'meta_key' => 'video_thumbnail',
                'meta_value' => $videoThumbnail,
            ]);
        }

        $this->request->merge([...$row,
            'slug' => Str::slug($property->name),
            'is_slug_editable' => true,
        ]);

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $this->request, $property));
    }

    public function map($row): array
    {
        $dataFacilities = [];

        if (! empty($row['facilities'])) {
            $facilities = explode(',', Arr::get($row, 'facilities', ''));
            foreach ($facilities as $facility) {
                $facilityExplode = explode(':', $facility);
                $dataFacilities[Arr::first($this->getIdsFromString($facilityExplode[0], $this->facilityRepository))] = $facilityExplode[1];
            }
        }

        $images = explode(',', Arr::get($row, 'images', ''));

        // Convert project name to ID
        $projectId = 0;
        if (! empty($row['project'])) {
            $project = Project::query()->where('name', $row['project'])->first();
            if ($project) {
                $projectId = $project->id;
            }
        }

        // Convert country name to ID
        $countryId = 0;
        if (! empty($row['country']) && is_plugin_active('location')) {
            $country = Country::query()->where('name', $row['country'])->first();
            if ($country) {
                $countryId = $country->id;
            }
        }

        // Convert state name to ID
        $stateId = 0;
        if (! empty($row['state']) && is_plugin_active('location')) {
            $state = State::query()->where('name', $row['state'])->first();
            if ($state) {
                $stateId = $state->id;
            }
        }

        // Convert city name to ID
        $cityId = 0;
        if (! empty($row['city']) && is_plugin_active('location')) {
            $city = City::query()->where('name', $row['city'])->first();
            if ($city) {
                $cityId = $city->id;
            }
        }

        // Handle custom fields
        $customFields = [];
        if (! empty($row['custom_fields'])) {
            $fields = explode(',', Arr::get($row, 'custom_fields', ''));
            foreach ($fields as $field) {
                $fieldExplode = explode(':', $field);
                if (count($fieldExplode) === 2) {
                    $customFields[] = [
                        'name' => trim($fieldExplode[0]),
                        'value' => trim($fieldExplode[1]),
                    ];
                }
            }
        }

        $videoUrl = Arr::get($row, 'video_url');
        $videoThumbnail = Arr::get($row, 'video_thumbnail');

        $property = apply_filters('real_estate_properties_import_row_data', [
            'id' => Arr::get($row, 'id'),
            'name' => Arr::get($row, 'name'),
            'type' => Arr::get($row, 'type'),
            'description' => Arr::get($row, 'description'),
            'price' => Arr::get($row, 'price'),
            'number_bedroom' => Arr::get($row, 'number_bedroom'),
            'number_bathroom' => Arr::get($row, 'number_bathroom'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'square' => Arr::get($row, 'square'),
            'images' => $this->getImageURLs($images),
            'author_id' => Arr::get($row, 'author_id') ?: 0,
            'author_type' => Arr::get($row, 'author_type') ?: User::class,
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', 'no')),
            'content' => Arr::get($row, 'content'),
            'location' => Arr::get($row, 'location'),
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'auto_renew' => $this->yesNoToBoolean(Arr::get($row, 'auto_renew', 'no')),
            'expire_date' => Arr::get($row, 'expire_date'),
            'never_expired' => $this->yesNoToBoolean(Arr::get($row, 'never_expired', 'no')),
            'period' => Arr::get($row, 'period'),
            'moderation_status' => Arr::get($row, 'moderation_status'),
            'status' => Arr::get($row, 'status'),
            'project_id' => $projectId,
            'country_id' => $countryId,
            'state_id' => $stateId,
            'city_id' => $cityId,
            'private_notes' => Arr::get($row, 'private_notes'),
            'unique_id' => Arr::get($row, 'unique_id'),
            'facilities' => $dataFacilities,
            'custom_fields' => $customFields,
            'video_url' => $videoUrl,
            'video_thumbnail' => $videoThumbnail,
        ], $row);

        return $this->mapRelationships($row, $property);
    }
}
