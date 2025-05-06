<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
    @foreach ($features->sortBy('name') as $feature)
        <div class="col">
            <x-core::form.checkbox
                :label="$feature->name"
                name="features[]"
                :value="$feature->id"
                :checked="in_array($feature->id, $selectedFeatures)"
            />
        </div>
    @endforeach
</div>
