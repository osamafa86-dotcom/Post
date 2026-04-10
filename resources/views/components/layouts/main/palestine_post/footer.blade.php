@php use App\Enums\CategoryTypeEnum;  use App\Models\Category;use App\Models\SocialMedia;use App\Models\Tag;@endphp
    <!-- footer start -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="about">
                <img class="footer-logo"
                     src="{{ config('system.footer_logo') ? file_url(config('system.footer_logo')) : asset('assets/main/palestine_post/imgs/footer_logo.png')}}"
                     alt="{{ config('system.site_name', 'شعار الموقع') }}" width="150" height="50"/>
                <p>
                    {{ config('system.footer_description')}}
                </p>
                <div class="footer-social-icons">
                    @php $social_media = SocialMedia::with('icon')->whereIn('position',[\App\Enums\LinkPosition::HeaderAndFooter->value,\App\Enums\LinkPosition::FOOTER->value])->orderByDesc('order')->get(); @endphp
                    @foreach($social_media as $social)
                        <a title="{{$social?->name}}" href="{{$social?->url}}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social?->name ?? 'مواقع التواصل' }}">
                            <img width="20" height="20" src="{{asset($social?->icon?->icon_path)}}"
                                 alt="{{ $social?->name ?? 'أيقونة تواصل' }}">
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="categores">
                @php
                    $categories = Category::where('category_type', CategoryTypeEnum::NEWS->value)
                        ->whereHas('post_relation')
                        ->withCount('post_relation')
                        ->take(6)
                        ->get();
                @endphp

                <h4>تصنيفات</h4>
                <ul class="categores-line">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('main.palestine_post.category_news', ['category_title' => $category->category_title]) }}">
                                <span class="category-icon">
                                    <i class="fa-solid fa-angles-left"></i>
                                    {{ $category?->category_title }}
                                </span>
                                <span class="category-count">
                                    ({{ $category?->post_relation_count }})
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>


            <div class="tags">
                @php
                    $tags = Tag::whereHas('post_relation')
                        ->withCount('post_relation')
                        ->take(12)
                        ->get();
                @endphp

                <h4>الوسوم</h4>
                <ul class="tag-line">
                    @foreach($tags as $tag)
                        <li>
                            <a href="{{ route('main.palestine_post.tag_news', ['tag_name' => $tag->tag_name]) }}">
                                {{ $tag?->tag_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>



            @php
                $navbar = \App\Models\FooterLink::whereNull('parent_id')->with('children')->orderBy('link_order')->get();
            @endphp
            @if($navbar->isNotEmpty())
            <div class="categores">
                <h4>روابط مفيدة</h4>
                <ul class="footer-list">
                    @forelse($navbar as $link)
                        <li>
                            <a href="{{ $link->link_url }}" target="{{$link?->link_open == \App\Enums\LinkUrlTargetEnum::NEW_WINDOW->value ? '_blank' : '_self'}}">
                                {{ $link->link_name }}
                            </a>
                        </li>
                    @empty
                    @endforelse
                </ul>

            </div>
            @endif
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container text-center">
            <p>     {{ config('system.copyright_text')
            ? config('system.copyright_text')
            : '© ' . date('Y') . ' منصة فلسطين بوست. جميع الحقوق محفوظة.'
        }}</p>
        </div>
    </div>
</footer>
<!-- footer-end-->

<!-- scroll -->
<div id="scrollToTop" role="button" tabindex="0" aria-label="العودة إلى أعلى الصفحة">
    <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
</div>
