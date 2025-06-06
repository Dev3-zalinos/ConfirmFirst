<x-core::card class="mb-3">
    <x-core::card.header>
        <x-core::card.title>
            {{ trans('plugins/real-estate::import.template') }}
        </x-core::card.title>
    </x-core::card.header>
    <div class="table-responsive">
        <x-core::table class="card-table">
            <x-core::table.header>
                @foreach ($headings as $heading)
                    <x-core::table.header.cell>
                        {{ $heading }}
                    </x-core::table.header.cell>
                @endforeach
            </x-core::table.header>
            <x-core::table.body>
                @foreach ($data as $item)
                    <x-core::table.body.row>
                        @foreach ($headings as $key => $value)
                            <x-core::table.body.cell>
                                @php
                                    $value = Arr::get($item, $key);

                                    if (is_array($value)) {
                                        $value = implode(', ', $value);
                                    }
                                @endphp
                                {{ $value }}
                            </x-core::table.body.cell>
                        @endforeach
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>
</x-core::card>
