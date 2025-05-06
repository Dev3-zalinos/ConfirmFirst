<h1>{{ $category->name }}</h1>
<p>{{ $category->description }}</p>
{!! Theme::breadcrumb()->render() !!}

<h3>{{ __('Properties in :name', ['name' => $category->name]) }}</h3>

<div class="row">
    @foreach ($properties as $property)
        <div
            class="col-sm-6 col-lg-4 col-xl-3"
        >
            @include('plugins/real-estate::themes.partials.properties.item', ['property' => $property])
        </div>
    @endforeach
</div>

<br>

{!! $properties->withQueryString()->links() !!}
