@if ($consult)
    <x-core::datagrid>
        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/real-estate::consult.time') }}
            </x-slot:title>

            {{ $consult->created_at }}
        </x-core::datagrid.item>

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/real-estate::consult.consult_id') }}
            </x-slot:title>

            AB00000{{ $consult->id }}
        </x-core::datagrid.item>

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/real-estate::consult.form_name') }}
            </x-slot:title>

            {{ $consult->name }}
        </x-core::datagrid.item>

        @if ($consult->ip_address && auth()->check())
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/real-estate::consult.ip_address') }}
                </x-slot:title>

                <a href="https://ipinfo.io/{{ $consult->ip_address }}" target="_blank">
                    {{ $consult->ip_address }}
                </a>
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/real-estate::consult.email.header') }}
            </x-slot:title>

            <a href="mailto:{{ $consult->email }}">
                {{ $consult->email }}
            </a>
        </x-core::datagrid.item>

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/real-estate::consult.phone') }}
            </x-slot:title>

            @if ($consult->phone)
                <a href="tel:{{ $consult->phone }}">
                    {{ $consult->phone }}
                </a>
            @else
                N/A
            @endif
        </x-core::datagrid.item>

        @if ($consult->project_id && $consult->project)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/real-estate::consult.project') }}
                </x-slot:title>

                <a href="{{ $consult->project->url }}" target="_blank">
                    {{ $consult->project->name }}
                </a>
            </x-core::datagrid.item>
        @endif

        @if ($consult->property_id && $consult->property)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/real-estate::consult.property') }}
                </x-slot:title>

                <a href="{{ $consult->property->url }}" target="_blank">
                    {{ $consult->property->name }}
                </a>
            </x-core::datagrid.item>
        @endif

        @if($consult->custom_fields)
            @foreach($consult->custom_fields as $key => $value)
                @continue(blank($value))
                <x-core::datagrid.item>
                    <x-slot:title>{{ $key }}</x-slot:title>
                    {{ $value }}
                </x-core::datagrid.item>
            @endforeach
        @endif
    </x-core::datagrid>

    <x-core::datagrid.item class="mt-4">
        <x-slot:title>
            {{ trans('plugins/real-estate::consult.content') }}
        </x-slot:title>

        {{ $consult->content ?: 'N/A' }}
    </x-core::datagrid.item>
@endif
