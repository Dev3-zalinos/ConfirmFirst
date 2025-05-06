<template id="property-map-content">
    <div class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
                <a href="__url__">
                    <img src="__image__" alt="__name__">
                </a>
                __status__
            </div>
            <div class="content">
                <p class="location">
                    <x-core::icon name="ti ti-map-pin" />
                    __location__
                </p>
                <div class="title">
                    <a href="__url__" title="__name__" class="line-clamp-2">__name__</a>
                </div>
                <div class="price">__price__</div>
                <ul class="list-info">
                    <li><x-core::icon name="ti ti-bed" />__bedroom__</li>
                    <li><x-core::icon name="ti ti-bath" />__bathroom__</li>
                    <li><x-core::icon name="ti ti-ruler" />__square__</li>
                </ul>
            </div>
        </div>
    </div>
</template>
