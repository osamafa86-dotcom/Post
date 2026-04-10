<?=
'<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title><![CDATA[{{ config('system.site_name') }}]]></title>
        <link>{{ url('/') }}</link>
        <atom:link href="{{ route('main.rss_feed') }}" rel="self" type="application/rss+xml" />
        <description><![CDATA[{{ config('system.light_site_name') }}]]></description>
        <language>ar</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <pubDate>{{ now()->toRssString() }}</pubDate>
        @foreach($posts as $post)
            <item>
                <title><![CDATA[{{ $post->title }}]]></title>
                <link>{{ route('main.'.config('app.launch').'.show_post', ['id' => $post->id, 'slug' => $post->slug]) }}</link>
                <description><![CDATA[{!! html_entity_decode(strip_tags($post->body)) !!}]]></description>
                @if($post?->category?->relationable?->category_title)
                <category><![CDATA[{{ $post->category->relationable->category_title }}]]></category>
                @endif
                @if($post?->author?->relationable?->name)
                <dc:creator><![CDATA[{{ $post->author->relationable->name }}]]></dc:creator>
                @endif
                <guid isPermaLink="false">{{ url('/post/' . $post->id) }}</guid>
                @if($post?->files?->first()?->file?->path)
                <enclosure url="{{ file_url($post->files->first()->file->path) }}" type="image/jpeg" length="0"></enclosure>
                @endif
                <pubDate>{{ $post->publish_date ? \Carbon\Carbon::parse($post->publish_date)->toRssString() : $post->created_at->toRssString() }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>
