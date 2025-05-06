@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="alert alert-warning">
        {{ trans('plugins/real-estate::account.pending_approval.description') }}
    </div>
@stop
