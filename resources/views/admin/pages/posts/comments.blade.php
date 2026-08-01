@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-9">
          <div class="card-box">
            <div class="d-flex align-items-center mb-3">
              <h4 class="m-0">Comments — {{ Str::limit(stripslashes($post->title),40) ?: 'Post #'.$post->id }}</h4>
              <a href="{{ URL::to('admin/posts') }}" class="btn btn-sm btn-secondary ml-auto">Back to Posts</a>
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
            @endif

            <table class="table table-bordered">
              <thead>
                <tr><th>User</th><th>Comment</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                @foreach($comments as $c)
                <tr class="{{ $c->status?'':'table-secondary' }}">
                  <td>{{ \App\User::getUserInfo($c->user_id,'name') }}</td>
                  <td>{{ stripslashes($c->comment) }}</td>
                  <td>@if($c->status)<span class="badge badge-success">Visible</span>@else<span class="badge badge-secondary">Hidden</span>@endif</td>
                  <td style="white-space:nowrap;">
                    <a href="{{ URL::to('admin/posts/comment_toggle/'.$c->id) }}" class="btn btn-sm {{ $c->status?'btn-warning':'btn-success' }}">{{ $c->status?'Hide':'Show' }}</a>
                    <a href="{{ URL::to('admin/posts/comment_delete/'.$c->id) }}" class="btn btn-sm btn-danger data_remove">Delete</a>
                  </td>
                </tr>
                @endforeach
                @if(count($comments)==0)
                <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">No comments yet.</td></tr>
                @endif
              </tbody>
            </table>

            <nav>@include('admin.pagination', ['paginator' => $comments])</nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
<script>
window.addEventListener('load', function () {
  document.querySelectorAll('.data_remove').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault(); var url=this.getAttribute('href');
      Swal.fire({title:'Delete this comment?',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#d33'})
        .then(function(r){ if(r.isConfirmed) window.location.href=url; });
    });
  });
});
</script>

@endsection
