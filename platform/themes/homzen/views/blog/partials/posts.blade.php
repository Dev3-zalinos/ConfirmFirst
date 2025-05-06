@if($posts->isNotEmpty())
    <div class="row">
        @foreach($posts as $post)
            <div class="box col-lg-4 col-md-6">
                <div class="flat-blog-item hover-img wow fadeIn" data-wow-delay=".2s" data-wow-duration="2000ms">
                    <a class="img-style" href="{{ $post->url }}">
                        {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                        <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                    </a>
                    <div class="content-box">
                        <div class="post-author">
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <span class="fw-6">{{ $post->author->name }}</span>
                            @endif

                            @if($category = $post->firstCategory)
                                <span>
                                    <a href="{{ $category->url }}">{{ $category->name }}</a>
                                </span>
                            @endif
                        </div>
                        <h6 class="title">
                            <a href="{{ $post->url }}" class="w-100 text-truncate">{!! BaseHelper::clean($post->name) !!}</a>
                        </h6>
                        @if($post->description)
                            <p class="description">{!! BaseHelper::clean(Str::limit($post->description)) !!}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
