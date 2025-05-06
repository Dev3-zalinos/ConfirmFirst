<?php

namespace Botble\RealEstate\Http\Resources;

use Botble\Base\Supports\Enum;
use Botble\RealEstate\Models\CustomField;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomField
 */
class CustomFieldResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'type' => $this->resource->type instanceof Enum
                ? $this->resource->type->getValue()
                : null,
            'options' => $this->resource->options,
        ];
    }
}
