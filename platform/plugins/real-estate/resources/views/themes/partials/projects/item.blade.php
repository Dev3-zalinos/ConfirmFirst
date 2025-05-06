<div class="item">
    <div class="blii">
        <div class="img"><img class="thumb" data-src="{{ RvMedia::getImageUrl($project->image, 'small', false, RvMedia::getDefaultImage()) }}" src="{{ RvMedia::getImageUrl($project->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $project->name }}">
        </div>
        <a href="{{ $project->url }}" class="linkdetail" title="{{ $project->name }}"></a>
        <ul class="item-price-wrap hide-on-list"><li class="h-type"><span>{{ $project->category->name }}</span></li></ul>
    </div>

    <div class="description">
        <a href="{{ $project->url }}"><h3>{!! BaseHelper::clean($project->name) !!}</h3>
            @if($project->short_address)
                <p class="dia_chi"><i class="fas fa-map-marker-alt"></i> {{ $project->short_address }}</p>
            @endif
            @if ($project->price_from || $project->price_to)
                <p class="bold500">{{ __('Price') }}: {{ $project->formatted_price }}</p>
            @endif
        </a>
    </div>
</div>
