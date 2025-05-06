@if ($property->features->isNotEmpty())
    <div @class(['single-property-feature', $class ?? null])>
        <div class="h7 title fw-7">{{ __('Amenities and features') }}</div>
        <div class="box-feature">
            <ul>
                @foreach ($property->features as $feature)
                    <li class="feature-item">
                        @if ($feature->icon)
                            {!! BaseHelper::renderIcon($feature->icon) !!}
                        @else
                            {!! BaseHelper::renderIcon('ti ti-square-dot') !!}
                        @endif
                        {{ $feature->name }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
