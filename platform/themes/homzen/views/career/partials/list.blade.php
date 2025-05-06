<div class="career-list row row-cols-1 row-cols-lg-2">
    @foreach($careers as $career)
        <div class="col mb-4">
            <div class="career-item">
                <div>
                    <h3 class="career-title line-clamp-1">
                        <a href="{{ $career->url }}" title="{{ $career->name }}">{{ $career->name }}</a>
                    </h3>
                    <div class="career-meta">
                        <div class="career-meta-item">
                            <x-core::icon name="ti ti-clock" />
                            {{ Theme::formatDate($career->created_at) }}
                        </div>
                        <div class="career-meta-item">
                            <x-core::icon name="ti ti-map-pin" />
                            {{ $career->location }}
                        </div>
                        <div class="career-meta-item">
                            <x-core::icon name="ti ti-cash" />
                            {{ $career->salary }}
                        </div>
                    </div>
                </div>
                @if($career->description)
                    <div class="career-description line-clamp-1">
                        {{ $career->description }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
