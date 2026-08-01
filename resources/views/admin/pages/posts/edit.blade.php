@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
          <div class="card-box">
            <h4 class="header-title m-t-0">Edit Post</h4>

            @if(Session::has('flash_message'))
              <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
            @endif

            <div class="mb-3">
              @if($info->media_type=='video')
                <video src="{{ $info->file_url }}" controls style="max-width:320px;border-radius:8px;"></video>
              @else
                <img src="{{ $info->file_url }}" style="max-width:320px;border-radius:8px;">
              @endif
            </div>

            <form action="{{ URL::to('admin/posts/update/'.$info->id) }}" method="POST">
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
                <small class="text-muted">Shown with the post in the app (e.g. a source or product link).</small>
              </div>

              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
              <a href="{{ URL::to('admin/posts') }}" class="btn btn-secondary">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

@endsection
