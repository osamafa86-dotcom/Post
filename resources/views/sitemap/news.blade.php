<?=
'<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
@foreach($posts as $post)
    <url>
        <loc>{{ route('main.'.config('app.launch').'.show_post', ['id' => $post->id, 'slug' => $post->slug]) }}</loc>
        <news:news>
            <news:publication>
                <news:name>{{ config('system.site_name') }}</news:name>
                <news:language>{{ $post->lang ?? 'ar' }}</news:language>
            </news:publication>
            <news:publication_date>{{ $post->publish_date ? \Illuminate\Support\Carbon::parse($post->publish_date)->toAtomString() : $post->created_at->toAtomString() }}</news:publication_date>
            <news:title>{{ $post->title }}</news:title>
        </news:news>
    </url>
@endforeach
</urlset>
