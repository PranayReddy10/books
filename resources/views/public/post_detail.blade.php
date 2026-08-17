@extends('public.layout')

@section('meta_title', ($post->title ? stripslashes($post->title).' — ' : 'Post — ').'JNTU Books')
@section('meta_description', Str::limit(strip_tags(stripslashes($post->description ?: $post->title)), 155) ?: 'A student post on JNTU Books.')
@section('og_type', $post->media_type=='video' ? 'video.other' : 'article')
@section('og_image', $post->media_type=='video' ? ($post->thumb_url ?: '') : ($post->file_url ?: ''))

@section('head')
<style>
  .wrapn{max-width:620px;margin:30px auto;padding:0 16px}
  .post{background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden}
  .post .top{display:flex;align-items:center;gap:10px;padding:14px 16px}
  .post .av{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--violet),var(--gold));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
  .post .who b{font-family:'Sora'}
  .post .who span{color:var(--muted);font-size:12px;display:block}
  .post .media{background:#0b0b12}
  .post .media img,.post .media video{width:100%;max-height:76vh;object-fit:contain;display:block;margin:0 auto}
  .post .body{padding:14px 18px 18px}
  .stats{display:flex;gap:18px;color:var(--muted);font-size:14px;margin:10px 0}
  .stats i{color:var(--violet)}
  .cbox{border-top:1px solid var(--line);margin-top:14px;padding-top:14px}
  .cbox h4{font-family:'Sora';font-size:15px;margin-bottom:10px}
  .cmt{display:flex;gap:10px;margin-bottom:12px}
  .cmt .ca{width:32px;height:32px;border-radius:50%;background:#eceafd;color:var(--violet);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex:none}
  .cmt .ct b{font-size:13px;font-family:'Sora'}
  .cmt .ct p{font-size:14px;color:#3a3d63}
  .gate{background:var(--paper);border:1px dashed var(--line);border-radius:12px;padding:14px;text-align:center;font-size:14px;color:var(--muted);margin-top:12px}
  .gate a{color:var(--violet);font-weight:600}
</style>
@endsection

@section('content')
<div class="wrapn">
  <p style="margin-bottom:14px"><a href="{{ route('public.feed') }}" style="color:var(--violet);font-weight:600">← Back to feed</a></p>

  <article class="post">
    <div class="top">
      <div class="av">{{ strtoupper(substr($post->uploaderName(),0,1)) }}</div>
      <div class="who">
        <b>{{ $post->uploaderName() }}</b>
        <span>{{ $post->created_at ? $post->created_at->format('d M Y, h:i A') : '' }}</span>
      </div>
    </div>
    @if($post->file_url)
    <div class="media">
      @if($post->media_type=='video')
        <video src="{{ $post->file_url }}" controls preload="metadata" @if($post->thumb_url) poster="{{ $post->thumb_url }}" @endif></video>
      @else
        @foreach($post->allImages() as $img)
          <img src="{{ $img }}" alt="{{ $post->title ?: 'Student post' }}">
        @endforeach
      @endif
    </div>
    @endif
    <div class="body">
      @if($post->title)<div style="font-family:'Sora';font-weight:600;font-size:17px">{{ stripslashes($post->title) }}</div>@endif
      @if($post->description && trim(strip_tags(stripslashes($post->description))) != '')<div style="color:#3a3d63;margin-top:6px">{{ trim(strip_tags(stripslashes($post->description))) }}</div>@endif

      <div class="stats">
        @if($post->show_views)<span><i class="fa fa-eye"></i> {{ $post->view_count }}</span>@endif
        @if($post->allow_likes)<span><i class="fa fa-heart"></i> {{ $like_count }}</span>@endif
        @if($post->allow_comments)<span><i class="fa fa-comment"></i> {{ count($comments) }}</span>@endif
      </div>
      @if($post->link_url)<a href="{{ $post->link_url }}" target="_blank" rel="nofollow noopener" style="color:var(--violet);font-weight:600"><i class="fa fa-link"></i> Open link</a>@endif
      @if($post->book_id && ($lb = \App\Books::where('id',$post->book_id)->where('status',1)->first()))
        <div style="margin-top:12px"><a href="{{ route('public.book', $lb->id.'-'.Str::slug($lb->title)) }}" class="btn btn-primary"><i class="fa fa-book"></i> View book: {{ Str::limit(stripslashes($lb->title),40) }}</a></div>
      @endif

      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px">
        <button onclick="shareThis('{{ addslashes(stripslashes($post->title ?: 'Post')) }}')" class="btn btn-ghost"><i class="fa fa-share-nodes"></i> Share</button>
        <button onclick="openInApp()" class="btn btn-ghost"><i class="fa fa-mobile-screen"></i> Open in app</button>
      </div>

      @if($post->allow_comments)
      <div class="cbox">
        <h4>Comments</h4>
        @forelse($comments as $c)
          <div class="cmt">
            <div class="ca">{{ strtoupper(substr(\App\User::getUserInfo($c->user_id,'name') ?: 'U',0,1)) }}</div>
            <div class="ct">
              <b>{{ \App\User::getUserInfo($c->user_id,'name') ?: 'User' }}</b>
              <p>{{ stripslashes($c->comment) }}</p>
            </div>
          </div>
        @empty
          <p style="color:var(--muted);font-size:14px">No comments yet.</p>
        @endforelse

        @guest
          <div class="gate">Want to like or comment? <a href="{{ route('register') }}">Join free</a> or <a href="{{ route('login') }}">sign in</a> — or use the app.</div>
        @endguest
      </div>
      @endif
    </div>
  </article>
</div>
@endsection