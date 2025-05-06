<ul class="search-suggestion">
    @forelse ($projects as $project)
        <li data-value="{{ $project->id }}" class="search-suggestion-item">{{ $project->name }}</li>
    @empty
        <li class="text-muted text-center">{{ __('No project found') }}</li>
    @endforelse
</ul>
