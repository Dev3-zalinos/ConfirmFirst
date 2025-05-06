<section class="flat-section">
    {!! apply_filters('ads_render', null, 'blog_list_before') !!}

    <div class="row">
        <div class="col-lg-8">
            <div class="flat-blog-list">
                @foreach($posts as $post)
                    <div class="flat-blog-item">
                        <a class="img-style" href="{{ $post->url }}">
                            {{ RvMedia::image($post->image, $post->name) }}
                            <span class="date-post">{{ Theme::formatDate($post->created_at) }}</span>
                        </a>
                        <div class="content-box">
                            <div class="post-author">
                                @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                    <span class="text-black fw-7">{{ $author->name }}</span>
                                @endif

                                @if($category = $post->firstCategory)
                                    <span>
                                        <a href="{{ $category->url }}">{{ $category->name }}</a>
                                    </span>
                                @endif
                            </div>
                            <h5 class="title">
                                <a href="{{ $post->url }}">
                                    {!! BaseHelper::clean($post->name) !!}
                                </a>
                            </h5>
                            @if($post->description)
                                <p class="description body-1">{!! BaseHelper::clean(Str::limit($post->description)) !!}</p>
                            @endif
                            <a href="{{ $post->url }}" class="btn-read-more">{{ __('Read More') }}</a>
                        </div>
                    </div>
                @endforeach

                {{ $posts->links(Theme::getThemeNamespace('partials.pagination')) }}
            </div>
        </div>
        <div class="col-lg-4">
            <aside class="sidebar-blog">
                {!! dynamic_sidebar('blog_sidebar') !!}
            </aside>
        </div>
    </div>

    {!! apply_filters('ads_render', null, 'blog_list_after') !!}
</section>
