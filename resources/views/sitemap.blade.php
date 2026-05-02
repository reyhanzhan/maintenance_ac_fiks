<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    @php
        $loc = $url["loc"];
        $changefreq = $url["changefreq"];
        $priority = $url["priority"];
    @endphp
    <url>
        <loc>{{ $loc }}</loc>
        <changefreq>{{ $changefreq }}</changefreq>
        <priority>{{ $priority }}</priority>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
