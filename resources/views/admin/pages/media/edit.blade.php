@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">

        <div class="col-lg-5">
          <div class="card-box">
            <h4 class="header-title m-t-0">Media Preview</h4>
            @if($info->media_type=='video')
              <video src="{{ $info->file_url }}" controls style="width:100%;border-radius:8px;background:#000;"></video>
            @else
              <img src="{{ $info->file_url }}" style="width:100%;border-radius:8px;">
            @endif

            <table class="table table-sm mt-3 mb-0">
              <tr><th style="width:45%;">Type</th><td>{{ ucfirst($info->media_type) }}</td></tr>
              <tr><th>Uploaded by</th><td>
                @if($info->is_admin_upload)<span class="badge badge-primary">Admin</span>
                @elseif($info->user_id)<a href="{{ url('admin/users/history/'.$info->user_id) }}">{{ \App\User::getUserInfo($info->user_id,'name') }}</a>@else - @endif
              </td></tr>
              <tr><th>Status</th><td>
                @if($info->upload_status=='pending')<span class="badge badge-warning">Pending</span>
                @elseif($info->upload_status=='approved')<span class="badge badge-success">Approved</span>
                @else<span class="badge badge-danger">Rejected</span>@endif
              </td></tr>
              <tr><th>Published</th><td>{{ $info->status ? 'Yes' : 'No (disabled)' }}</td></tr>
              <tr><th>Views</th><td>{{ $info->view_count }}</td></tr>
              <tr><th>Likes</th><td>{{ $like_count }}</td></tr>
              <tr><th>Comments</th><td>{{ $comment_count }}</td></tr>
              <tr><th>Created</th><td>{{ $info->created_at ? $info->created_at->format('d M Y, h:i A') : '-' }}</td></tr>
              <tr><th>Last updated</th><td>{{ $info->updated_at ? $info->updated_at->format('d M Y, h:i A') : '-' }}</td></tr>
              @if($info->reject_reason)<tr><th>Reject reason</th><td class="text-danger">{{ $info->reject_reason }}</td></tr>@endif
            </table>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card-box">
            <h4 class="header-title m-t-0">Edit Details</h4>

            @if(Session::has('flash_message'))
              <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
            @endif

            <form action="{{ URL::to('admin/media/update/'.$info->id) }}" method="POST">
              {{ csrf_field() }}

              <div class="form-group">
                <label>Title / Caption</label>
                <input type="text" name="title" class="form-control" value="{{ stripslashes($info->title) }}">
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4">{{ stripslashes($info->description) }}</textarea>
              </div>

              <div class="form-group">
                <label>Link URL (optional)</label>
                <input type="url" name="link_url" class="form-control" value="{{ $info->link_url }}" placeholder="https://example.com">
                <small class="text-muted">Shown with the post in the app.</small>
              </div>

              <div class="form-group">
                <label>Link a book (optional)</label>
                <select name="book_id" class="form-control select2" data-placeholder="Search and select a book…">
                  <option value="">— None —</option>
                  @foreach($books_list as $b)
                    <option value="{{ $b->id }}" {{ $info->book_id == $b->id ? 'selected' : '' }}>{{ Str::limit(stripslashes($b->title), 70) }}</option>
                  @endforeach
                </select>
                <small class="text-muted">Type to search. Shows a "View book" button on the post.</small>
              </div>

              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
              <a href="{{ URL::to('admin/media') }}" class="btn btn-secondary">Media Feed</a>
              <a href="{{ URL::to('admin/posts') }}" class="btn btn-secondary">Posts</a>
            </form>

            <hr>

            <h5>Actions</h5>
            <div class="d-flex" style="gap:10px;flex-wrap:wrap;">
              @if($info->upload_status!='approved')
                <a href="{{ URL::to('admin/media/approve/'.$info->id) }}" class="btn btn-success"><i class="fa fa-check"></i> Approve</a>
              @endif
              <a href="{{ URL::to('admin/media/toggle_status/'.$info->id) }}" class="btn {{ $info->status?'btn-warning':'btn-success' }}">
                <i class="fa fa-power-off"></i> {{ $info->status ? 'Disable' : 'Enable' }}
              </a>
              <a href="{{ URL::to('admin/media/delete/'.$info->id) }}" class="btn btn-danger data_remove"><i class="fa fa-trash"></i> Delete</a>
            </div>
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
      Swal.fire({title:'Delete this media permanently?',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#d33'})
        .then(function(r){ if(r.isConfirmed) window.location.href=url; });
    });
  });
});
</script>

@endsection