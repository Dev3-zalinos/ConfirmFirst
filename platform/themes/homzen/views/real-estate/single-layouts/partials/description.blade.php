@if ($property->content || $property->private_notes)
    <div @class(['single-property-desc', $class ?? null])>
        @if($property->content)
            <div class="h7 title fw-7">{{ __('Description') }}</div>
            <div class="body-2 text-variant-1">
                <div class="ck-content single-detail">
                    {!! BaseHelper::clean($property->content) !!}
                </div>
            </div>
        @endif

        @if($property->can_see_private_notes && $property->private_notes)
            <div class="bd-callout bd-callout-info">
                <div class="h7 title fw-7 mb-2">{{ __('Private Notes') }}</div>

                {!! BaseHelper::clean(nl2br($property->private_notes)) !!}
            </div>
        @endif
    </div>
@endif

<div @class(['single-property-overview', $class ?? null])>
    <div class="h7 title fw-7">{{ __('Overview') }}</div>
    <div class="row row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4 info-box">
        <div class="col item">
            <div class="box-icon w-52">
                <x-core::icon name="ti ti-home" />
            </div>
            <div class="content">
                <span class="label">{{ __('Property ID:') }}</span>
                <span>{{ $property->unique_id ?: $property->getKey() }}</span>
            </div>
        </div>
        @if ($property->categories->isNotEmpty())
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-category" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Type:') }}</span>
                    <span>
                        {{ $property->categories->map(fn ($item) => $item->name)->implode(', ') }}
                    </span>
                </div>
            </div>
        @endif
        @if ($property->number_bedroom !== null)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-bed" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Bedrooms:') }}</span>
                    <span>{{ $property->number_bedroom === 0 ? __('Studio') : number_format($property->number_bedroom) }}</span>
                </div>
            </div>
        @endif
        @if ($property->number_bathroom)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-bath" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Bathrooms:') }}</span>
                    <span>{{ number_format($property->number_bathroom) }}</span>
                </div>
            </div>
        @endif
        @if ($property->number_floor)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-stairs" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Floors:') }}</span>
                    <span>{{ number_format($property->number_floor) }}</span>
                </div>
            </div>
        @endif
        @if ($property->square)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-ruler-2" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Square:') }}</span>
                    <span>{{ $property->square_text }}</span>
                </div>
            </div>
        @endif
        @foreach ($property->customFields as $customField)
            @continue(! $customField->value)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-box" />
                </div>
                <div class="content">
                    <span class="label">{!! BaseHelper::clean($customField->name) !!}:</span>
                    <span>{!! BaseHelper::clean($customField->value) !!}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
