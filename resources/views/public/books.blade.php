@extends('public.layout')

@php
  $catName = $cat ? \App\Category::getCategoryInfo($cat,'category_name') : '';
  if ($q) {
    $pageTitle = '"'.$q.'" — Search JNTU Books';
    $pageDesc = 'Search results for "'.$q.'" — free JNTU engineering eBooks and notes.';
  } elseif ($catName) {
    $pageTitle = $catName.' Books & Notes — JNTU Books';
    $pageDesc = 'Free '.$catName.' eBooks and notes for JNTU students. Browse and read online.';
  } else {
    $pageTitle = 'Browse Engineering eBooks & Notes — JNTU Books';
    $pageDesc = 'Browse and search free JNTU engineering eBooks and notes by category. Sign in to read.';
  }
@endphp
@section('meta_title', $pageTitle)
@section('meta_description', $pageDesc)
@if($q)@section('meta_robots', 'noindex, follow')@endif
@if($cat && !$q)@section('canonical', route('public.books', ['cat'=>$cat]))@elseif($q)@section('canonical', route('public.books'))@endif

@section('head')
<style>
  .page-head{padding:40px 0 10px}
  .page-head h1{font-size:34px}
  .page-head p{color:var(--muted);margin-top:6px}
  .toolbar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin:22px 0}
  .toolbar form{display:flex;gap:8px;flex:1;min-width:240px}
  .toolbar input[type=text]{flex:1;padding:11px 14px;border-radius:12px;border:1px solid var(--line);font-size:15px;background:#fff}
  .cats{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px}
  .empty{padding:60px 0;text-align:center;color:var(--muted)}
  .pager{display:flex;gap:8px;justify-content:center;margin-top:34px}
  .pager a,.pager span{padding:9px 14px;border-radius:10px;border:1px solid var(--line);background:#fff;font-weight:600;font-size:14px}
  .pager .cur{background:var(--violet);color:#fff;border-color:var(--violet)}
</style>
@endsection

@section('content')
<div class="wrap page-head">
  <h1>Engineering eBooks &amp; Notes</h1>
  <p>Browse free. Sign in when you're ready to read.</p>

  <div class="toolbar">
    <form method="GET" action="{{ route('public.books') }}">
      <input type="text" name="q" value="{{ $q }}" placeholder="Search books by title…">
      @if($cat)<input type="hidden" name="cat" value="{{ $cat }}">@endif
      <button class="btn btn-primary"><i class="fa fa-magnifying-glass"></i></button>
    </form>
  </div>

  <div class="cats">
    <a href="{{ route('public.books') }}" class="pill {{ $cat=='' ? 'active':'' }}">All</a>
    @foreach($categories as $c)
      <a href="{{ route('public.books', ['cat'=>$c->id]) }}" class="pill {{ $cat==$c->id ? 'active':'' }}">{{ $c->category_name }}</a>
    @endforeach
  </div>
</div>

<div class="wrap">
  @if(count($books))
    <div class="grid-books">
      @foreach($books as $b)
        <a href="{{ route('public.book', $b->id.'-'.Str::slug($b->title)) }}" class="book-card">
          <div class="cover">@include('public.partials.book_cover', ['book' => $b])</div>
          <div class="meta">
            <div class="bt">{{ $b->title }}</div>
            <div class="bc">{{ \App\Category::getCategoryInfo($b->cat_id,'category_name') ?? 'eBook' }}</div>
          </div>
        </a>
      @endforeach
    </div>

    <div class="pager">{{ $books->links('public.pagination') }}</div>
  @else
    <div class="empty">
      <i class="fa fa-book" style="font-size:40px;color:var(--line)"></i>
      <p style="margin-top:12px">No books found. Try a different search or category.</p>
    </div>
  @endif
</div>
@endsection