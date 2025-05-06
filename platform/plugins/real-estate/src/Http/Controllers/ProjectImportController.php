<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\RealEstate\Exports\ProjectTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ProjectImportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::project.name'), route('project.index'))
            ->add(trans('plugins/real-estate::project.import_projects'), route('projects.import.index'));
    }

    public function index(ProjectTemplateExport $export)
    {
        $this->pageTitle(trans('plugins/real-estate::project.import_projects'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/bulk-import.js')
            ->addScripts(['dropzone'])
            ->addStyles(['dropzone']);

        $mimetypes = collect(config('plugins.real-estate.general.bulk-import.mime_types', []))->implode(',');
        $projects = $export->collection();
        $headings = $export->headings();
        $rules = $export->rules();

        return view('plugins/real-estate::import.project', compact('projects', 'headings', 'rules', 'mimetypes'));
    }

    public function downloadTemplate(Request $request, ProjectTemplateExport $export)
    {
        $request->validate([
            'extension' => 'required|in:csv,xlsx',
        ]);

        $extension = Excel::XLSX;
        $contentType = 'text/xlsx';

        if ($request->input('extension') === 'csv') {
            $extension = Excel::CSV;
            $contentType = 'text/csv';
        }

        $fileName = 'template_properties_import.' . $extension;

        return $export->download($fileName, $extension, ['Content-Type' => $contentType]);
    }
}
