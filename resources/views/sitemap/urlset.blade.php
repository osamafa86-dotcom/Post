<?=
'<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        @isset($url['lastmod'])<lastmod>{{ $url['lastmod'] }}</lastmod>@endisset
        @isset($url['changefreq'])<changefreq>{{ $url['changefreq'] }}</changefreq>@endisset
        @isset($url['priority'])<priority>{{ $url['priority'] }}</priority>@endisset
        @isset($url['image'])
        <image:image>
            <image:loc>{{ $url['image']['loc'] }}</image:loc>
            @isset($url['image']['title'])<image:title>{{ $url['image']['title'] }}</image:title>@endisset
        </image:image>
        @endisset
    </url>
@endforeach
</urlset>
