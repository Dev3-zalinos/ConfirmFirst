@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/real-estate::export.properties.name') }}
            </x-core::card.title>
        </x-core::card.header>

        <x-core::card.body>
            <div class="row justify-content-center text-center py-5">
                <div class="col-md-6">
                    <h3>{{ trans('plugins/real-estate::export.properties.total_properties') }}</h3>
                    <h2 class="fs-1 text-primary fw-bold">{{ $totalProperties }}</h2>
                </div>
            </div>

            <x-core::button
                tag="a"
                data-bb-toggle="export-data"
                class="w-100"
                color="primary"
                :data-loading-text="trans('plugins/real-estate::export.exporting')"
                data-filename="export_properties.csv"
                :href="route('export-properties.index.post')"
            >
                {{ trans('plugins/real-estate::export.start_export') }}
            </x-core::button>
        </x-core::card.body>
    </x-core::card>
@stop
