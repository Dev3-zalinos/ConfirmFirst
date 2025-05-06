@if (RealEstateHelper::isEnabledReview())
    @php
        Theme::asset()->add('star-rating', 'vendor/core/plugins/real-estate/libraries/star-rating/star-rating.min.css');
        Theme::asset()->container('footer')->add('star-rating', 'vendor/core/plugins/real-estate/libraries/star-rating/star-rating.min.js');
        Theme::asset()->container('footer')->add('front-review', 'vendor/core/plugins/real-estate/js/front-review.js', ['jquery', 'star-rating'], version: '1.0.0');
    @endphp

    <div @class(['single-wrapper-review',  $class ?? null])>
        <div class="wrap-form-comment">
            <div class="h7">{{ __('Write A Review') }}</div>
            <div class="respond-comment">
                {!!
                    \Botble\RealEstate\Forms\Fronts\ReviewForm::create(data: ['model' => $model])
                        ->setUrl(route('public.ajax.review.store', $model->slug))
                        ->setFormInputWrapperClass('form-wg')
                        ->modify('content', \Botble\Base\Forms\Fields\TextareaField::class, ['attr' => ['class' => '']])
                        ->modify('submit', 'submit', ['attr' => ['class' => 'form-wg tf-btn primary']])
                        ->add('reviewable_type', 'hidden', ['attr' => ['value' => $model::class]])
                        ->renderForm()
                !!}
            </div>
        </div>
        <div class="mt-5 box-title-review d-flex justify-content-between align-items-center flex-wrap gap-20">
            <div class="h7 fw-7">
                @if($model->reviews_count === 1)
                    {{ __('1 Review') }}
                @else
                    {{ __(':number Reviews', ['number' => $model->reviews_count]) }}
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                @include(Theme::getThemeNamespace('views.real-estate.partials.star'), ['star' => $model->reviews_avg_star, 'class' => null])
                <span class="small text-muted fw-medium">{{ __(':avg out of 5', ['avg' => round($model->reviews_avg_star, 1) ?: 0]) }}</span>
            </div>
        </div>
        <div class="wrap-review">
            <div
                class="reviews-list position-relative"
                data-url="{{ route('public.ajax.review.index', $model->slug) }}?reviewable_type={{ $model::class }}" data-type="{{ $model::class }}"
            ></div>
        </div>
    </div>
@endif
