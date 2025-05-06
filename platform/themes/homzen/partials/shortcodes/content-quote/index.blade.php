<div class="my-40 flat-quote">
    <blockquote class="quote">
        “{{ BaseHelper::clean($shortcode->message) }}”
    </blockquote>

    @if($shortcode->author)
        <span class="author">{{ $shortcode->author }}</span>
    @endif
</div>
