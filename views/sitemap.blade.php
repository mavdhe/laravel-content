<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $page)            
    <url>
        <loc>{{ URL::base() }}/{{ $page->properties['path'] }}</loc>
        @if (array_key_exists('priority', $page->properties))
        <priority>{{ $page->properties['priority'] }}</priority>
        @endif
        @if (array_key_exists('lastmod', $page->properties))
        <lastmod>{{ $page->properties['lastmod'] }}</lastmod>
        @endif
        @if (array_key_exists('changefreq', $page->properties))
        <changefreq>{{ $page->properties['changefreq'] }}</changefreq>
        @endif
    </url>
@endforeach
</urlset>