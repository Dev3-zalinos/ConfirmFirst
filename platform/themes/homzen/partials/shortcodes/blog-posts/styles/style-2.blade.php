<section class="flat-section flat-latest-new-v2">
    <div class="container">
        {!! Theme::partial('shortcode-heading', ['shortcode' => $shortcode, 'centered' => false]) !!}

        <div class="row wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
            @foreach($posts as $post)
                <div class="box col-lg-3 col-sm-6">
                    <div class="flat-blog-item hover-img not-overlay style-1">
                        <div class="img-style">
                            {{ RvMedia::image($post->image, $post->name, 'medium-rectangle-column') }}
                        </div>
                        <div class="content-box">
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                            <div class="title h7 fw-7 link">
                                <a href="{{ $post->url }}" class="line-clamp-1">{{ $post->name }}</a>
                            </div>
                            <div class="post-author">
                                @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                    <span class="fw-5">{{ $post->author->name }}</span>
                                @endif

                                @if ($category = $post->firstCategory)
                                    <span>
                                        <a href="{{ $category->url }}">{{ $category->name }}</a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
