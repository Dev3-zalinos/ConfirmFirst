<?php

namespace Theme\Homzen\Http\Resources;

use Botble\RealEstate\Models\Project;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'image_thumb' => $this->image_thumb,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->short_address,
            'formatted_price' => $this->formatted_price,
            'map_icon' => $this->map_icon,
            'status_html' => $this->status_html,
        ];
    }
}
