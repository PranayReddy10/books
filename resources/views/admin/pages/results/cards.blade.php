@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">
            <h4 class="header-title m-b-20">Generated Report Cards</h4>
            <table class="table table-hover m-0">
              <thead>
                <tr><th>#</th><th>Result ID</th><th>Verified</th><th>Generated</th><th>File</th></tr>
              </thead>
              <tbody>
                @forelse($list as $c)
                <tr>
                  <td>{{ $c->id }}</td>
                  <td><a href="{{ url('admin/results/view/'.$c->result_id) }}">#{{ $c->result_id }}</a></td>
                  <td>@if($c->verified_at_generation)<span class="badge badge-success">Yes</span>@else<span class="badge badge-secondary">No</span>@endif</td>
                  <td>{{ $c->generated_at ? $c->generated_at->format('d M Y H:i') : '-' }}</td>
                  <td><a href="{{ $c->pdf_url }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-image"></i> Open</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No cards generated yet</td></tr>
                @endforelse
              </tbody>
            </table>
            <div class="m-t-20">{!! $list->links() !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
