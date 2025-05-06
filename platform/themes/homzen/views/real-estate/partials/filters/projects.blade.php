@if (theme_option('real_estate_enable_filter_by_project', 'yes') == 'yes' && RealEstateHelper::isEnabledProjects())
    @php
        $value = null;

        if (request()->has('project_id')) {
            $project = \Botble\RealEstate\Models\Project::query()->find(request()->input('project_id'));

            if ($project) {
                $value = $project->name;
            }
        }
    @endphp

    <div @class(['box-select',  $class ?? null]) data-bb-toggle="search-suggestion">
        <label class="title-select fw-5">{{ __('Project') }}</label>
        <input type="hidden" name="project_id">
        <div class="position-relative">
            <input
                type="text"
                class="form-control"
                placeholder="{{ __('Search project') }}"
                value="{{ $value }}"
                autocomplete="off"
                data-url="{{ route('public.ajax.projects.search') }}"
            />
            <div data-bb-toggle="data-suggestion"></div>
        </div>
    </div>
@endif

