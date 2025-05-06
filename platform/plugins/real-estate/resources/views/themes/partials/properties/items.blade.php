<div id="loading">
    <div class="half-circle-spinner">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
</div>

<input type="hidden" name="page" data-value="{{ $properties->currentPage() }}">
@if ($properties->isNotEmpty())
    <div class="row">
        @foreach ($properties as $property)
            <div class="colm10 property-item" data-lat="{{ $property->latitude }}" data-long="{{ $property->longitude }}">
                @include('plugins/real-estate::themes.partials.properties.item', compact('property'))
            </div>
        @endforeach
    </div>
    <br>
@endif

<div class="col-sm-12">
    <nav class="d-flex justify-content-center pt-3" aria-label="Page navigation example">
        {!! $properties->withQueryString()->onEachSide(1)->links() !!}
    </nav>
</div>
