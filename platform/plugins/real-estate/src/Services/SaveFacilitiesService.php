<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;

class SaveFacilitiesService
{
    public function execute(Property|Project $item, array|string|null $facilities): void
    {
        $item->facilities()->detach();

        if (! $facilities || ! is_array($facilities)) {
            return;
        }

        $facilitiesToSync = [];

        foreach ($facilities as $facility) {
            if (empty($facility['id']) || $facility['id'] == '0') {
                continue;
            }

            $facilitiesToSync[$facility['id']] = [
                'distance' => $facility['distance'],
            ];
        }

        if (empty($facilitiesToSync)) {
            return;
        }

        $item->facilities()->syncWithoutDetaching($facilitiesToSync);
    }
}
