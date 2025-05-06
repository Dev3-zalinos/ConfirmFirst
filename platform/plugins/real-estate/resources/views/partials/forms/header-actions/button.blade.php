<x-core::button :class="$className ?? ''" :id="$id ?? Str::uuid()">
    {!! BaseHelper::clean($label) !!}
</x-core::button>
