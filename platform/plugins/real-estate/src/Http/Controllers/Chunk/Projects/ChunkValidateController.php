<?php

namespace Botble\RealEstate\Http\Controllers\Chunk\Projects;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Http\Controllers\Chunk\ChunkController;
use Botble\RealEstate\Http\Requests\ChunkFileRequest;
use Botble\RealEstate\Models\Project;
use Exception;
use Illuminate\Validation\Rule;

class ChunkValidateController extends ChunkController
{
    public function __invoke(ChunkFileRequest $request)
    {
        try {
            $filePath = $this->getFilePath($request->input('file'), 'app/project-import');

        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        $offset = $request->integer('offset');
        $limit = $request->integer('limit', 10);
        $rows = $this->getLocationRows($filePath, $offset, $limit);

        // Check if we're updating existing projects
        $updateExisting = $request->boolean('update_existing');

        $rules = [
            '*.name' => 'required|string|max:120',
            '*.description' => 'nullable|string|max:400',
            '*.content' => 'required|string',
            '*.number_block' => 'numeric|min:0|max:100000|nullable',
            '*.number_floor' => 'numeric|min:0|max:100000|nullable',
            '*.number_flat' => 'numeric|min:0|max:100000|nullable',
            '*.price_from' => 'numeric|min:0|nullable',
            '*.price_to' => 'numeric|min:0|nullable',
            '*.latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            '*.longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            '*.status' => Rule::in(ProjectStatusEnum::values()),
            '*.date_finish' => 'nullable|date',
            '*.date_sell' => 'nullable|date',
        ];

        // Validate the basic rules first
        $failed = $this->validator($rows, $rules);

        // If we're not updating existing projects, validate unique_id to be unique
        if (! $updateExisting) {
            // Get all unique_ids from the rows
            $uniqueIds = [];
            foreach ($rows as $index => $row) {
                if (! empty($row['unique_id'])) {
                    $uniqueIds[$index] = $row['unique_id'];
                }
            }

            // Check for duplicates in the current batch
            $duplicateUniqueIds = array_diff_assoc($uniqueIds, array_unique($uniqueIds));

            // Check for existing unique_ids in the database
            $existingUniqueIds = [];
            if (! empty($uniqueIds)) {
                $existingUniqueIds = Project::query()
                    ->whereIn('unique_id', array_values($uniqueIds))
                    ->pluck('unique_id')
                    ->toArray();
            }

            // Mark rows with duplicate or existing unique_ids as failed
            foreach ($uniqueIds as $index => $uniqueId) {
                if (in_array($uniqueId, $duplicateUniqueIds) || in_array($uniqueId, $existingUniqueIds)) {
                    if (! isset($failed[$index])) {
                        $failed[$index] = [];
                    }
                    // Add row number (offset + index + 1) to make it easier to identify in the UI
                    $failed[$index]['row'] = $offset + $index + 1;
                    $failed[$index]['unique_id'] = [
                        trans('plugins/real-estate::import.project_unique_id_exists', ['id' => $uniqueId]),
                    ];
                }
            }
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::import.validating_message', [
                'from' => number_format($offset),
                'to' => number_format($offset + count($rows)),
            ]))
            ->setData([
                'offset' => $offset + count($rows),
                'count' => count($rows),
                'failed' => $failed,
            ]);
    }
}
