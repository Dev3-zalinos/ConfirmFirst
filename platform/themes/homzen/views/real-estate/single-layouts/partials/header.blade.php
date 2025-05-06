<div class="header-property-detail">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            {!! BaseHelper::clean($property->status_html) !!}
            <h4 class="title">
                {!! BaseHelper::clean($property->name) !!}
            </h4>
        </div>

        <div class="box-price d-flex align-items-center">
            <h4>{{ $property->price_html }}</h4>
        </div>
    </div>
    @include(Theme::getThemeNamespace('views.real-estate.partials.meta'), ['model' => $property])
</div>
