@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-8">

          {!! Form::open(array('url' => 'admin/results/update/'.$result->id,'method'=>'post')) !!}
          <div class="card-box">
            <h4 class="header-title m-b-20">Result — {{ $result->hall_ticket_no }}
              @if($result->verified)<span class="badge badge-success">Verified</span>@endif
              @if($result->locked)<span class="badge badge-dark">Locked (student edit disabled)</span>@endif
            </h4>

            <div class="form-group row">
              <label class="col-md-3 col-form-label">Student Name</label>
              <div class="col-md-9"><input type="text" name="student_name" value="{{ $result->student_name }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Hall Ticket No</label>
              <div class="col-md-9"><input type="text" name="hall_ticket_no" value="{{ $result->hall_ticket_no }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Regulation</label>
              <div class="col-md-9">
                <select name="regulation" class="form-control">
                  @foreach(['R22','R18','R16','R13'] as $reg)
                    <option value="{{ $reg }}" {{ $result->regulation==$reg?'selected':'' }}>{{ $reg }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Degree</label>
              <div class="col-md-9"><input type="text" name="degree" value="{{ $result->degree }}" class="form-control" placeholder="B.Tech / B.Pharm"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Branch</label>
              <div class="col-md-9"><input type="text" name="branch" value="{{ $result->branch }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Current CGPA</label>
              <div class="col-md-3"><input type="text" name="current_cgpa" value="{{ $result->current_cgpa }}" class="form-control"></div>
              <label class="col-md-3 col-form-label">Total Credits</label>
              <div class="col-md-3"><input type="text" name="total_credits" value="{{ $result->total_credits }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Backlogs</label>
              <div class="col-md-3"><input type="number" name="backlogs_count" value="{{ $result->backlogs_count }}" class="form-control"></div>
            </div>

            {{-- Semester tables with editable grade/backlog --}}
            @foreach($sems as $sem)
              <h5 class="m-t-30 text-primary">Semester {{ $sem->sem_code }}
                @if($sem->sgpa) | SGPA {{ $sem->sgpa }} @endif
                @if($sem->exam_month_year) | {{ $sem->exam_month_year }} @endif
              </h5>
              <table class="table table-sm table-bordered">
                <thead><tr><th>Code</th><th>Subject</th><th>Grade</th><th>Cr</th><th>Backlog</th></tr></thead>
                <tbody>
                  @foreach($sem->loaded_subjects as $sub)
                  <tr>
                    <td>{{ $sub->subject_code }}</td>
                    <td>{{ $sub->subject_name }}</td>
                    <td><input type="text" name="subjects[{{ $sub->id }}][grade]" value="{{ $sub->grade }}" class="form-control form-control-sm" style="width:70px"></td>
                    <td>{{ $sub->credits }}</td>
                    <td>
                      <select name="subjects[{{ $sub->id }}][is_backlog]" class="form-control form-control-sm">
                        <option value="0" {{ !$sub->is_backlog?'selected':'' }}>No</option>
                        <option value="1" {{ $sub->is_backlog?'selected':'' }}>Yes</option>
                      </select>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            @endforeach

            <button type="submit" class="btn btn-primary m-t-20">Save Corrections</button>
          </div>
          {!! Form::close() !!}

        </div>

        {{-- Actions sidebar --}}
        <div class="col-md-4">
          <div class="card-box">
            <h4 class="header-title m-b-20">Actions</h4>

            @if(!$result->verified)
              <a href="{{ url('admin/results/verify/'.$result->id) }}" class="btn btn-success btn-block m-b-10">
                <i class="fa fa-check"></i> Verify &amp; Lock
              </a>
            @else
              <a href="{{ url('admin/results/unverify/'.$result->id) }}" class="btn btn-warning btn-block m-b-10">
                <i class="fa fa-undo"></i> Un-verify &amp; Unlock
              </a>
            @endif

            <a href="{{ url('admin/results/regenerate/'.$result->id) }}" class="btn btn-info btn-block m-b-10">
              <i class="fa fa-refresh"></i> Regenerate Card
            </a>

            <a href="{{ url('admin/results/delete/'.$result->id) }}" class="btn btn-danger btn-block m-b-10 data_remove">
              <i class="fa fa-trash"></i> Delete
            </a>
          </div>

          <div class="card-box">
            <h4 class="header-title m-b-20">Generated Cards</h4>
            @forelse($cards as $c)
              <div class="m-b-10">
                <a href="{{ $c->pdf_url }}" target="_blank">{{ $c->generated_at ? \Carbon\Carbon::parse($c->generated_at)->format('d M Y H:i') : '' }}</a>
                @if($c->verified_at_generation)<span class="badge badge-success">verified</span>@endif
              </div>
            @empty
              <p class="text-muted">None yet.</p>
            @endforelse
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection
