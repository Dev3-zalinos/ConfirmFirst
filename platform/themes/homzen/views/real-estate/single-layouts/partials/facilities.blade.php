@if ($property->facilities->isNotEmpty())
    <div @class(['single-property-nearby', $class ?? null])>
        <div class="h7 title fw-7">{{ __('What’s nearby?') }}</div>
        <p class="body-2">{{ __("Explore nearby amenities to precisely locate your property and identify surrounding conveniences, providing a comprehensive overview of the living environment and the property's convenience.") }}</p>
        <ul class="grid-3 box-nearby">
            @foreach ($property->facilities as $facility)
                <li class="item-nearby">
                    <span class="label">
                        @if($facility->icon)
                            {!! BaseHelper::renderIcon($facility->icon) !!}
                        @else
                            {!! BaseHelper::renderIcon('ti ti-square-dot') !!}
                        @endif
                        {{ $facility->name }}:
                    </span>
                    <span class="fw-7">{{ $facility->pivot->distance }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
