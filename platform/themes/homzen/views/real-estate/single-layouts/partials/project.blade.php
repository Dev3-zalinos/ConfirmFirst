@if (RealEstateHelper::isEnabledProjects() && $property->project_id && ($project = $property->project))
    <div @class(['single-property-project', $class ?? null])>
        <div class="h7 title fw-7">{{ __("Project's Information") }}</div>
        <div class="box-project mt-3">
            <div class="project-thumb">
                <a href="{{ $project->url }}">
                    {{ RvMedia::image($project->image, $project->name) }}
                </a>
            </div>
            <div class="project-info">
                <h5 class="title">
                    <a href="{{ $project->url }}">{{ $project->name }}</a>
                </h5>
                <div class="text-variant-1">
                    {{ Str::limit($project->description, 120) }}
                </div>
                <ul class="meta">
                    @if($project->short_address)
                        <li class="meta-item">
                            <x-core::icon name="ti ti-map-pin" />
                            {{ $project->short_address }}
                        </li>
                    @endif
                    @if ($project->investor->id)
                        <li class="meta-item">
                            <x-core::icon name="ti ti-building" />
                            {{ $project->investor->name }}
                        </li>
                    @elseif ($project->date_finish || $project->date_sell)
                        <li class="meta-item">
                            <x-core::icon name="ti ti-calendar" />
                            {{ Theme::formatDate($project->date_finish ?: $project->date_sell) }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
