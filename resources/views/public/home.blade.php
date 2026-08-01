@extends('public.layout')

@section('meta_title', 'JNTU Books — Free Engineering eBooks, Notes, Photos & Videos')
@section('meta_description', 'Read and download JNTU engineering eBooks and notes for free. Explore a student feed of photos and videos. Browse free, sign in to read.')

@section('head')
<style>
  .hero{position:relative;overflow:hidden;padding:70px 0 40px}
  .hero:before{content:'';position:absolute;inset:0;background:
     radial-gradient(60% 60% at 15% 10%, rgba(139,108,255,.20), transparent 60%),
     radial-gradient(50% 50% at 90% 20%, rgba(255,194,75,.18), transparent 60%);pointer-events:none}
  .hero .wrap{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;position:relative}
  .eyebrow{font-weight:600;color:var(--violet);letter-spacing:.08em;text-transform:uppercase;font-size:13px;margin-bottom:14px}
  .hero h1{font-size:clamp(34px,5vw,56px);line-height:1.04;font-weight:800}
  .hero h1 .hl{background:linear-gradient(120deg,var(--violet),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
  .hero p.sub{font-size:18px;color:var(--muted);margin:18px 0 26px;max-width:520px}
  .hero .cta-row{display:flex;gap:12px;flex-wrap:wrap}
  .stat-row{display:flex;gap:26px;margin-top:30px}
  .stat b{font-family:'Sora';font-size:24px;display:block}
  .stat span{color:var(--muted);font-size:13px}

  /* 3D floating book stage */
  .stage{perspective:1200px;height:380px;position:relative}
  .float{position:absolute;border-radius:14px;overflow:hidden;box-shadow:0 30px 60px rgba(20,16,60,.28);animation:bob 6s ease-in-out infinite;border:3px solid #fff;background:#eceafd}
  .float img{width:100%;height:100%;object-fit:cover}
  .float .pub-auto-cover{font-size:14px}
  .f1{width:190px;height:250px;left:50%;top:40px;transform:translateX(-50%) rotateY(-16deg) rotateX(6deg);z-index:3}
  .f2{width:150px;height:200px;left:12%;top:90px;transform:rotateY(18deg) rotateX(6deg);z-index:2;animation-delay:.6s}
  .f3{width:150px;height:200px;right:10%;top:110px;transform:rotateY(-20deg) rotateX(6deg);z-index:2;animation-delay:1.1s}
  @keyframes bob{0%,100%{margin-top:0}50%{margin-top:-16px}}

  .section{padding:44px 0}
  .section h2{font-size:26px;margin-bottom:4px}
  .section .head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px}
  .section .head a{color:var(--violet);font-weight:600;font-size:14px}

  /* feed teaser */
  .feed-strip{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px}
  .feed-tile{position:relative;aspect-ratio:1;border-radius:14px;overflow:hidden;background:#111}
  .feed-tile img{width:100%;height:100%;object-fit:cover}
  .feed-tile .play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:26px;text-shadow:0 2px 10px rgba(0,0,0,.5)}
  .feed-tile .by{position:absolute;left:0;right:0;bottom:0;padding:8px;color:#fff;font-size:12px;font-weight:600;background:linear-gradient(transparent,rgba(0,0,0,.7))}

  @media(max-width:820px){.hero .wrap{grid-template-columns:1fr}.stage{height:320px;order:-1}}
</style>
@endsection

@section('content')
<section class="hero">
  <div class="wrap">
    <div>
      <div class="eyebrow">For JNTU engineering students</div>
      <h1>Study smarter with <span class="hl">free eBooks</span> &amp; a student feed.</h1>
      <p class="sub">Thousands of engineering books and notes, plus photos and videos shared by students. Browse everything free — sign in only when you're ready to read.</p>
      <div class="cta-row">
        <a href="{{ route('public.books') }}" class="btn btn-primary"><i class="fa fa-book-open"></i> Browse books</a>
        <a href="{{ route('public.feed') }}" class="btn btn-gold"><i class="fa fa-photo-film"></i> Open the feed</a>
      </div>
    </div>

    <div class="stage">
      @php $heroBooks = $featured_books->take(3)->values(); @endphp
      @if(isset($heroBooks[0]))<div class="float f1">@include('public.partials.book_cover', ['book' => $heroBooks[0]])</div>@endif
      @if(isset($heroBooks[1]))<div class="float f2">@include('public.partials.book_cover', ['book' => $heroBooks[1]])</div>@endif
      @if(isset($heroBooks[2]))<div class="float f3">@include('public.partials.book_cover', ['book' => $heroBooks[2]])</div>@endif
    </div>
  </div>
</section>

{{-- Featured / default books --}}
<section class="section">
  <div class="wrap">
    <div class="head">
      <div><div class="eyebrow">Start reading</div><h2>Featured books</h2></div>
      <a href="{{ route('public.books') }}">View all →</a>
    </div>
    <div class="grid-books">
      @foreach($featured_books as $b)
        <a href="{{ route('public.book', $b->id.'-'.Str::slug($b->title)) }}" class="book-card">
          <div class="cover">@include('public.partials.book_cover', ['book' => $b])</div>
          <div class="meta">
            <div class="bt">{{ $b->title }}</div>
            <div class="bc">{{ \App\Category::getCategoryInfo($b->cat_id,'category_name') ?? 'eBook' }}</div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- Feed teaser --}}
@if(count($feed_teaser))
<section class="section" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="head">
      <div><div class="eyebrow">From students</div><h2>Latest in the feed</h2></div>
      <a href="{{ route('public.feed') }}">Open feed →</a>
    </div>
    <div class="feed-strip">
      @foreach($feed_teaser as $p)
        <a href="{{ route('public.post', $p->id) }}" class="feed-tile">
          <img src="{{ $p->media_type=='video' ? ($p->thumb_url ?: '') : $p->file_url }}" alt="{{ $p->title ?: 'Student post' }}" loading="lazy">
          @if($p->media_type=='video')<span class="play"><i class="fa fa-circle-play"></i></span>@endif
          <span class="by">{{ $p->uploaderName() }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
