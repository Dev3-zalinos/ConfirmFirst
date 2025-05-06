@php
    $cols = max((int)(substr($shortcode->number_of_columns, -1)), 1);
    $cols = min($cols, 6);
    $rows = array_chunk($tabs, $cols);
@endphp

@foreach ($rows as $row)
    @if ($row[0]['image'])
        <div class="my-40 box-image grid-{{ $cols }} gap-30">
            @foreach ($row as $item)
                @if ($image = $item['image'])
                    <div class="overflow-hidden">
                        {{ RvMedia::image($image, $item['caption'] ?: __('image'), attributes: ['class' => 'round-12']) }}
                        @if ($caption = $item['caption'])
                            <figcaption class="figure-caption text-center">{!! BaseHelper::clean($caption) !!}</figcaption>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endforeach
