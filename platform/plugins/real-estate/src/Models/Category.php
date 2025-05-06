<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Contracts\HasTreeCategory as HasTreeCategoryContract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\HasTreeCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\HtmlString;

class Category extends BaseModel implements HasTreeCategoryContract
{
    use HasTreeCategory;

    protected $table = 're_categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'order',
        'is_default',
        'parent_id',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 're_property_categories')->with('slugable');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 're_project_categories')->with('slugable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    protected function badgeWithCount(): Attribute
    {
        return Attribute::get(function (): HtmlString {
            $html = '';

            if ($this->is_default) {
                $html .= sprintf(
                    '<span class="text-success" data-bs-toggle="tooltip" title="%s">%s</span>',
                    trans('plugins/real-estate::category.is_default'),
                    BaseHelper::renderIcon('ti ti-check', 'sm')
                );
            }

            $html .= BaseHelper::renderBadge(
                $this->projects_count,
                'info',
                [
                    'data-bs-toggle' => 'tooltip',
                    'title' => trans('plugins/real-estate::category.total_projects', ['total' => $this->projects_count]),
                ]
            );

            $html .= BaseHelper::renderBadge(
                $this->properties_count,
                'info',
                [
                    'data-bs-toggle' => 'tooltip',
                    'title' => trans('plugins/real-estate::category.total_properties', ['total' => $this->properties_count]),
                ]
            );

            return new HtmlString($html);
        });
    }

    protected static function booted(): void
    {
        self::deleting(function (Category $category): void {
            foreach ($category->children()->get() as $child) {
                $child->parent_id = $category->parent_id;
                $child->save();
            }

            $category->properties()->detach();
            $category->projects()->detach();
        });
    }
}
