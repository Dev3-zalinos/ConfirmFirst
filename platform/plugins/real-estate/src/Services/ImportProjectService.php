<?php

namespace Botble\RealEstate\Services;

use Botble\ACL\Models\User;
use Botble\Base\Events\CreatedContentEvent;
use Botble\RealEstate\Concerns\ChunkImportable;
use Botble\RealEstate\Contracts\ChunkImportable as ChunkImportableContract;
use Botble\RealEstate\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportProjectService implements ChunkImportableContract
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
        $project = null;

        // Check if we should update existing projects and if the project exists
        if ($this->updateExisting) {
            // First try to find by ID if provided
            if ($id) {
                $project = Project::query()->find($id);
            }

            // If not found by ID and unique_id is provided, try to find by unique_id
            if (! $project && $uniqueId) {
                $project = Project::query()->where('unique_id', $uniqueId)->first();
            }
        }

        // If unique_id is empty, set it to null to avoid unique constraint issues
        if (empty($uniqueId)) {
            $row['unique_id'] = null;
        }

        // If project doesn't exist or we're not updating existing projects, create a new one
        if (! $project) {
            $project = new Project();

            // Remove ID from data when creating a new project
            $itemData = apply_filters(
                'real_estate_projects_import_row_data_for_saving',
                Arr::except($row, ['id', 'categories', 'facilities', 'features', 'custom_fields', 'video_url', 'video_thumbnail']),
                $row
            );
        } else {
            $itemData = apply_filters(
                'real_estate_projects_import_row_data_for_saving',
                Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields', 'video_url', 'video_thumbnail']),
                $row
            );
        }

        $project->forceFill($itemData);
        $project->save();

        $project->categories()->sync(Arr::get($row, 'categories', []));
        $project->features()->sync(Arr::get($row, 'features', []));

        // For facilities, we need to detach all existing ones first to avoid duplicate key errors
        if ($this->updateExisting && $project->exists) {
            $project->facilities()->detach();
        }

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $project->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        if ($customFields = Arr::get($row, 'custom_fields')) {
            // Delete existing custom fields if we're updating an existing project
            if ($this->updateExisting && $project->exists) {
                $project->customFields()->delete();
            }

            foreach ($customFields as $field) {
                $project->customFields()->insert([
                    'name' => $field['name'],
                    'value' => $field['value'],
                    'reference_id' => $project->id,
                    'reference_type' => Project::class,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Delete existing metadata if we're updating an existing project
        if ($this->updateExisting && $project->exists) {
            $project->metadata()->whereIn('meta_key', ['video_url', 'video_thumbnail'])->delete();
        }

        // Save video metadata
        if ($videoUrl = Arr::get($row, 'video_url')) {
            $project->metadata()->create([
                'meta_key' => 'video_url',
                'meta_value' => $videoUrl,
            ]);
        }

        if ($videoThumbnail = Arr::get($row, 'video_thumbnail')) {
            $project->metadata()->create([
                'meta_key' => 'video_thumbnail',
                'meta_value' => $videoThumbnail,
            ]);
        }

        $this->request->merge(array_merge($row, [
            'slug' => Str::slug($project->name),
            'is_slug_editable' => true,
        ]));

        event(new CreatedContentEvent(PROJECT_MODULE_SCREEN_NAME, $this->request, $project));
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

        $images = explode(',', Arr::get($row, 'images', ''));

        // Extract video metadata fields
        $videoUrl = Arr::get($row, 'video_url');
        $videoThumbnail = Arr::get($row, 'video_thumbnail');

        $data = apply_filters('real_estate_projects_import_row_data', [
            'id' => Arr::get($row, 'id'),
            'name' => Arr::get($row, 'name'),
            'description' => Arr::get($row, 'description'),
            'content' => Arr::get($row, 'content'),
            'images' => $this->getImageURLs($images),
            'location' => Arr::get($row, 'location'),
            'number_block' => Arr::get($row, 'number_block'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'number_flat' => Arr::get($row, 'number_flat'),
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', 'no')),
            'date_finish' => Arr::get($row, 'date_finish') ?: null,
            'date_sell' => Arr::get($row, 'date_sell') ?: null,
            'price_from' => Arr::get($row, 'price_from'),
            'price_to' => Arr::get($row, 'price_to'),
            'author_id' => Arr::get($row, 'author_id') ?: 0,
            'author_type' => Arr::get($row, 'author_type') ?: User::class,
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'status' => Arr::get($row, 'status'),
            'facilities' => $dataFacilities,
            'custom_fields' => $customFields,
            'unique_id' => Arr::get($row, 'unique_id'),
            'video_url' => $videoUrl,
            'video_thumbnail' => $videoThumbnail,
        ]);

        return $this->mapRelationships($row, $data);
    }

    public function mapRelationships(mixed $row, array $data): array
    {
        $data['country_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'country'), $this->countryRepository));
        $data['state_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'state'), $this->stateRepository));
        $data['city_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'city'), $this->cityRepository));
        $data['author_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'author_id'), $this->accountRepository));
        $data['currency_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'currency'), $this->currencyRepository, 'title'));
        $data['investor_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'investor'), $this->investorRepository));
        $data['categories'] = $this->getIdsFromString(Arr::get($row, 'categories'), $this->categoryRepository);
        $data['features'] = $this->getIdsFromString(Arr::get($row, 'features'), $this->featureRepository);

        return $data;
    }
}
