<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Exports\ProjectsExport;
use Botble\RealEstate\Models\Project;
use Maatwebsite\Excel\Excel;

class ExportProjectController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::project.name'), route('project.index'))
            ->add(trans('plugins/real-estate::export.projects.name'), route('export-projects.index'));
    }

    public function index()
    {
        $this->pageTitle(trans('plugins/real-estate::export.projects.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/export.js']);

        $totalProjects = Project::query()->count();

        return view('plugins/real-estate::export.projects', compact('totalProjects'));
    }

    public function export(ProjectsExport $projectsExport)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        return $projectsExport->download('export_projects.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
