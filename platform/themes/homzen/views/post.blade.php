@php
    Theme::set('breadcrumbEnabled', 'no');
    Theme::set('currentPostId', $post->getKey());
    $bottomPostDetailSidebar = dynamic_sidebar('bottom_post_detail_sidebar');
    Theme::layout('full-width');
    Theme::set('pageTitle', $post->name);
@endphp

<section class="flat-banner-blog">
    {{ RvMedia::image($post->image, $post->name, lazy: false) }}
</section>

<section @class(['flat-section-v2', 'flat-section' => ! $bottomPostDetailSidebar])>
    <div class="container">
        {!! apply_filters('ads_render', null, 'post_detail_before') !!}

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="flat-blog-detail">
                    @if($post->firstCategory)
                        <a href="{{ $post->firstCategory->url }}" class="blog-tag primary">{{ $post->firstCategory->name }}</a>
                    @endif
                    <h3 class="text-capitalize">{!! BaseHelper::clean($post->name) !!}</h3>
                    <div class="mt-12 d-flex align-items-center gap-16 mb-3">
                        @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                            <div class="avatar avt-40 round">
                                {{ RvMedia::image($author->avatar_url, $author->name) }}
                            </div>
                        @endif
                        <div class="post-author style-1">
                            @if (theme_option('blog_show_author_name', 'yes') == 'yes' && class_exists($post->author_type) && ($author = $post->author ?? null) && trim($author->name))
                                <span>{{ $post->author->name }}</span>
                            @endif
                            <span>{{ Theme::formatDate($post->created_at) }}</span>
                        </div>
                    </div>

                    <div class="ck-content single-detail">
                        {!! BaseHelper::clean($post->content) !!}
                    </div>

                    <div class="my-40 d-flex justify-content-between flex-wrap gap-16">
                        @if($post->tags->isNotEmpty())
                            <div class="d-flex flex-wrap align-items-center gap-12">
                                <span class="text-black">{{ __('Tag:') }}</span>
                                <ul class="d-flex flex-wrap gap-12">
                                    @foreach($post->tags as $tag)
                                        <li>
                                            <a href="{{ $tag->url }}" class="blog-tag">{{ $tag->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $shareSocials = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons($post->url, $post->name);
                        @endphp
                        @if($shareSocials)
                            <div class="d-flex flex-wrap align-items-center gap-16">
                                <span class="text-black">{{ __('Share:') }}</span>
                                <ul class="d-flex flex-wrap gap-12">
                                    @foreach($shareSocials as $social)
                                        <li>
                                            <a href="{{ $social['url'] }}" class="box-icon w-40 social square" title="{{ $social['name'] }}">
                                                {!! $social['icon'] !!}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    @php
                        $relatedPosts = get_related_posts($post->id, 2);
                    @endphp

                    @if($relatedPosts->isNotEmpty())
                        <div class="post-navigation">
                            @foreach($relatedPosts as $post)
                                <div @class(['previous-post' => $loop->first, 'next-post' => ! $loop->first])>
                                    <div class="subtitle">{{ $loop->first ? __('Previous') : __('Next') }}</div>
                                    <div class="h7 fw-7 text-black text-capitalize">
                                        <a href="{{ $post->url }}">{!! BaseHelper::clean($post->name) !!}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}
                </div>
            </div>
        </div>

        {!! apply_filters('ads_render', null, 'post_detail_after') !!}
    </div>
</section>

@if($bottomPostDetailSidebar)
    {!! $bottomPostDetailSidebar !!}
@endif
