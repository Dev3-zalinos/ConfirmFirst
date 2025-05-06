<div class="@if ($errors->has($name)) has-error @endif">
    <div class="multi-choices-widget list-item-checkbox ms-n3" data-bb-toggle="tree-checkboxes">
        @if (isset($options['choices']) &&
                (is_array($options['choices']) || $options['choices'] instanceof \Illuminate\Support\Collection))
            @include('plugins/real-estate::categories.categories-checkbox-option-line', [
                'categories' => $options['choices'],
                'value' => $options['value'],
                'currentId' => null,
                'name' => $name,
            ])
        @endif
    </div>
</div>
