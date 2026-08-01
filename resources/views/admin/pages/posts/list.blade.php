@extends("admin.admin_app")

@section("content")

<style>
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;margin-top:10px;}
.post-card{background:#2b3648;border-radius:12px;overflow:hidden;display:flex;flex-direction:column;}
.post-media{position:relative;aspect-ratio:1/1;background:#1c2330;}
.post-media img{width:100%;height:100%;object-fit:cover;display:block;}
.post-media .play-badge{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:52px;height:52px;border-radius:50%;background:rgba(220,40,40,.9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;}
.post-media .byline{position:absolute;left:0;right:0;bottom:0;padding:8px 10px;background:linear-gradient(transparent,rgba(0,0,0,.7));color:#fff;font-size:13px;}
.post-media .byline b{color:#4aa8ff;}
.post-body{padding:12px;color:#fff;}
.post-title{font-weight:600;font-size:15px;margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.stat-pills{display:flex;gap:10px;margin-bottom:8px;}
.stat-pills .pill{display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.12);color:#fff;border-radius:20px;padding:4px 10px;font-size:12px;font-weight:600;text-decoration:none;}
.stat-pills .pill i{color:#ff6b6b;}
.stat-pills .pill.off{opacity:.4;}
.stat-pills a.pill:hover{background:rgba(255,255,255,.22);color:#fff;}
.post-date{font-size:11px;color:#9fb0c9;margin-bottom:10px;}
.post-actions{display:flex;align-items:center;gap:8px;}
.post-actions .btn-round{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:none;color:#fff;}
.post-actions .edit{background:#22a06b;}
.post-actions .notify{background:#3b7ddd;}
.post-actions .del{background:#e05353;}
.post-actions .spacer{flex:1;}
.tgl-mini{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#ffffff;}
.tgl-mini label{margin:0;}
.tgl-mini,
.tgl-mini label,
.tgl-mini span {
    color: #fff !important;
}
.switch{position:relative;display:inline-block;width:44px;height:24px;}
.switch input{opacity:0;width:0;height:0;}
.slider{position:absolute;cursor:pointer;inset:0;background:#5a6577;border-radius:24px;transition:.2s;}
.slider:before{content:"";position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.2s;}
input:checked+.slider{background:#22a06b;}
input:checked+.slider:before{transform:translateX(20px);}
.toggle-row{display:flex;flex-wrap:wrap;gap:12px;margin-top:10px;padding-top:10px;border-top:1px solid #3a4658;}
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="d-flex align-items-center mb-3">
            <h4 class="m-0">Posts</h4>
            <div class="ml-auto">
              <form method="GET" action="{{ URL::to('admin/posts') }}" class="form-inline">
                <select name="source" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                  <option value="">All sources</option>
                  <option value="admin" {{ $source=='admin'?'selected':'' }}>Admin uploads</option>
                  <option value="user" {{ $source=='user'?'selected':'' }}>User uploads</option>
                </select>
                <select name="type" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                  <option value="">All types</option>
                  <option value="photo" {{ $type=='photo'?'selected':'' }}>Photos</option>
                  <option value="video" {{ $type=='video'?'selected':'' }}>Videos</option>
                </select>
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm mr-2" placeholder="Search title / description">
                <button class="btn btn-sm btn-primary mr-2"><i class="fa fa-search"></i></button>
                <a href="{{ URL::to('admin/posts') }}" class="btn btn-sm btn-secondary" title="Refresh"><i class="fa fa-refresh"></i></a>
              </form>
            </div>
          </div>

          @if(Session::has('flash_message'))
            <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
          @endif

          <div class="posts-grid">
            @foreach($list as $post)
            <div class="post-card" id="card-{{ $post->id }}">
              <div class="post-media">
                @php
                  $preview = $post->media_type=='video' ? ($post->thumb_url ?: '') : $post->file_url;
                @endphp
                @if($preview)
                  <img src="{{ $preview }}" onerror="this.style.opacity=0">
                @endif
                @if($post->media_type=='video')<div class="play-badge"><i class="fa fa-play"></i></div>@endif
                <div class="byline">Post by <b>{{ $post->uploaderName() }}</b></div>
              </div>
              <div class="post-body">
                <div class="post-title">{{ Str::limit(stripslashes($post->title), 28) ?: 'Untitled' }}</div>
                <div class="post-date"><i class="fa fa-clock-o"></i> {{ $post->created_at ? $post->created_at->format('d M Y, h:i A') : '-' }}</div>

                <div class="stat-pills">
                  <span class="pill {{ $post->show_views?'':'off' }}"><i class="fa fa-eye"></i> {{ $post->view_count }}</span>
                  <span class="pill {{ $post->allow_likes?'':'off' }}"><i class="fa fa-thumbs-up"></i> {{ $post->likesCount() }}</span>
                  <a href="{{ URL::to('admin/posts/comments/'.$post->id) }}" class="pill {{ $post->allow_comments?'':'off' }}"><i class="fa fa-comment"></i> {{ $post->commentsCount() }}</a>
                </div>

                <div class="post-actions">
                  <a href="{{ URL::to('admin/media/edit/'.$post->id) }}" class="btn-round edit" title="Edit / Details"><i class="fa fa-pencil"></i></a>
                  <a href="{{ URL::to('admin/posts/notify/'.$post->id) }}" class="btn-round notify notify_btn" title="Send notification"><i class="fa fa-bell"></i></a>
                  <a href="{{ URL::to('admin/posts/delete/'.$post->id) }}" class="btn-round del data_remove" title="Delete"><i class="fa fa-times"></i></a>
                  <span class="spacer"></span>
                  <label class="switch" title="Published">
                    <input type="checkbox" class="tgl" data-id="{{ $post->id }}" data-field="status" {{ $post->status?'checked':'' }}>
                    <span class="slider"></span>
                  </label>
                </div>

                <div class="toggle-row">
                  <span class="tgl-mini"><label class="switch"><input type="checkbox" class="tgl" data-id="{{ $post->id }}" data-field="show_views" {{ $post->show_views?'checked':'' }}><span class="slider"></span></label> Views</span>
                  <span class="tgl-mini"><label class="switch"><input type="checkbox" class="tgl" data-id="{{ $post->id }}" data-field="allow_likes" {{ $post->allow_likes?'checked':'' }}><span class="slider"></span></label> Likes</span>
                  <span class="tgl-mini"><label class="switch"><input type="checkbox" class="tgl" data-id="{{ $post->id }}" data-field="allow_comments" {{ $post->allow_comments?'checked':'' }}><span class="slider"></span></label> Comments</span>
                </div>
              </div>
            </div>
            @endforeach
          </div>

          @if(count($list)==0)
            <div class="text-center text-muted" style="padding:40px;">No approved posts yet. Items appear here once approved in <a href="{{ URL::to('admin/media') }}">Media Feed</a>.</div>
          @endif

          <nav class="mt-3">@include('admin.pagination', ['paginator' => $list])</nav>

        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
<script>
window.addEventListener('load', function () {
  // AJAX toggles
  document.querySelectorAll('.tgl').forEach(function(el){
    el.addEventListener('change', function(){
      var id = this.dataset.id, field = this.dataset.field, box = this;
      fetch("{{ URL::to('admin/posts/toggle') }}/"+id+"/"+field, {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(function(r){return r.json();})
        .then(function(d){ if(!d || !d.success){ box.checked = !box.checked; } })
        .catch(function(){ box.checked = !box.checked; });
    });
  });
  // delete confirm
  document.querySelectorAll('.data_remove').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault(); var url=this.getAttribute('href');
      Swal.fire({title:'Delete this post?',text:'Its likes & comments will also be removed.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#d33'})
        .then(function(r){ if(r.isConfirmed) window.location.href=url; });
    });
  });
  // notify confirm
  document.querySelectorAll('.notify_btn').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault(); var url=this.getAttribute('href');
      Swal.fire({title:'Send notification?',text:'All app users will be notified about this post.',icon:'question',showCancelButton:true,confirmButtonText:'Send',confirmButtonColor:'#22a06b'})
        .then(function(r){ if(r.isConfirmed) window.location.href=url; });
    });
  });
});
</script>

@endsection
