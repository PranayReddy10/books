@extends('public.layout')

@section('meta_title', 'Student Photo & Video Feed — JNTU Books')
@section('meta_description', 'Scroll through photos and videos shared by JNTU students. Free to browse on JNTU Books.')

@section('head')
<style>
  .feed-head{padding:36px 0 8px}
  .feed-head h1{font-size:32px}
  .feed-head p{color:var(--muted);margin-top:6px}
  .ftabs{display:flex;gap:8px;margin:20px 0}
  .feed-col{max-width:560px;margin:0 auto;padding-bottom:20px}
  .post{background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden;margin-bottom:22px}
  .post .top{display:flex;align-items:center;gap:10px;padding:12px 14px}
  .post .av{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--violet),var(--gold));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
  .post .who b{font-family:'Sora';font-size:14px}
  .post .who span{color:var(--muted);font-size:12px;display:block}
  .post .media{background:#0b0b12}
  .post .media img,.post .media video{width:100%;max-height:70vh;object-fit:contain;display:block;margin:0 auto}
  .post .body{padding:12px 16px 16px}
  .post .cap{font-weight:600;font-family:'Sora'}
  .post .stats{display:flex;gap:16px;color:var(--muted);font-size:14px;margin-top:8px}
  .post .stats i{color:var(--violet)}
  .post .link{display:inline-block;margin-top:10px;color:var(--violet);font-weight:600;font-size:14px}
  .pager{display:flex;gap:8px;justify-content:center;margin-top:20px}
  .pager a,.pager span{padding:9px 14px;border-radius:10px;border:1px solid var(--line);background:#fff;font-weight:600;font-size:14px}
  .pager .cur{background:var(--violet);color:#fff;border-color:var(--violet)}
</style>
@endsection

@section('content')
<div class="wrap feed-head">
  <h1>Student Feed</h1>
  <p>Photos and videos shared by students. Free to browse.</p>
  <div class="ftabs">
    <a href="{{ route('public.feed') }}" class="pill {{ $type=='' ? 'active':'' }}">All</a>
    <a href="{{ route('public.feed',['type'=>'photo']) }}" class="pill {{ $type=='photo' ? 'active':'' }}">Photos</a>
    <a href="{{ route('public.feed',['type'=>'video']) }}" class="pill {{ $type=='video' ? 'active':'' }}">Videos</a>
  </div>
</div>

<div class="wrap">
  <div class="feed-col">
    @forelse($posts as $p)
      <article class="post">
        <div class="top">
          <div class="av">{{ strtoupper(substr($p->uploaderName(),0,1)) }}</div>
          <div class="who">
            <b>{{ $p->uploaderName() }}</b>
            <span>{{ $p->created_at ? $p->created_at->diffForHumans() : '' }}</span>
          </div>
        </div>
        <div class="media">
          @if($p->media_type=='video')
            <video src="{{ $p->file_url }}" controls preload="metadata" @if($p->thumb_url) poster="{{ $p->thumb_url }}" @endif></video>
          @else
            <a href="{{ route('public.post',$p->id) }}"><img src="{{ $p->file_url }}" alt="{{ $p->title ?: 'Student post' }}" loading="lazy"></a>
          @endif
        </div>
        <div class="body">
          @if($p->title)<div class="cap">{{ stripslashes($p->title) }}</div>@endif
          @if($p->description && trim(strip_tags(stripslashes($p->description))) != '')<div style="color:var(--muted);font-size:14px;margin-top:4px">{{ trim(strip_tags(stripslashes($p->description))) }}</div>@endif
          <div class="stats">
            @if($p->show_views)<span><i class="fa fa-eye"></i> {{ $p->view_count }}</span>@endif
            @if($p->allow_likes)<span><i class="fa fa-heart"></i> {{ $p->likesCount() }}</span>@endif
            @if($p->allow_comments)<span><i class="fa fa-comment"></i> {{ $p->commentsCount() }}</span>@endif
            <span style="cursor:pointer" onclick="shareUrl('{{ route('public.post',$p->id) }}','{{ addslashes(stripslashes($p->title ?: 'Post')) }}')"><i class="fa fa-share-nodes"></i> Share</span>
          </div>
          @if($p->link_url)<a href="{{ $p->link_url }}" class="link" target="_blank" rel="nofollow noopener"><i class="fa fa-link"></i> Open link</a>@endif
          @if($p->book_id && ($lb = \App\Books::where('id',$p->book_id)->where('status',1)->first()))
            <a href="{{ route('public.book', $lb->id.'-'.Str::slug($lb->title)) }}" class="link" style="margin-left:12px"><i class="fa fa-book"></i> View book: {{ Str::limit(stripslashes($lb->title),40) }}</a>
          @endif
        </div>
      </article>
    @empty
      <p style="text-align:center;color:var(--muted);padding:50px 0">No posts yet. Check back soon.</p>
    @endforelse

    <div class="pager">{{ $posts->links('public.pagination') }}</div>
  </div>
</div>
@endsection