@extends('public.layout')

@section('meta_title', 'My Account — JNTU Books')
@section('meta_description', 'Your JNTU Books account.')

@section('head')
<style>
  .acct{padding:40px 0}
  .acct .wrap{display:grid;grid-template-columns:320px 1fr;gap:34px;align-items:start}
  .profile-card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:26px;text-align:center;position:sticky;top:90px}
  .avatar{width:92px;height:92px;border-radius:50%;margin:0 auto 14px;background:linear-gradient(135deg,var(--violet),var(--gold));color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Sora';font-weight:800;font-size:34px;overflow:hidden}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .profile-card h2{font-family:'Sora';font-size:21px}
  .profile-card .handle{color:var(--violet);font-weight:600;font-size:14px;margin-top:2px}
  .details{margin-top:20px;text-align:left}
  .details .row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid var(--line);font-size:14px}
  .details .row:last-child{border-bottom:none}
  .details .row .k{color:var(--muted)}
  .details .row .v{font-weight:600;text-align:right}
  .acct-actions{display:flex;flex-direction:column;gap:10px;margin-top:20px}
  .btn-block{width:100%;justify-content:center}
  .sec{margin-bottom:34px}
  .sec .head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
  .sec h3{font-family:'Sora';font-size:20px}
  .mini-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px}
  .mini-tile{border-radius:12px;overflow:hidden;border:1px solid var(--line);background:#fff;position:relative;aspect-ratio:1}
  .mini-tile img{width:100%;height:100%;object-fit:cover}
  .mini-tile .cap{position:absolute;left:0;right:0;bottom:0;padding:8px;font-size:12px;font-weight:600;color:#fff;background:linear-gradient(transparent,rgba(0,0,0,.7))}
  .mini-tile .status{position:absolute;top:8px;left:8px;font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;background:#fff;color:#333}
  .mini-tile .play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;text-shadow:0 2px 8px rgba(0,0,0,.5)}
  .empty{color:var(--muted);font-size:14px;padding:20px;background:#fff;border:1px dashed var(--line);border-radius:12px;text-align:center}
  @media(max-width:820px){.acct .wrap{grid-template-columns:1fr}.profile-card{position:static}}
</style>
@endsection

@section('content')
<section class="acct">
  <div class="wrap">

    {{-- Profile --}}
    <div class="profile-card">
      <div class="avatar">
        @if($user->user_image)
          <img src="{{ user_image_url($user->user_image) }}" alt="{{ $user->name }}" onerror="this.style.display='none';this.parentNode.innerText='{{ strtoupper(substr($user->name,0,1)) }}'">
        @else
          {{ strtoupper(substr($user->name,0,1)) }}
        @endif
      </div>
      <h2>{{ stripslashes($user->name) }}</h2>
      @if($user->username)<div class="handle">&#64;{{ $user->username }}</div>@endif

      <div class="details">
        <div class="row"><span class="k">Email</span><span class="v">{{ $user->email }}</span></div>
        @if($user->phone)<div class="row"><span class="k">Phone</span><span class="v">{{ $user->phone }}</span></div>@endif
        @if($user->rollnumber)<div class="row"><span class="k">Roll No</span><span class="v">{{ $user->rollnumber }}</span></div>@endif
        @if($user->university)<div class="row"><span class="k">University</span><span class="v">{{ stripslashes($user->university) }}</span></div>@endif
        @if($user->department_id)<div class="row"><span class="k">Department</span><span class="v">{{ \App\Department::where('id',$user->department_id)->value('department_name') ?: '-' }}</span></div>@endif
        @if($user->college)<div class="row"><span class="k">College</span><span class="v">{{ stripslashes($user->college) }}</span></div>@endif
        @if($user->gender)<div class="row"><span class="k">Gender</span><span class="v">{{ $user->gender }}</span></div>@endif
      </div>

      <div class="acct-actions">
        <a href="{{ route('public.books') }}" class="btn btn-primary btn-block"><i class="fa fa-book-open"></i> Browse books</a>
        <a href="https://www.instagram.com/jntu_books_updates/" target="_blank" rel="noopener" class="btn btn-ghost btn-block"><i class="fa-brands fa-instagram"></i> Follow on Instagram</a>
        <a href="https://whatsapp.com/channel/0029Vb5uthA7NoZyvanZPr1J" target="_blank" rel="noopener" class="btn btn-ghost btn-block"><i class="fa-brands fa-whatsapp"></i> Join WhatsApp Channel</a>
        <a href="{{ route('logout') }}" class="btn btn-ghost btn-block"><i class="fa fa-right-from-bracket"></i> Log out</a>
      </div>
    </div>

    {{-- Content --}}
    <div>
      <div class="sec">
        <div class="head"><h3>My posts</h3></div>
        @if(count($my_posts))
          <div class="mini-grid">
            @foreach($my_posts as $p)
              <a href="{{ route('public.post',$p->id) }}" class="mini-tile">
                <img src="{{ $p->media_type=='video' ? ($p->thumb_url ?: '') : $p->file_url }}" alt="{{ $p->title }}" loading="lazy">
                @if($p->media_type=='video')<span class="play"><i class="fa fa-circle-play"></i></span>@endif
                @if($p->upload_status!='approved')<span class="status">{{ ucfirst($p->upload_status) }}</span>@endif
                @if($p->title)<span class="cap">{{ Str::limit(stripslashes($p->title),24) }}</span>@endif
              </a>
            @endforeach
          </div>
        @else
          <div class="empty">You haven't posted any photos or videos yet. Use the app to upload.</div>
        @endif
      </div>

      <div class="sec">
        <div class="head"><h3>My uploaded books</h3></div>
        @if(count($my_books))
          <div class="grid-books">
            @foreach($my_books as $b)
              <a href="{{ route('public.book', $b->id.'-'.Str::slug($b->title)) }}" class="book-card">
                <div class="cover">@include('public.partials.book_cover', ['book' => $b])</div>
                <div class="meta"><div class="bt">{{ $b->title }}</div>
                  @if($b->upload_status && $b->upload_status!='approved')<div class="bc">{{ ucfirst($b->upload_status) }}</div>@endif
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="empty">You haven't uploaded any books yet.</div>
        @endif
      </div>
    </div>

  </div>
</section>
@endsection