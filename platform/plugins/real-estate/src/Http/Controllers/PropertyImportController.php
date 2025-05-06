<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\RealEstate\Exports\PropertyTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class PropertyImportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::property.name'), route('property.index'))
            ->add(trans('plugins/real-estate::property.import_properties'), route('properties.import.index'));
    }

    public function index(PropertyTemplateExport $export)
    {
        $this->pageTitle(trans('plugins/real-estate::property.import_properties'));

        $mimetypes = collect(config('plugins.real-estate.general.bulk-import.mime_types', []))->implode(',');

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/bulk-import.js')
            ->addScripts(['dropzone'])
            ->addStyles(['dropzone']);

        $properties = $export->collection();
        $headings = $export->headings();
        $rules = $export->rules();

        return view('plugins/real-estate::import.property', compact('properties', 'headings', 'rules', 'mimetypes'));
    }

    public function downloadTemplate(Request $request, PropertyTemplateExport $export)
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
