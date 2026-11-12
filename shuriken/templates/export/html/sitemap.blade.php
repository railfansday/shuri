{!! '<' . '?' . 'xml version="1.0" encoding="UTF-8"' . '?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ config('export.base_url', 'https://domain.com') }}/index.html</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toISOString() }}</lastmod>
    </url>
    @foreach($pages as $page)
        <url>
            <loc>{{ config('export.base_url', 'https://domain.com') }}/{{ $page }}.html</loc>
            <lastmod>{{ \Carbon\Carbon::now()->toISOString() }}</lastmod>
        </url>
    @endforeach
    @foreach($posts as $post)
        <url>
            <loc>{{ config('export.base_url', 'https://domain.com') }}/{{ $post->slug }}.html</loc>
            <lastmod>{{ $post->published_at->toISOString() }}</lastmod>
        </url>
    @endforeach
</urlset>
