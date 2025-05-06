@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="bulk-import">
        <x-core::form
            class="form-import-data mb-3"
            :data-upload-url="route('properties.upload.process')"
            :data-validate-url="route('properties.upload.validate')"
            :data-import-url="route('properties.import')"
            method="post"
        >
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/real-estate::property.import_properties') }}
                    </x-core::card.title>
                </x-core::card.header>
                <x-core::card.body>
                    <x-core::form-group>
                        <x-core::form.label
                            for="input-group-file">{{ trans('plugins/real-estate::import.choose_file') }}</x-core::form.label>
                        <div
                            class="import-dropzone dropzone"
                            data-mimetypes="{{ $mimetypes }}"
                        >
                            <div class="dz-message">
                                {{ trans('plugins/real-estate::import.choose_file_description', ['types' => implode(', ', config('plugins.real-estate.general.bulk-import.mimes', []))]) }}<br>
                            </div>
                        </div>

                        <x-core::form.helper-text class="mt-1">
                            {{ trans('plugins/real-estate::import.choose_file_description', ['types' => implode(', ', config('plugins.real-estate.general.bulk-import.mimes', []))]) }}
                        </x-core::form.helper-text>
                    </x-core::form-group>

                    <div class="mb-3 text-center p-2 border bg-body text-body">
                        <a
                            href="javascript:void(0)"
                            class="download-template"
                            data-url="{{ route('properties.download-template') }}"
                            data-extension="csv"
                            data-filename="template_properties_import.csv"
                            data-downloading="<i class='fas fa-spinner fa-spin'></i> {{ trans('plugins/real-estate::import.downloading') }}"
                        >
                            <x-core::icon name="ti ti-file-type-csv" />
                            {{ trans('plugins/real-estate::import.download_csv_file') }}
                        </a>
                        &nbsp; | &nbsp;
                        <a
                            href="javascript:void(0)"
                            class="download-template"
                            data-url="{{ route('properties.download-template') }}"
                            data-extension="xlsx"
                            data-filename="template_properties_import.xlsx"
                            data-downloading="<i class='fas fa-spinner fa-spin'></i> {{ trans('plugins/real-estate::import.downloading') }}"
                        >
                            <x-core::icon name="ti ti-file-spreadsheet" />
                            {{ trans('plugins/real-estate::import.download_excel_file') }}
                        </a>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="update_existing" id="update-existing" value="1">
                        <label class="form-check-label" for="update-existing">
                            {{ trans('plugins/real-estate::import.update_existing_properties') }}
                        </label>
                        <x-core::form.helper-text>
                            {{ trans('plugins/real-estate::import.update_existing_properties_description') }}
                        </x-core::form.helper-text>
                    </div>

                    <div class="d-grid mb-3">
                        <x-core::button
                            class="btn-import"
                            type="submit"
                            color="primary"
                            data-uploading-text="{{ __('plugins/real-estate::import.uploading') }}"
                            data-validating-text="{{ __('plugins/real-estate::import.validating') }}"
                            data-importing-text="{{ __('plugins/real-estate::import.importing') }}"
                        >
                            {{ trans('plugins/real-estate::property.import_properties') }}
                        </x-core::button>
                    </div>

                    <x-core::alert
                        type="info"
                        class="bulk-import-message"
                        style="display: none"
                    ></x-core::alert>

                    <div class="processing mt-3" style="height: 10px; background-color: #e3e0e0  ; position: relative; border-radius: 24px; overflow: hidden; display: none">
                        <div class="process" style="position: absolute; width: 0; inset: 0; background-color: var(--bb-primary)"></div>
                    </div>
                </x-core::card.body>
            </x-core::card>

            <div class="main-form-message mt-3" style="display: none">
                <div id="imported-message"></div>
                <x-core::card class="bg-danger-lt show-errors overflow-auto" id="failures-list" style="max-height: 100vh; min-height: 10rem; display: none">
                    <x-core::card.header>
                        <x-core::card.title class="text-warning">
                            {{ trans('plugins/real-estate::import.failures') }}
                        </x-core::card.title>
                    </x-core::card.header>
                    <x-core::table :hover="false">
                        <x-core::table.header>
                            <x-core::table.header.cell>
                                #{{ trans('plugins/real-estate::import.row') }}
                            </x-core::table.header.cell>
                            <x-core::table.header.cell>
                                {{ trans('plugins/real-estate::import.errors') }}
                            </x-core::table.header.cell>
                        </x-core::table.header>
                        <x-core::table.body id="imported-listing"></x-core::table.body>
                    </x-core::table>
                </x-core::card>
            </div>
        </x-core::form>
    </div>

    @include('plugins/real-estate::import.partials.template', ['headings' => $headings, 'data' => $properties])

    @include('plugins/real-estate::import.partials.rules', compact('rules', 'headings'))
@endsection

@push('footer')
    <x-core::custom-template id="preview-template">
        <div class="position-relative d-flex gap-3">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="height: 2rem; width: 2rem">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                </svg>
            </div>
            <div>
                <h4><span data-dz-name></span></h4>
                <div class="small text-muted">
                    <span data-dz-size></span>
                    <a href="#" class="ms-1 text-danger cursor-pointer" data-dz-remove>
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <div class="text-danger small" data-dz-errormessage></div>
            </div>
        </div>
    </x-core::custom-template>

    @include('plugins/real-estate::import.partials.failure-template')
@endpush
