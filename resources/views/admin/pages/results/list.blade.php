@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      {{-- Stat cards --}}
      <div class="row">
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0">{{ $stats['total'] }}</h4><p class="text-muted m-b-0">Total Results</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-success">{{ $stats['verified'] }}</h4><p class="text-muted m-b-0">Verified</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-warning">{{ $stats['unverified'] }}</h4><p class="text-muted m-b-0">Unverified</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-info">{{ $stats['last7'] }}</h4><p class="text-muted m-b-0">Last 7 days</p></div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">

            {{-- Auto-import from the university feed (jntuhconnect). The first
                 fetch for an unseen roll number usually answers "queued" while
                 the upstream scrapes; press it again after a few seconds. --}}
            @if(config('jntuh.enabled'))
            <div class="row m-b-20">
              <div class="col-md-12">
                {!! Form::open(array('url' => 'admin/results/jntuh-fetch','class'=>'form-inline','role'=>'form','method'=>'post')) !!}
                  <input type="text" name="hall_ticket_no" maxlength="10" placeholder="Hall ticket number" class="form-control m-r-5" required>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-cloud-download"></i> Fetch from JNTUH</button>
                  <span class="text-muted m-l-10">Pulls all semesters automatically. Marks the result verified; you can still edit it.</span>
                {!! Form::close() !!}
              </div>
            </div>
            @endif

            {{-- Filters --}}
            <div class="row m-b-20">
              <div class="col-md-12">
                <div class="float-right m-b-10">
                  <a href="{{ URL::to('admin/results/add') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add Result</a>
                  <a href="{{ URL::to('admin/results-recompute-all') }}" class="btn btn-outline-primary" onclick="return confirm('Recompute CGPA/SGPA for ALL results with the corrected formula? This may take a moment.');"><i class="fa fa-calculator"></i> Recompute all</a>
                </div>
                {!! Form::open(array('url' => 'admin/results','class'=>'form-inline','role'=>'form','method'=>'get')) !!}
                  <input type="text" name="s" value="{{ request('s') }}" placeholder="Hall ticket / name" class="form-control m-r-5">
                  <select name="verified" class="form-control m-r-5">
                    <option value="">All statuses</option>
                    <option value="1" {{ request('verified')==='1'?'selected':'' }}>Verified</option>
                    <option value="0" {{ request('verified')==='0'?'selected':'' }}>Unverified</option>
                  </select>
                  <select name="regulation" class="form-control m-r-5">
                    <option value="">All regulations</option>
                    @foreach(['R22','R18','R16','R13'] as $reg)
                      <option value="{{ $reg }}" {{ request('regulation')==$reg?'selected':'' }}>{{ $reg }}</option>
                    @endforeach
                  </select>
                  <select name="university_id" class="form-control m-r-5 select2">
                    <option value="">All universities</option>
                    @foreach($universities as $u)
                      <option value="{{ $u->id }}" {{ request('university_id')==$u->id?'selected':'' }}>{{ $u->university_name }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-primary">Filter</button>
                {!! Form::close() !!}
              </div>
            </div>

            <table class="table table-hover m-0">
              <thead>
                <tr>
                  <th>Hall Ticket</th><th>Name</th><th>Branch</th><th>Reg</th>
                  <th>CGPA</th><th>Backlogs</th><th>Source</th><th>Status</th><th>Entered</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($list as $r)
                <tr>
                  <td><strong>{{ $r->hall_ticket_no }}</strong></td>
                  <td>{{ $r->student_name }}</td>
                  <td>{{ $r->branch }}</td>
                  <td>{{ $r->regulation }}</td>
                  <td>{{ $r->current_cgpa ?: '-' }}</td>
                  <td>{{ $r->backlogs_count }}</td>
                  <td><span class="badge badge-secondary">{{ ucfirst($r->source) }}</span></td>
                  <td>
                    @if($r->verified)
                      <span class="badge badge-success">Verified</span>
                    @else
                      <span class="badge badge-warning">Unverified</span>
                    @endif
                    @if($r->locked)<span class="badge badge-dark">Locked</span>@endif
                  </td>
                  <td>{{ $r->created_at ? $r->created_at->format('d M Y') : '-' }}</td>
                  <td>
                    <a href="{{ url('admin/results/view/'.$r->id) }}" class="btn btn-icon waves-effect btn-info"><i class="fa fa-eye"></i></a>
                    <a href="{{ url('admin/results/delete/'.$r->id) }}" class="btn btn-icon waves-effect btn-danger data_remove"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted">No results found</td></tr>
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
