<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultCustomFieldOption extends BaseModel
{
    protected $table = 're_consult_custom_field_options';

    protected $fillable = [
        'custom_field_id',
        'label',
        'value',
        'order',
    ];

    protected $casts = [
        'order' => 'int',
    ];

    public function customField(): BelongsTo
    {
        return $this->belongsTo(ConsultCustomField::class, 'custom_field_id');
    }
}
