<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as [$loc, $modified])
    <url><loc>{{ $loc }}</loc><lastmod>{{ $modified->toAtomString() }}</lastmod></url>
@endforeach
</urlset>
