<h1>{{ __('Projects') }}</h1>
{!! Theme::breadcrumb()->render() !!}

<div class="row">
    @foreach ($projects as $project)
        <div
            class="col-sm-6 col-lg-4 col-xl-3"
        >
            @include('plugins/real-estate::themes.partials.projects.item', ['project' => $project])
        </div>
    @endforeach
</div>

<br>

{!! $projects->withQueryString()->links() !!}
