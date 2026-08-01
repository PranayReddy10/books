<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>{{ route('public.home') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
  <url><loc>{{ route('public.books') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
  <url><loc>{{ route('public.feed') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
@foreach($books as $b)
  <url><loc>{{ route('public.book', $b->id.'-'.Str::slug($b->title)) }}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
@endforeach
@foreach($posts as $p)
  <url><loc>{{ route('public.post', $p->id) }}</loc><changefreq>weekly</changefreq><priority>0.6</priority></url>
@endforeach
</urlset>
