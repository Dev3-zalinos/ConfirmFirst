<div id="custom-field-options">
    <x-core::table class="card-table">
        <x-core::table.header>
            <x-core::table.header.cell>
                #
            </x-core::table.header.cell>
            <x-core::table.header.cell>
                {{ trans('plugins/real-estate::custom-fields.option.label') }}
            </x-core::table.header.cell>
            <x-core::table.header.cell colspan="2">
                {{ trans('plugins/real-estate::custom-fields.option.value') }}
            </x-core::table.header.cell>
        </x-core::table.header>
        <x-core::table.body class="option-sortable">
            <input name="is_global" type="hidden" value="1">

            @if ($options->isNotEmpty())
                @foreach ($options as $key => $value)
                    <x-core::table.body.row
                        class="option-row ui-state-default"
                        data-index="{{ $value->id }}"
                    >
                        <input
                            name="options[{{ $key }}][id]"
                            type="hidden"
                            value="{{ $value->id }}"
                        >
                        <input
                            name="options[{{ $key }}][order]"
                            type="hidden"
                            value="{{ $value->order !== 999 ? $value->order : $key }}"
                        >
                        <td class="text-center">
                            <x-core::icon name="ti ti-arrows-sort" />
                        </td>
                        <td>
                            <input
                                class="form-control option-label"
                                name="options[{{ $key }}][label]"
                                type="text"
                                value="{{ $value->label }}"
                                placeholder="{{ trans('plugins/real-estate::custom-fields.option.label') }}"
                            />
                        </td>
                        <td>
                            <input
                                class="form-control option-value"
                                name="options[{{ $key }}][value]"
                                type="text"
                                value="{{ $value->value }}"
                                placeholder="{{ trans('plugins/real-estate::custom-fields.option.value') }}"
                            />
                        </td>
                        <td style="width: 50px">
                            <x-core::button
                                class="remove-row"
                                data-index="0"
                                icon="ti ti-trash"
                                :icon-only="true"
                            />
                        </td>
                    </x-core::table.body.row>
                @endforeach
            @else
                <tr class="option-row" data-index="0">
                    <td class="text-center">
                        <x-core::icon name="ti ti-arrows-sort" />
                    </td>
                    <td>
                        <input
                            class="form-control option-label"
                            name="options[0][label]"
                            type="text"
                            placeholder="{{ trans('plugins/real-estate::custom-fields.option.label') }}"
                        />
                    </td>
                    <td>
                        <input
                            class="form-control option-value"
                            name="options[0][value]"
                            type="text"
                            placeholder="{{ trans('plugins/real-estate::custom-fields.option.value') }}"
                        />
                    </td>
                    <td style="width: 50px">
                        <x-core::button
                            class="remove-row"
                            data-index="0"
                            icon="ti ti-trash"
                            :icon-only="true"
                        />
                    </td>
                </tr>
            @endif
        </x-core::table.body>
    </x-core::table>
</div>
