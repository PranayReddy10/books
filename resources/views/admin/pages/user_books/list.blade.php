@extends("admin.admin_app")

@section("content")

<style>
.media-table{border-collapse:separate;border-spacing:0 8px;}
.media-table thead th{border:none;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#8a94a6;}
.media-table tbody tr{background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06);}
.media-table tbody td{border:none;vertical-align:middle;padding:10px 12px;}
.media-table tbody tr td:first-child{border-radius:8px 0 0 8px;}
.media-table tbody tr td:last-child{border-radius:0 8px 8px 0;}
.book-cover{width:46px;height:62px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,.2);}
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
                <a href="{{ URL::to('admin/user_books?filter=pending') }}" class="btn btn-sm {{ $filter=='pending'?'btn-primary':'btn-secondary' }}">Pending @if($pending_count>0)<span class="badge badge-light">{{ $pending_count }}</span>@endif</a>
                <a href="{{ URL::to('admin/user_books?filter=approved') }}" class="btn btn-sm {{ $filter=='approved'?'btn-primary':'btn-secondary' }}">Approved</a>
                <a href="{{ URL::to('admin/user_books?filter=rejected') }}" class="btn btn-sm {{ $filter=='rejected'?'btn-primary':'btn-secondary' }}">Rejected</a>
              </div>
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
            @endif

            <table class="table media-table mb-0">
              <thead>
                <tr>
                  <th>Cover</th>
                  <th>Title</th>
                  <th>Uploaded By</th>
                  <th>Category</th>
                  <th>Source</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($list as $book)
                <tr>
                  <td><img src="{{ book_asset_url($book->image) }}" class="book-cover" onerror="this.style.display='none'"></td>
                  <td>
                    <a href="{{ URL::to('admin/books/edit/'.$book->id) }}" class="media-title">{{ Str::limit(stripslashes($book->title), 40) }}</a>
                    @if($book->description)<br><small class="text-muted">{{ Str::limit(stripslashes($book->description), 60) }}</small>@endif
                  </td>
                  <td>@if($book->uploaded_by)<a href="{{ url('admin/users/history/'.$book->uploaded_by) }}" title="View user history">{{ \App\User::getUserInfo($book->uploaded_by,'name') }}</a>@else - @endif</td>
                  <td>{{ \App\Category::getCategoryInfo($book->cat_id,'category_name') }}</td>
                  <td>
                    @if($book->url_type=='local')
                      <a href="{{ URL::to('/'.$book->url) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file"></i> File</a>
                    @else
                      <a href="{{ $book->url }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-link"></i> Link</a>
                    @endif
                  </td>
                  <td>
                    @if($book->upload_status=='pending')<span class="badge badge-warning">Pending</span>
                    @elseif($book->upload_status=='approved')<span class="badge badge-success">Approved</span>
                    @else<span class="badge badge-danger">Rejected</span>@endif
                    @if($book->reject_reason)<br><small class="text-muted">{{ $book->reject_reason }}</small>@endif
                  </td>
                  <td style="white-space:nowrap;">
                    <a href="{{ URL::to('admin/books/edit/'.$book->id) }}" class="btn btn-icon btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                    @if($book->upload_status!='approved')
                      <a href="{{ URL::to('admin/user_books/approve/'.$book->id) }}" class="btn btn-icon btn-success btn-sm approve_btn" title="Approve"><i class="fa fa-check"></i></a>
                    @endif
                    @if($book->upload_status!='rejected')
                      <button class="btn btn-icon btn-warning btn-sm reject_btn" data-id="{{ $book->id }}" title="Reject"><i class="fa fa-ban"></i></button>
                    @endif
                    <a href="{{ URL::to('admin/user_books/delete/'.$book->id) }}" class="btn btn-icon btn-danger btn-sm data_remove" title="Delete"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>
                @endforeach
                @if(count($list)==0)
                <tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No {{ $filter }} books.</td></tr>
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

<!-- Reject reason form (hidden) -->
<form id="reject_form" method="GET" style="display:none;">
  <input type="hidden" name="reason_holder">
</form>

<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
<script>
// Content renders before jQuery in this theme, so wait for full load
// (jQuery is included at the bottom of the layout) before binding handlers.
window.addEventListener('load', function () {
$(".reject_btn").click(function(){
  var id = $(this).data('id');
  Swal.fire({
    title: 'Reject this book?',
    input: 'text',
    inputLabel: 'Reason (optional)',
    inputPlaceholder: 'e.g. copyright / low quality',
    showCancelButton: true,
    confirmButtonText: 'Reject',
    confirmButtonColor: '#f0ad4e',
    background:"#fff"
  }).then(function(result){
    if(result.isConfirmed){
      var reason = encodeURIComponent(result.value || 'Not approved');
      window.location.href = "{{ URL::to('admin/user_books/reject') }}/"+id+"?reject_reason="+reason;
    }
  });
});
$(".approve_btn").click(function(e){
  e.preventDefault();
  var url=$(this).attr('href');
  Swal.fire({title:'Approve & publish this book?',icon:'question',showCancelButton:true,confirmButtonText:'Approve',confirmButtonColor:'#28a745'})
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
