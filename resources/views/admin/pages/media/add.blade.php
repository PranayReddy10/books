@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
          <div class="card-box">
            <h4 class="header-title m-t-0">Add Media Post</h4>
            <p class="text-muted font-14">Admin uploads publish to the feed immediately.</p>

            @if(Session::has('flash_message'))
              <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
            @endif

            <form action="{{ URL::to('admin/media/store') }}" method="POST" enctype="multipart/form-data">
              {{ csrf_field() }}

              <div class="form-group">
                <label>Media Type</label>
                <select name="media_type" id="media_type" class="form-control" required>
                  <option value="photo">Photo</option>
                  <option value="video">Video</option>
                </select>
              </div>

              <div class="form-group">
                <label>Title (optional)</label>
                <input type="text" name="title" class="form-control" placeholder="Caption / title">
              </div>

              <div class="form-group">
                <label>Description (optional)</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
              </div>

              <div class="form-group">
                <label>Link URL (optional)</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://example.com">
                <small class="text-muted">An external link shown with the post.</small>
              </div>

              <div class="form-group">
                <label>Link a book (optional)</label>
                <select name="book_id" class="form-control select2" data-placeholder="Search and select a book…">
                  <option value="">— None —</option>
                  @foreach($books_list as $b)
                    <option value="{{ $b->id }}">{{ Str::limit(stripslashes($b->title), 70) }}</option>
                  @endforeach
                </select>
                <small class="text-muted">Type to search. Shows a "View book" button on the post.</small>
              </div>

              <div class="form-group">
                <label id="file_label">Photo file</label>
                <input type="file" name="media_file" class="form-control" required>
                <small class="text-muted" id="file_hint">Allowed: jpg, png, gif, webp.</small>
              </div>

              <div class="form-group" id="thumb_group" style="display:none;">
                <label>Video thumbnail (optional)</label>
                <input type="file" name="thumb_file" class="form-control">
                <small class="text-muted">A poster image shown before the video plays.</small>
              </div>

              <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Publish</button>
              <a href="{{ URL::to('admin/media') }}" class="btn btn-secondary">Cancel</a>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script>
window.addEventListener('load', function () {
  var sel = document.getElementById('media_type');
  function sync(){
    var isVideo = sel.value === 'video';
    document.getElementById('thumb_group').style.display = isVideo ? 'block' : 'none';
    document.getElementById('file_label').innerText = isVideo ? 'Video file' : 'Photo file';
    document.getElementById('file_hint').innerText = isVideo
      ? 'Allowed: mp4, mov, webm.'
      : 'Allowed: jpg, png, gif, webp.';
  }
  sel.addEventListener('change', sync);
  sync();
});
</script>

@endsection