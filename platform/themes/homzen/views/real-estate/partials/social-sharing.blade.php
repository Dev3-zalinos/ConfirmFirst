@php
    $socialSharing = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons($model->url, $model->name);
@endphp
@if($socialSharing)
    <div class="property-share-social">
        <span>{{ __('Share:') }}</span>
        <ul class="list-social d-flex align-items-center">
            @foreach($socialSharing as $social)
                <li>
                    <a title="{{ $social['name'] }}" href="{{ $social['url'] }}" class="box-icon w-40 social" target="_blank">
                        {!! $social['icon'] !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
