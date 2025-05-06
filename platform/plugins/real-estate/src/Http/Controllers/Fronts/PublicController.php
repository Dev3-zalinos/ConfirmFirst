<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fronts\ConsultForm;
use Botble\RealEstate\Http\Requests\SendConsultRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PublicController extends BaseController
{
    public function postSendConsult(SendConsultRequest $request)
    {
        abort_unless(RealEstateHelper::isEnabledConsultForm(), 404);

        do_action('form_extra_fields_validate', $request, ConsultForm::class);

        try {
            $sendTo = null;
            $link = null;
            $subject = null;

            if ($request->input('type') == 'project') {
                $request->merge(['project_id' => $request->input('data_id')]);
                $project = Project::query()
                    ->where('id', $request->input('data_id'))
                    ->with('author')
                    ->first();

                if ($project) {
                    $link = $project->url;
                    $subject = $project->name;

                    if ($project->author?->email) {
                        $sendTo = $project->author->email;
                    }
                }
            } else {
                $request->merge(['property_id' => $request->input('data_id')]);
                $property = Property::query()
                    ->where('id', $request->input('data_id'))
                    ->with('author')
                    ->first();

                if ($property) {
                    $link = $property->url;
                    $subject = $property->name;

                    if ($property->author?->email) {
                        $sendTo = $property->author->email;
                    }
                }
            }

            $data = [
                ...$request->input(),
                'ip_address' => $request->ip(),
            ];

            if (Arr::has($data, 'consult_custom_fields')) {
                $customFields = ConsultCustomField::query()
                    ->wherePublished()
                    ->with('options')
                    ->get();

                $data['custom_fields'] = collect($data['consult_custom_fields'])
                    ->mapWithKeys(function ($item, $id) use ($customFields) {
                        $field = $customFields->firstWhere('id', $id);
                        $option = $field->options->firstWhere('value', $item);

                        if (! $field) {
                            return [];
                        }

                        $value = match ($field->type->getValue()) {
                            ConsultCustomFieldTypeEnum::CHECKBOX => $item ? __('Yes') : __('No'),
                            ConsultCustomFieldTypeEnum::RADIO, ConsultCustomFieldTypeEnum::DROPDOWN => $option?->label,
                            default => $item,
                        };

                        return [$field->name => $value];
                    })->all();
            }

            $consult = Consult::query()->create($data);

            EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'consult_name' => $consult->name,
                    'consult_email' => $consult->email,
                    'consult_phone' => $consult->phone,
                    'consult_content' => $consult->content,
                    'consult_link' => $link,
                    'consult_subject' => $subject,
                    'consult_ip_address' => $consult->ip_address,
                    'consult_custom_fields' => $data['custom_fields'] ?? [],
                ])
                ->sendUsingTemplate('notice', $sendTo);

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/real-estate::consult.email.success'));
        } catch (Exception $exception) {
            BaseHelper::logError($exception);

            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/real-estate::consult.email.failed'));
        }
    }

    public function getProjects(Request $request)
    {
        SeoHelper::setTitle(__('Projects'));

        $projects = RealEstateHelper::getProjectsFilter((int) theme_option('number_of_projects_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            $view = Theme::getThemeNamespace('partials.real-estate.projects.items');

            if (! view()->exists($view)) {
                $view = Theme::getThemeNamespace('views.real-estate.projects.index');
            }

            return $this
                ->httpResponse()
                ->setData(view($view, compact('projects'))->render());
        }

        return Theme::scope('real-estate.projects', compact('projects'), 'plugins/real-estate::themes.projects')->render();
    }

    public function getProperties(Request $request)
    {
        SeoHelper::setTitle(__('Properties'));

        $properties = RealEstateHelper::getPropertiesFilter((int) theme_option('number_of_properties_per_page') ?: 12, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->query('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            $view = Theme::getThemeNamespace('partials.real-estate.properties.items');

            if (! view()->exists($view)) {
                $view = Theme::getThemeNamespace('views.real-estate.properties.index');
            }

            return $this
                ->httpResponse()
                ->setData(view($view, compact('properties'))->render());
        }

        return Theme::scope('real-estate.properties', compact('properties'), 'plugins/real-estate::themes.properties')->render();
    }

    public function changeCurrency(Request $request, $title = null)
    {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (! $title) {
            return $this->httpResponse();
        }

        /**
         * @var Currency $currency
         */
        $currency = Currency::query()
            ->where('title', $title)
            ->first();

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $this->httpResponse();
    }

    public function getProjectsByCity(string $slug, Request $request)
    {
        $city = City::query()->wherePublished()->where('slug', $slug)->firstOrFail();

        SeoHelper::setTitle(__('Projects in :city', ['city' => $city->name]));

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.projects-by-city', $city->slug));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_projects_per_page', 12);

        $request->merge(['city' => $slug, 'city_id' => $city->id]);

        $projects = RealEstateHelper::getProjectsFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        return Theme::scope('real-estate.projects', [
            'projects' => $projects,
            'ajaxUrl' => route('public.projects-by-city', $city->slug),
            'actionUrl' => route('public.projects-by-city', $city->slug),
        ], 'plugins/real-estate::themes.projects')
            ->render();
    }

    public function getPropertiesByCity(string $slug, Request $request)
    {
        $city = City::query()->wherePublished()->where('slug', $slug)->firstOrFail();

        SeoHelper::setTitle(__('Properties in :city', ['city' => $city->name]));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, CITY_MODULE_SCREEN_NAME, $city);

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.properties-by-city', $city->slug));

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_properties_per_page', 12);

        $request->merge(['city' => $slug, 'city_id' => $city->id]);

        $properties = RealEstateHelper::getPropertiesFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.properties.items', ['properties' => $properties]));
        }

        return Theme::scope('real-estate.properties', [
            'properties' => $properties,
            'ajaxUrl' => route('public.properties-by-city', $city->slug),
            'actionUrl' => route('public.properties-by-city', $city->slug),
        ], 'plugins/real-estate::themes.properties')
            ->render();
    }

    public function getProjectsByState(string $slug, Request $request)
    {
        $state = State::query()
            ->wherePublished()
            ->where('slug', $slug)
            ->firstOrFail();

        SeoHelper::setTitle(__('Projects in :state', ['state' => $state->name]));

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.projects-by-city', $state->slug));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, STATE_MODULE_SCREEN_NAME, $state);

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_projects_per_page', 12);

        $request->merge(['state' => $slug, 'state_id' => $state->id]);

        $projects = RealEstateHelper::getProjectsFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $projects]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.projects.items', ['projects' => $projects]));
        }

        return Theme::scope('real-estate.projects', [
            'projects' => $projects,
            'ajaxUrl' => route('public.projects-by-state', $state->slug),
            'actionUrl' => route('public.projects-by-state', $state->slug),
        ], 'plugins/real-estate::themes.projects')
            ->render();
    }

    public function getPropertiesByState(string $slug, Request $request)
    {
        $state = State::query()
            ->wherePublished()
            ->where('slug', $slug)
            ->firstOrFail();

        SeoHelper::setTitle(__('Properties in :state', ['state' => $state->name]));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, STATE_MODULE_SCREEN_NAME, $state);

        Theme::breadcrumb()
            ->add(SeoHelper::getTitle(), route('public.properties-by-state', $state->slug));

        $perPage = $request->integer('per_page') ?: (int) theme_option('number_of_properties_per_page', 12);

        $request->merge(['state' => $slug, 'state_id' => $state->id]);

        $properties = RealEstateHelper::getPropertiesFilter($perPage, RealEstateHelper::getReviewExtraData());

        if ($request->ajax()) {
            if ($request->input('minimal')) {
                return $this
                    ->httpResponse()
                    ->setData(Theme::partial('search-suggestion', ['items' => $properties]));
            }

            return $this
                ->httpResponse()
                ->setData(Theme::partial('real-estate.properties.items', ['properties' => $properties]));
        }

        return Theme::scope('real-estate.properties', [
            'properties' => $properties,
            'ajaxUrl' => route('public.properties-by-state', $state->slug),
            'actionUrl' => route('public.properties-by-state', $state->slug),
        ], 'plugins/real-estate::themes.properties')
            ->render();
    }

    public function getAgents()
    {
        abort_if(RealEstateHelper::isDisabledPublicProfile(), 404);

        $accounts = Account::query()
            ->where('is_public_profile', true)
            ->orderByDesc('is_featured')
            ->oldest('first_name')
            ->withCount([
                'properties' => function ($query) {
                    return RepositoryHelper::applyBeforeExecuteQuery($query, $query->getModel());
                },
            ])
            ->with(['avatar'])
            ->paginate(12);

        SeoHelper::setTitle(__('Agents'));

        Theme::breadcrumb()->add(__('Agents'), route('public.agents'));

        return Theme::scope('real-estate.agents', compact('accounts'), 'plugins/real-estate::themes.agents')->render();
    }
}
