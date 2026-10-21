@extends("admin.admin_app")

@section("content")

{{-- Review queue for books students upload from the app. --}}
<style>
.ub-tabs{ display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px; }
.ub-tab{ display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:9px; border:1px solid var(--ad-line); background:#fff; color:var(--ad-ink-2) !important; font-weight:600; font-size:13px; }
.ub-tab:hover{ border-color:#cfd6e2; }
.ub-tab.is-active{ background:var(--ad-ink); border-color:var(--ad-ink); color:#fff !important; }
.ub-tab .n{ min-width:20px; padding:1px 7px; border-radius:999px; background:var(--ad-surface-2); color:var(--ad-ink-2); font-size:11.5px; text-align:center; }
.ub-tab.is-active .n{ background:rgba(255,255,255,.18); color:#fff; }
.ub-tab.is-pending .n{ background:var(--ad-amber-soft); color:var(--ad-amber); }
.ub-intro{ color:var(--ad-muted); font-size:13px; margin:-6px 0 16px; }

.ub-item{ display:grid; grid-template-columns:64px minmax(0,1.6fr) minmax(0,1fr) minmax(0,1fr) auto; gap:18px; align-items:center; padding:16px 0; border-bottom:1px solid var(--ad-line-2); }
.ub-item:last-child{ border-bottom:0; }
.ub-cover{ width:64px; }
.ub-cover img, .ub-cover .auto-cover{ width:64px; height:86px !important; border-radius:8px; object-fit:cover; box-shadow:var(--ad-shadow); }
.ub-cover .auto-cover-inner{ padding:6px; }
.ub-cover .auto-cover-title{ font-size:8.5px; -webkit-line-clamp:5; word-break:normal; overflow-wrap:normal; hyphens:auto; }
.content-page .ub-title{ font-weight:700; color:var(--ad-ink) !important; font-size:14.5px; display:block; }
.content-page .ub-title:hover{ color:var(--ad-accent) !important; }
.ub-desc{ font-size:12.5px; color:var(--ad-muted); margin-top:3px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.ub-chips{ display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
.ub-chip{ font-size:11.5px; font-weight:600; padding:2px 8px; border-radius:999px; background:var(--ad-surface-2); color:var(--ad-ink-2); border:1px solid var(--ad-line); }
.ub-lbl{ font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:var(--ad-faint); font-weight:600; margin-bottom:3px; }
.ub-who{ display:flex; align-items:center; gap:10px; min-width:0; }
.ub-av{ width:34px; height:34px; border-radius:50%; background:var(--ad-accent-soft); color:var(--ad-accent); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex:0 0 34px; }
.content-page .ub-who a{ font-weight:600; color:var(--ad-ink) !important; }
.ub-who small{ display:block; color:var(--ad-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ub-reason{ margin-top:8px; font-size:12.5px; color:#991b1b; background:var(--ad-red-soft); border-radius:8px; padding:6px 10px; }
.ub-actions{ display:flex; gap:6px; align-items:center; justify-content:flex-end; flex-wrap:wrap; }
.ub-actions .btn{ padding:7px 12px; font-size:12.5px; }
.ub-empty{ text-align:center; color:var(--ad-muted); padding:48px 10px; }
.ub-empty i{ display:block; font-size:26px; color:var(--ad-faint); margin-bottom:8px; }
@media (max-width: 1100px){
  .ub-item{ grid-template-columns:64px 1fr; }
  .ub-item > .ub-col{ grid-column:2; }
  .ub-actions{ grid-column:1 / -1; justify-content:flex-start; }
}
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="card-box">

        <div class="ub-tabs">
          <a href="{{ URL::to('admin/user_books?filter=pending') }}" class="ub-tab is-pending {{ $filter=='pending' ? 'is-active' : '' }}">Waiting for review <span class="n">{{ $pending_count }}</span></a>
          <a href="{{ URL::to('admin/user_books?filter=approved') }}" class="ub-tab {{ $filter=='approved' ? 'is-active' : '' }}">Approved <span class="n">{{ $approved_count }}</span></a>
          <a href="{{ URL::to('admin/user_books?filter=rejected') }}" class="ub-tab {{ $filter=='rejected' ? 'is-active' : '' }}">Rejected <span class="n">{{ $rejected_count }}</span></a>
        </div>
        @if($filter=='pending')
          <p class="ub-intro">Books students uploaded from the app. Open the file, check it is not a copyrighted textbook, then approve to publish it. Uploads from verified students skip this queue.</p>
        @endif

        @if(Session::has('flash_message'))
          <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
        @endif

        @forelse($list as $book)
          @php
            $uploader = $book->uploaded_by ? \App\User::find($book->uploaded_by) : null;
            $initials = $uploader ? collect(preg_split('/\s+/', trim($uploader->name)))->filter()->take(2)->map(function ($w) { return mb_strtoupper(mb_substr($w, 0, 1)); })->implode('') : '?';
            $file_url = $book->url ? book_asset_url($book->url) : '';
            $dept_names = \DB::table('book_department')->join('departments','departments.id','=','book_department.department_id')->where('book_department.book_id',$book->id)->pluck('departments.department_name');
          @endphp
          <div class="ub-item">
            <div class="ub-cover">@include('admin.partials.book_cover', ['book' => $book, 'class' => 'ub-cover-img'])</div>

            <div class="ub-col">
              <a href="{{ URL::to('admin/books/edit/'.$book->id) }}" class="ub-title">{{ stripslashes($book->title) }}</a>
              @if($book->description)<div class="ub-desc">{{ strip_tags(stripslashes($book->description)) }}</div>@endif
              <div class="ub-chips">
                <span class="ub-chip">{{ \App\Category::getCategoryInfo($book->cat_id,'category_name') ?: 'No category' }}</span>
                @foreach($dept_names->take(2) as $dn)<span class="ub-chip">{{ $dn }}</span>@endforeach
                @if($dept_names->count() > 2)<span class="ub-chip">+{{ $dept_names->count() - 2 }}</span>@endif
              </div>
              @if($book->upload_status=='rejected' && $book->reject_reason)<div class="ub-reason"><i class="fa fa-ban"></i> {{ $book->reject_reason }}</div>@endif
            </div>

            <div class="ub-col">
              <div class="ub-lbl">Uploaded by</div>
              @if($uploader)
                <div class="ub-who">
                  <span class="ub-av">{{ $initials }}</span>
                  <span style="min-width:0;">
                    <a href="{{ url('admin/users/history/'.$uploader->id) }}">{{ $uploader->name }}</a>
                    @if($uploader->is_verified)<i class="fa fa-check-circle text-primary" title="Verified"></i>@endif
                    <small>{{ $uploader->college ?: $uploader->email }}</small>
                  </span>
                </div>
              @else
                <span class="text-muted">Deleted account</span>
              @endif
            </div>

            <div class="ub-col">
              <div class="ub-lbl">File</div>
              @if($file_url)
                <a href="{{ $file_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><i class="fa {{ $book->url_type=='local' ? 'fa-file-pdf-o' : 'fa-external-link' }}"></i> Open {{ $book->url_type=='local' ? 'file' : 'link' }}</a>
              @else
                <span class="text-muted">No file</span>
              @endif
            </div>

            <div class="ub-actions">
              @if($book->upload_status!='approved')
                <a href="{{ URL::to('admin/user_books/approve/'.$book->id) }}" class="btn btn-success approve_btn"><i class="fa fa-check"></i> Approve</a>
              @endif
              @if($book->upload_status!='rejected')
                <button type="button" class="btn btn-secondary reject_btn" data-id="{{ $book->id }}"><i class="fa fa-ban"></i> Reject</button>
              @endif
              <a href="{{ URL::to('admin/books/edit/'.$book->id) }}" class="btn btn-secondary" title="Edit"><i class="fa fa-pencil"></i></a>
              <a href="{{ URL::to('admin/user_books/delete/'.$book->id) }}" class="btn btn-secondary data_remove" title="Delete"><i class="fa fa-trash-o"></i></a>
            </div>
          </div>
        @empty
          <div class="ub-empty">
            <i class="fa {{ $filter=='pending' ? 'fa-check-circle' : 'fa-inbox' }}"></i>
            {{ $filter=='pending' ? 'All caught up. No uploads waiting for review.' : 'No '.$filter.' uploads.' }}
          </div>
        @endforelse

        <nav class="paging_simple_numbers mt-3">
          @include('admin.pagination', ['paginator' => $list->appends(['filter' => $filter])])
        </nav>

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
