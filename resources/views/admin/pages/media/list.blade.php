@extends("admin.admin_app")

@section("content")

<style>
.media-table{border-collapse:separate;border-spacing:0 8px;}
.media-table thead th{border:none;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#8a94a6;}
.media-table tbody tr{background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06);}
.media-table tbody td{border:none;vertical-align:middle;padding:10px 12px;}
.media-table tbody tr td:first-child{border-radius:8px 0 0 8px;}
.media-table tbody tr td:last-child{border-radius:0 8px 8px 0;}
.media-thumb{position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;background:#1c2330;}
.media-thumb img{width:100%;height:100%;object-fit:cover;}
.media-thumb .play-dot{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:28px;height:28px;border-radius:50%;background:rgba(220,40,40,.9);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;}
.media-title{font-weight:600;color:#2b3648;}
.media-title:hover{color:#4aa8ff;}
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">

            <div class="row mb-3">
              <div class="col-md-8">
                <a href="{{ URL::to('admin/media?filter=pending') }}" class="btn btn-sm {{ $filter=='pending'?'btn-primary':'btn-secondary' }}">Pending @if($pending_count>0)<span class="badge badge-light">{{ $pending_count }}</span>@endif</a>
                <a href="{{ URL::to('admin/media?filter=approved') }}" class="btn btn-sm {{ $filter=='approved'?'btn-primary':'btn-secondary' }}">Approved</a>
                <a href="{{ URL::to('admin/media?filter=rejected') }}" class="btn btn-sm {{ $filter=='rejected'?'btn-primary':'btn-secondary' }}">Rejected</a>
              </div>
              <div class="col-md-4 text-right">
                <a href="{{ URL::to('admin/media/add') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Media</a>
              </div>
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
            @endif

            <table class="table media-table mb-0">
              <thead>
                <tr>
                  <th>Preview</th>
                  <th>Type</th>
                  <th>Title</th>
                  <th>Uploaded By</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($list as $post)
                <tr>
                  <td>
                    <div class="media-thumb">
                    @if($post->media_type=='photo')
                      <img src="{{ $post->file_url }}" onerror="this.style.display='none'">
                    @elseif($post->media_type=='text')
                      <span class="play-dot"><i class="fa fa-align-left"></i></span>
                    @else
                      @if($post->thumb_url)
                        <img src="{{ $post->thumb_url }}" onerror="this.style.display='none'">
                      @endif
                      <span class="play-dot"><i class="fa fa-play"></i></span>
                    @endif
                    </div>
                  </td>
                  <td>
                    @if($post->media_type=='photo')<span class="badge badge-info"><i class="fa fa-image"></i> Photo</span>
                    @elseif($post->media_type=='text')<span class="badge badge-secondary"><i class="fa fa-align-left"></i> Text</span>
                    @else<span class="badge badge-dark"><i class="fa fa-video-camera"></i> Video</span>@endif
                  </td>
                  <td>
                    <a href="{{ URL::to('admin/media/edit/'.$post->id) }}" class="media-title">{{ Str::limit(stripslashes($post->title), 40) ?: 'Untitled' }}</a>
                    @if($post->description)<br><small class="text-muted">{{ Str::limit(stripslashes($post->description), 60) }}</small>@endif
                  </td>
                  <td>
                    @if($post->is_admin_upload)
                      <span class="badge badge-primary">Admin</span>
                    @elseif($post->user_id)
                      <a href="{{ url('admin/users/history/'.$post->user_id) }}" title="View user history">{{ \App\User::getUserInfo($post->user_id,'name') }}</a>
                    @else - @endif
                  </td>
                  <td><small>{{ $post->created_at ? $post->created_at->format('d M Y') : '-' }}<br><span class="text-muted">{{ $post->created_at ? $post->created_at->format('h:i A') : '' }}</span></small></td>
                  <td>
                    @if($post->upload_status=='pending')<span class="badge badge-warning">Pending</span>
                    @elseif($post->upload_status=='approved')<span class="badge badge-success">Approved</span>
                    @else<span class="badge badge-danger">Rejected</span>@endif
                    @if($post->reject_reason)<br><small class="text-muted">{{ $post->reject_reason }}</small>@endif
                  </td>
                  <td style="white-space:nowrap;">
                    <a href="{{ URL::to('admin/media/edit/'.$post->id) }}" class="btn btn-icon btn-primary btn-sm" title="Edit / Details"><i class="fa fa-pencil"></i></a>
                    @if($post->upload_status!='approved')
                      <a href="{{ URL::to('admin/media/approve/'.$post->id) }}" class="btn btn-icon btn-success btn-sm approve_btn" title="Approve"><i class="fa fa-check"></i></a>
                    @endif
                    @if($post->upload_status!='rejected')
                      <button class="btn btn-icon btn-warning btn-sm reject_btn" data-id="{{ $post->id }}" title="Reject"><i class="fa fa-ban"></i></button>
                    @endif
                    <a href="{{ URL::to('admin/media/delete/'.$post->id) }}" class="btn btn-icon btn-danger btn-sm data_remove" title="Delete"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>
                @endforeach
                @if(count($list)==0)
                <tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No {{ $filter }} media.</td></tr>
                @endif
              </tbody>
            </table>

            <nav class="paging_simple_numbers mt-3">
              @include('admin.pagination', ['paginator' => $list])
            </nav>

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
$(".reject_btn").click(function(){
  var id = $(this).data('id');
  Swal.fire({
    title: 'Reject this media?',
    input: 'text',
    inputLabel: 'Reason (optional)',
    inputPlaceholder: 'e.g. inappropriate / low quality',
    showCancelButton: true,
    confirmButtonText: 'Reject',
    confirmButtonColor: '#f0ad4e',
    background:"#fff"
  }).then(function(result){
    if(result.isConfirmed){
      var reason = encodeURIComponent(result.value || 'Not approved');
      window.location.href = "{{ URL::to('admin/media/reject') }}/"+id+"?reject_reason="+reason;
    }
  });
});
$(".approve_btn").click(function(e){
  e.preventDefault();
  var url=$(this).attr('href');
  Swal.fire({title:'Approve & publish this media?',icon:'question',showCancelButton:true,confirmButtonText:'Approve',confirmButtonColor:'#28a745'})
    .then(function(r){ if(r.isConfirmed) window.location.href=url; });
});
$(".data_remove").click(function(e){
  e.preventDefault();
  var url=$(this).attr('href');
  Swal.fire({title:'Delete permanently?',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#d33'})
    .then(function(r){ if(r.isConfirmed) window.location.href=url; });
});
});
</script>

@endsection
