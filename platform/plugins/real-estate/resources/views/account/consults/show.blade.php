@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                {{ trans('plugins/real-estate::consult.consult_information') }}
            </x-core::card.title>
        </x-core::card.header>
        <x-core::card.body>
            @include('plugins/real-estate::info')
        </x-core::card.body>
    </x-core::card>
@stop
