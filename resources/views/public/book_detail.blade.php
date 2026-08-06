@extends('public.layout')

@section('meta_title', $book->title.' — JNTU Books')
@section('meta_description', Str::limit(strip_tags(stripslashes($book->description)), 155) ?: 'Read '.$book->title.' free on JNTU Books.')
@section('og_type', 'book')
@section('og_image', book_asset_url($book->image))

@section('structured_data')
@if(isset($book->content_type) && $book->content_type == 'video')
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"VideoObject",
  "name":{!! json_encode(stripslashes($book->title)) !!},
  "description":{!! json_encode(Str::limit(strip_tags(stripslashes($book->description)),300) ?: stripslashes($book->title)) !!},
  "thumbnailUrl":{!! json_encode(youtube_thumb($book->url)) !!},
  "embedUrl":{!! json_encode(youtube_embed($book->url)) !!},
  "contentUrl":{!! json_encode(youtube_watch($book->url)) !!},
  "uploadDate":"{{ date('Y-m-d') }}",
  "publisher":{"@type":"Organization","name":"JNTU Books"}
}
</script>
@else
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Book",
  "name":{!! json_encode(stripslashes($book->title)) !!},
  "image":{!! json_encode(book_asset_url($book->image)) !!},
  "description":{!! json_encode(Str::limit(strip_tags(stripslashes($book->description)),300)) !!},
  "url":{!! json_encode(url()->current()) !!},
  "bookFormat":"https://schema.org/EBook",
  "inLanguage":"en",
  "publisher":{"@type":"Organization","name":"JNTU Books"}
}
</script>
@endif
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"BreadcrumbList",
  "itemListElement":[
    {"@type":"ListItem","position":1,"name":"Home","item":"{{ url('/') }}"},
    {"@type":"ListItem","position":2,"name":"Books","item":"{{ route('public.books') }}"},
    {"@type":"ListItem","position":3,"name":{!! json_encode(stripslashes($book->title)) !!},"item":"{{ url()->current() }}"}
  ]
}
</script>
@endsection

@section('head')
<style>
  .detail{padding:44px 0}
  .detail .wrap{display:grid;grid-template-columns:300px 1fr;gap:40px;align-items:start}
  .cover-lg{border-radius:18px;overflow:hidden;box-shadow:0 24px 50px rgba(20,16,60,.2);border:4px solid #fff;position:sticky;top:90px;aspect-ratio:3/4;background:#eceafd;position:relative}
  .cover-lg img{width:100%;height:100%;object-fit:cover}
  .cover-lg .pub-auto-cover{font-size:20px}
  .detail h1{font-size:32px;line-height:1.1}
  .crumb{color:var(--muted);font-size:14px;margin-bottom:10px}
  .crumb a{color:var(--violet)}
  .tags{display:flex;gap:8px;flex-wrap:wrap;margin:16px 0}
  .desc{margin:22px 0;color:#3a3d63;white-space:pre-line}
  .gate{background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;margin:24px 0}
  .gate h3{font-size:18px;margin-bottom:6px}
  .gate p{color:var(--muted);font-size:14px;margin-bottom:14px}
  .related h2{font-size:22px;margin:40px 0 18px}
  @media(max-width:760px){.detail .wrap{grid-template-columns:1fr}.cover-lg{position:static;max-width:240px}}
</style>
@endsection

@section('content')
<section class="detail">
  <div class="wrap">
    <div class="cover-lg">@include('public.partials.book_cover', ['book' => $book])</div>

    <div>
      <div class="crumb"><a href="{{ route('public.books') }}">Books</a> / {{ \App\Category::getCategoryInfo($book->cat_id,'category_name') ?? 'eBook' }}</div>
      <h1>{{ stripslashes($book->title) }}</h1>

      <div class="tags">
        <span class="pill">{{ \App\Category::getCategoryInfo($book->cat_id,'category_name') ?? 'eBook' }}</span>
        @if($book->featured)<span class="pill" style="background:var(--gold);color:#3a2a00;border:none">Featured</span>@endif
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin:14px 0">
        <button onclick="shareThis('{{ addslashes(stripslashes($book->title)) }}')" class="btn btn-ghost"><i class="fa fa-share-nodes"></i> Share</button>
        <button onclick="openInApp()" class="btn btn-ghost"><i class="fa fa-mobile-screen"></i> Open in app</button>
      </div>

      @if($book->description && trim(strip_tags(stripslashes($book->description))) != '')
        <div class="desc">{{ trim(strip_tags(stripslashes($book->description))) }}</div>
      @endif

      {{-- Video: free to watch. Book: browse free, reading needs login. --}}
      @if(isset($book->content_type) && $book->content_type == 'video')
        @php $vid = youtube_id($book->url); @endphp
        @if($vid)
          <div style="margin:18px 0;border-radius:16px;overflow:hidden;box-shadow:0 18px 40px rgba(20,16,60,.18)">
            <div style="position:relative;padding-bottom:56.25%;height:0;background:#000">
              <iframe src="{{ youtube_embed($vid) }}" title="{{ stripslashes($book->title) }}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
        @else
          <div class="gate"><h3>Video unavailable</h3><p>This video link couldn't be loaded.</p></div>
        @endif
      @else
        @auth
          <div class="gate">
            <h3>Ready to read</h3>
            <p>You're signed in. Open the reader to start.</p>
            <a href="{{ route('public.read', $book->id.'-'.Str::slug($book->title)) }}" class="btn btn-primary"><i class="fa fa-book-open"></i> Read now</a>
          </div>
        @else
          <div class="gate">
            <h3>Sign in to read this book</h3>
            <p>Browsing is free. Create a free account or sign in to open the reader.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Create free account</a>
            <a href="{{ route('login') }}" class="btn btn-ghost">Sign in</a>
          </div>
        @endauth
      @endif
    </div>
  </div>

  @if(count($related))
  <div class="wrap related">
    <h2>Related books</h2>
    <div class="grid-books">
      @foreach($related as $b)
        <a href="{{ route('public.book', $b->id.'-'.Str::slug($b->title)) }}" class="book-card">
          <div class="cover">@include('public.partials.book_cover', ['book' => $b])</div>
          <div class="meta"><div class="bt">{{ $b->title }}</div></div>
        </a>
      @endforeach
    </div>
  </div>
  @endif
</section>
@endsection