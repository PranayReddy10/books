@extends("admin.admin_app")

@section("content")

@php
  $GRADES = ['O','A+','A','B+','B','C','F','Ab'];
@endphp

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      @php
        $grade_tone = function ($g) {
          $g = strtoupper(trim((string) $g));
          if (in_array($g, ['O','A+'])) return 'g-top';
          if (in_array($g, ['A','B+'])) return 'g-good';
          if (in_array($g, ['B','C','P'])) return 'g-mid';
          if (in_array($g, ['F','AB','ABSENT'])) return 'g-bad';
          return 'g-none';
        };
        $sem_count = count($sems);
        $sgpa_max = 10;
        $university = $result->university_id ? \App\University::where('id',$result->university_id)->value('university_name') : '';
        $latest_card = $cards->first();
      @endphp

      <style>
        .rv .panel{ background:#fff; border:1px solid var(--ad-line); border-radius:var(--ad-radius); box-shadow:var(--ad-shadow); padding:22px; margin-bottom:20px; }
        .rv-head{ display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; }
        .rv-name{ font-size:22px; font-weight:700; color:var(--ad-ink); margin:0; display:flex; gap:8px; align-items:center; flex-wrap:wrap; letter-spacing:-.01em; }
        .rv-sub{ color:var(--ad-muted); font-size:13.5px; margin-top:6px; display:flex; gap:16px; flex-wrap:wrap; }
        .rv-sub i{ color:var(--ad-faint); margin-right:5px; }
        .content-page .rv-sub a{ color:var(--ad-accent) !important; font-weight:600; }
        .rv-actions{ display:flex; gap:8px; flex-wrap:wrap; }
        .rv-stats{ display:grid; grid-template-columns:repeat(4, minmax(110px,1fr)) minmax(240px,2fr); gap:14px; margin-top:20px; }
        .rv-stat{ background:var(--ad-surface-2); border:1px solid var(--ad-line); border-radius:12px; padding:14px 16px; }
        .rv-stat b{ display:block; font-size:26px; font-weight:700; color:var(--ad-ink); line-height:1.1; letter-spacing:-.02em; }
        .rv-stat span{ font-size:12px; color:var(--ad-muted); font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
        .rv-stat.is-cgpa{ background:var(--ad-accent-soft); border-color:#cddcff; }
        .rv-stat.is-cgpa b{ color:var(--ad-accent); }
        .rv-stat.is-bad b{ color:var(--ad-red); }
        .rv-trend{ display:flex; align-items:flex-end; gap:8px; height:64px; margin-top:6px; }
        .rv-trend div{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%; min-width:0; }
        .rv-trend i{ display:block; width:100%; max-width:26px; background:var(--ad-accent); border-radius:4px 4px 1px 1px; min-height:3px; }
        .rv-trend i.is-zero{ background:var(--ad-red); opacity:.5; }
        .rv-trend small{ font-size:10px; color:var(--ad-muted); margin-top:3px; white-space:nowrap; }

        .rv-sem-head{ display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px; }
        .rv-sem-head h4{ font-size:16px; font-weight:700; margin:0; color:var(--ad-ink); display:flex; align-items:center; gap:8px; }
        .rv-sem-meta{ display:flex; gap:16px; font-size:13px; color:var(--ad-muted); flex-wrap:wrap; }
        .rv-sem-meta b{ color:var(--ad-ink); }
        .rv .table td.rv-subj{ white-space:normal; min-width:220px; }
        .rv .table td.rv-num, .rv .table th.rv-num{ text-align:center; }
        .rv-grade{ display:inline-block; min-width:34px; text-align:center; padding:3px 8px; border-radius:7px; font-weight:700; font-size:12.5px; }
        .g-top{ background:var(--ad-green-soft); color:var(--ad-green); }
        .g-good{ background:var(--ad-accent-soft); color:var(--ad-accent); }
        .g-mid{ background:var(--ad-amber-soft); color:var(--ad-amber); }
        .g-bad{ background:var(--ad-red-soft); color:var(--ad-red); }
        .g-none{ background:var(--ad-surface-2); color:var(--ad-muted); }
        .rv tr.is-backlog > td{ background:#fff8f8 !important; }
        .rv-cards{ display:flex; gap:16px; flex-wrap:wrap; }
        .rv-card{ width:150px; }
        .rv-card img{ width:150px; border-radius:8px; border:1px solid var(--ad-line); display:block; }
        .rv-card small{ display:block; color:var(--ad-muted); margin-top:6px; font-size:12px; }
        .rv-empty{ text-align:center; color:var(--ad-muted); padding:30px 10px; }
        #result-edit .subj-table input, #result-edit .subj-table select{ min-width:70px; }
        #result-edit .subj-table td:nth-child(2) input{ min-width:240px; }
        #result-edit .subj-table td:nth-child(1) input{ min-width:110px; }
        @media (max-width: 991px){ .rv-stats{ grid-template-columns:repeat(2, 1fr); } .rv-stats .rv-stat:last-child{ grid-column:1 / -1; } }
      </style>

      <div class="rv">
        <a href="{{ url('admin/results') }}" class="btn btn-secondary btn-sm" style="margin-bottom:16px;"><i class="fa fa-arrow-left"></i> All results</a>

        @if(Session::has('flash_message'))
          <div class="alert alert-info">{{ Session::get('flash_message') }}</div>
        @endif

        {{-- Summary --}}
        <div class="panel">
          <div class="rv-head">
            <div>
              <h3 class="rv-name">
                {{ $result->student_name ?: 'Unnamed student' }}
                @if($result->verified)<span class="badge badge-success"><i class="fa fa-check"></i> Verified</span>@else<span class="badge badge-warning">Not verified</span>@endif
                @if($result->locked)<span class="badge badge-secondary"><i class="fa fa-lock"></i> Locked</span>@endif
                @if($result->source)<span class="badge badge-info">{{ $result->source == 'jntuh' ? 'From JNTUH' : ucfirst($result->source) }}</span>@endif
              </h3>
              <div class="rv-sub">
                <span><i class="fa fa-id-card-o"></i>{{ $result->hall_ticket_no }}</span>
                @if($result->branch)<span><i class="fa fa-graduation-cap"></i>{{ $result->branch }}</span>@endif
                @if($result->regulation || $result->degree)<span><i class="fa fa-book"></i>{{ trim($result->degree.' '.$result->regulation) }}</span>@endif
                @if($university)<span><i class="fa fa-university"></i>{{ $university }}</span>@endif
                @if($linkedUser)
                  <span><i class="fa fa-user"></i><a href="{{ url('admin/users/history/'.$linkedUser->id) }}">{{ $linkedUser->name }}'s account</a></span>
                @else
                  <span class="text-muted"><i class="fa fa-user-times"></i>Not linked to an account</span>
                @endif
              </div>
            </div>
            <div class="rv-actions">
              <button type="button" class="btn btn-primary" id="toggleEdit"><i class="fa fa-pencil"></i> Edit result</button>
              @if(!$result->verified)
                <a href="{{ url('admin/results/verify/'.$result->id) }}" class="btn btn-success"><i class="fa fa-check"></i> Verify &amp; lock</a>
              @else
                <a href="{{ url('admin/results/unverify/'.$result->id) }}" class="btn btn-secondary"><i class="fa fa-unlock"></i> Un-verify</a>
              @endif
              @if(config('jntuh.enabled'))
                <a href="{{ url('admin/results/jntuh-refetch/'.$result->id) }}" class="btn btn-secondary" onclick="return confirm('Re-fetch this hall ticket from JNTUH? Unlocked semesters will be replaced.');"><i class="fa fa-cloud-download"></i> Re-fetch</a>
              @endif
              <a href="{{ url('admin/results/regenerate/'.$result->id) }}" class="btn btn-secondary"><i class="fa fa-id-card"></i> {{ $latest_card ? 'Regenerate card' : 'Generate card' }}</a>
              <a href="{{ url('admin/results/delete/'.$result->id) }}" class="btn btn-secondary rv-delete" title="Delete result"><i class="fa fa-trash-o"></i></a>
            </div>
          </div>

          <div class="rv-stats">
            <div class="rv-stat is-cgpa"><b>{{ $result->current_cgpa !== null && $result->current_cgpa !== '' ? number_format((float) $result->current_cgpa, 2) : '—' }}</b><span>CGPA</span></div>
            <div class="rv-stat"><b>{{ $result->total_credits !== null && $result->total_credits !== '' ? (0 + $result->total_credits) : '—' }}</b><span>Credits</span></div>
            <div class="rv-stat {{ (int) $result->backlogs_count > 0 ? 'is-bad' : '' }}"><b>{{ (int) $result->backlogs_count }}</b><span>Backlogs</span></div>
            <div class="rv-stat"><b>{{ $sem_count }}</b><span>Semesters</span></div>
            <div class="rv-stat">
              <span>SGPA by semester</span>
              @if($sem_count)
                <div class="rv-trend">
                  @foreach($sems as $sem)
                    @php $sg = (float) $sem->sgpa; @endphp
                    <div title="{{ $sem->sem_code }}: {{ $sem->sgpa !== null ? $sem->sgpa : '—' }}">
                      <i class="{{ $sg > 0 ? '' : 'is-zero' }}" style="height:{{ max(3, round($sg / $sgpa_max * 46)) }}px"></i>
                      <small>{{ $sem->sem_code }}</small>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-muted" style="margin-top:10px;font-size:13px;">No semesters yet</div>
              @endif
            </div>
          </div>
        </div>

        {{-- Read view --}}
        <div id="result-read">
          @forelse($sems as $sem)
            <div class="panel">
              <div class="rv-sem-head">
                <h4>Semester {{ $sem->sem_code }}
                  @if((int) $sem->locked === 1)<span class="badge badge-success"><i class="fa fa-lock"></i> Verified</span>@endif
                </h4>
                <div class="rv-sem-meta">
                  <span>SGPA <b>{{ $sem->sgpa !== null && $sem->sgpa !== '' ? $sem->sgpa : '—' }}</b></span>
                  <span>Credits <b>{{ $sem->credits_earned !== null && $sem->credits_earned !== '' ? (0 + $sem->credits_earned) : '—' }}</b></span>
                  @if($sem->exam_month_year)<span>Exam <b>{{ $sem->exam_month_year }}</b></span>@endif
                  @if($sem->id)
                    @if((int) $sem->locked === 1)
                      <a href="{{ url('admin/results/unverify-sem/'.$result->id.'/'.$sem->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-unlock"></i> Unlock</a>
                    @else
                      <a href="{{ url('admin/results/verify-sem/'.$result->id.'/'.$sem->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-check"></i> Verify &amp; lock</a>
                    @endif
                  @endif
                </div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead><tr>
                    <th>Code</th><th>Subject</th>
                    <th class="rv-num">Int</th><th class="rv-num">Ext</th><th class="rv-num">Total</th>
                    <th class="rv-num">Credits</th><th class="rv-num">Grade</th><th class="rv-num">Points</th><th>Status</th>
                  </tr></thead>
                  <tbody>
                    @forelse($sem->loaded_subjects as $sub)
                      <tr class="{{ $sub->is_backlog ? 'is-backlog' : '' }}">
                        <td><b>{{ $sub->subject_code ?: '—' }}</b></td>
                        <td class="rv-subj">{{ $sub->subject_name ?: '—' }}</td>
                        <td class="rv-num">{{ $sub->internal ?? '—' }}</td>
                        <td class="rv-num">{{ $sub->external ?? '—' }}</td>
                        <td class="rv-num"><b>{{ $sub->total ?? '—' }}</b></td>
                        <td class="rv-num">{{ $sub->credits !== null ? (0 + $sub->credits) : '—' }}</td>
                        <td class="rv-num"><span class="rv-grade {{ $grade_tone($sub->grade) }}">{{ $sub->grade ?: '—' }}</span></td>
                        <td class="rv-num">{{ $sub->grade_points !== null && $sub->grade_points !== '' ? (0 + $sub->grade_points) : '—' }}</td>
                        <td>@if($sub->is_backlog)<span class="badge badge-danger">Backlog</span>@else<span class="badge badge-success">Pass</span>@endif</td>
                      </tr>
                    @empty
                      <tr><td colspan="9">No subjects entered for this semester.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          @empty
            <div class="panel rv-empty"><i class="fa fa-graduation-cap" style="font-size:24px;display:block;margin-bottom:8px;color:var(--ad-faint)"></i>No semesters yet. Use <b>Edit result</b> to add them, or <b>Re-fetch</b> from JNTUH.</div>
          @endforelse

          {{-- Report cards --}}
          <div class="panel">
            <div class="rv-sem-head">
              <h4>Report cards</h4>
              <a href="{{ url('admin/results/regenerate/'.$result->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-id-card"></i> {{ $latest_card ? 'Regenerate' : 'Generate' }}</a>
            </div>
            @if($cards->count())
              <div class="rv-cards">
                @foreach($cards->take(6) as $c)
                  <div class="rv-card">
                    <a href="{{ $c->pdf_url }}" target="_blank" rel="noopener"><img src="{{ $c->pdf_url }}" alt="Report card" loading="lazy"></a>
                    <small>{{ $c->generated_at ? \Carbon\Carbon::parse($c->generated_at)->format('d M Y, h:i A') : '' }} @if($c->verified_at_generation)<span class="badge badge-success">Verified</span>@endif</small>
                    <a href="{{ $c->pdf_url }}" target="_blank" rel="noopener" download style="font-size:12.5px;font-weight:600;"><i class="fa fa-download"></i> Download</a>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-muted" style="font-size:13.5px;">No card yet. Generate one to share the result as an image.</div>
            @endif
          </div>
        </div>

        {{-- Edit mode: the full form, opened with "Edit result" --}}
        <div id="result-edit" style="display:none;">
          {!! Form::open(array('url' => 'admin/results/update/'.$result->id,'method'=>'post','id'=>'result-form')) !!}
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
              <div class="col-md-3">
                <select name="regulation" class="form-control">
                  @foreach(['R22','R18','R16','R13'] as $reg)
                    <option value="{{ $reg }}" {{ $result->regulation==$reg?'selected':'' }}>{{ $reg }}</option>
                  @endforeach
                </select>
              </div>
              <label class="col-md-2 col-form-label">Degree</label>
              <div class="col-md-4"><input type="text" name="degree" value="{{ $result->degree }}" class="form-control" placeholder="B.Tech / B.Pharm"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Branch</label>
              <div class="col-md-9"><input type="text" name="branch" value="{{ $result->branch }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Current CGPA</label>
              <div class="col-md-3"><input type="text" name="current_cgpa" id="f_cgpa" value="{{ $result->current_cgpa }}" class="form-control"></div>
              <label class="col-md-2 col-form-label">Total Credits</label>
              <div class="col-md-4"><input type="text" name="total_credits" id="f_credits" value="{{ $result->total_credits }}" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Backlogs</label>
              <div class="col-md-3"><input type="number" name="backlogs_count" value="{{ $result->backlogs_count }}" class="form-control"></div>
              <div class="col-md-6 text-muted" style="padding-top:8px"><small>CGPA &amp; credits auto-fill from subjects — override if needed.</small></div>
            </div>
          </div>

          <div id="semesters-wrap">
            @foreach($sems as $si => $sem)
              <div class="card-box sem-block" data-idx="{{ $si }}">
                <div class="d-flex justify-content-between align-items-center m-b-15">
                  <h5 class="text-primary m-0">
                    Semester {{ $sem->sem_code }}
                    @if((int)$sem->locked === 1)
                      <span class="badge badge-success"><i class="fa fa-lock"></i> Verified &amp; Locked</span>
                    @endif
                  </h5>
                  <div>
                    @if($sem->id)
                      @if((int)$sem->locked === 1)
                        <a href="{{ url('admin/results/unverify-sem/'.$result->id.'/'.$sem->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-unlock"></i> Unlock</a>
                      @else
                        <a href="{{ url('admin/results/verify-sem/'.$result->id.'/'.$sem->id) }}" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Verify &amp; Lock</a>
                      @endif
                    @endif
                    <button type="button" class="btn btn-sm btn-danger remove-sem" @if((int)$sem->locked === 1) disabled title="Unlock to remove" @endif><i class="fa fa-remove"></i> Remove</button>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-3"><input type="text" name="semesters[{{ $si }}][sem_code]" value="{{ $sem->sem_code }}" class="form-control" placeholder="Sem code"></div>
                  <div class="col-md-3">
                    <div class="input-group">
                      <input type="text" name="semesters[{{ $si }}][sgpa]" value="{{ $sem->sgpa }}" class="sem-sgpa form-control" placeholder="SGPA (auto)" data-touched="1">
                      <div class="input-group-append"><span class="input-group-text">SGPA</span></div>
                    </div>
                  </div>
                  <div class="col-md-3"><input type="text" name="semesters[{{ $si }}][credits_earned]" value="{{ $sem->credits_earned }}" class="sem-credits form-control" placeholder="Credits" data-touched="1"></div>
                  <div class="col-md-3"><input type="text" name="semesters[{{ $si }}][exam_month_year]" value="{{ $sem->exam_month_year }}" class="form-control" placeholder="Exam e.g. Nov 2024"></div>
                </div>
                <table class="table table-sm table-bordered subj-table">
                  <thead><tr>
                    <th style="width:100px">Code</th><th>Subject</th>
                    <th style="width:70px">Credits</th><th style="width:90px">Grade</th>
                    <th style="width:90px">Points</th><th style="width:60px" class="text-center">Backlog</th>
                    <th class="text-center opt-col" style="width:70px;display:none">Int</th>
                    <th class="text-center opt-col" style="width:70px;display:none">Ext</th>
                    <th class="text-center opt-col" style="width:70px;display:none">Total</th>
                    <th style="width:40px"></th>
                  </tr></thead>
                  <tbody class="subj-body">
                    @foreach($sem->loaded_subjects as $sj => $sub)
                    <tr class="subj-row">
                      <td><input type="text" name="subjects[{{ $si }}][{{ $sj }}][subject_code]" value="{{ $sub->subject_code }}" class="form-control form-control-sm"></td>
                      <td><input type="text" name="subjects[{{ $si }}][{{ $sj }}][subject_name]" value="{{ $sub->subject_name }}" class="form-control form-control-sm"></td>
                      <td><input type="number" step="0.5" name="subjects[{{ $si }}][{{ $sj }}][credits]" value="{{ $sub->credits }}" class="subj-credits form-control form-control-sm"></td>
                      <td>
                        <select name="subjects[{{ $si }}][{{ $sj }}][grade]" class="subj-grade form-control form-control-sm">
                          <option value="">—</option>
                          @foreach($GRADES as $g)<option value="{{ $g }}" {{ strtoupper($sub->grade)==strtoupper($g)?'selected':'' }}>{{ $g }}</option>@endforeach
                        </select>
                      </td>
                      <td><input type="number" step="0.01" name="subjects[{{ $si }}][{{ $sj }}][grade_points]" value="{{ $sub->grade_points }}" class="subj-points form-control form-control-sm" data-touched="1"></td>
                      <td class="text-center"><input type="checkbox" name="subjects[{{ $si }}][{{ $sj }}][is_backlog]" value="1" class="subj-backlog" {{ $sub->is_backlog?'checked':'' }}></td>
                      <td class="opt-col" style="display:none"><input type="number" name="subjects[{{ $si }}][{{ $sj }}][internal]" value="{{ $sub->internal }}" class="form-control form-control-sm"></td>
                      <td class="opt-col" style="display:none"><input type="number" name="subjects[{{ $si }}][{{ $sj }}][external]" value="{{ $sub->external }}" class="form-control form-control-sm"></td>
                      <td class="opt-col" style="display:none"><input type="number" name="subjects[{{ $si }}][{{ $sj }}][total]" value="{{ $sub->total }}" class="form-control form-control-sm"></td>
                      <td><button type="button" class="btn btn-sm btn-danger remove-subj"><i class="fa fa-remove"></i></button></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-secondary add-subj"><i class="fa fa-plus"></i> Add Subject</button>
                <button type="button" class="btn btn-sm btn-link toggle-opt">show/hide internal-external</button>
              </div>
            @endforeach
          </div>

          <div class="card-box">
            <button type="button" class="btn btn-secondary" id="add-sem"><i class="fa fa-plus"></i> Add Semester</button>
            <button type="submit" class="btn btn-primary float-right"><i class="fa fa-save"></i> Save Changes</button>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var read = document.getElementById('result-read'), editBox = document.getElementById('result-edit'), btn = document.getElementById('toggleEdit');
  function show(editing) {
    read.style.display = editing ? 'none' : '';
    editBox.style.display = editing ? '' : 'none';
    btn.innerHTML = editing ? '<i class="fa fa-eye"></i> Back to view' : '<i class="fa fa-pencil"></i> Edit result';
    if (editing) { history.replaceState(null, '', '#edit'); } else { history.replaceState(null, '', window.location.pathname + window.location.search); }
  }
  btn.addEventListener('click', function () { show(editBox.style.display === 'none'); });
  if (window.location.hash === '#edit') { show(true); }
  document.querySelectorAll('.rv-delete').forEach(function (a) {
    a.addEventListener('click', function (e) {
      if (!confirm('Delete this result and all its semesters? This cannot be undone.')) { e.preventDefault(); }
    });
  });
});
</script>

{{-- templates for new rows --}}
<script type="text/template" id="sem-template">
  <div class="card-box sem-block" data-idx="__I__">
    <div class="d-flex justify-content-between align-items-center m-b-15">
      <h5 class="text-primary m-0">New Semester</h5>
      <button type="button" class="btn btn-sm btn-danger remove-sem"><i class="fa fa-remove"></i> Remove</button>
    </div>
    <div class="form-group row">
      <div class="col-md-3"><input type="text" name="semesters[__I__][sem_code]" class="form-control" placeholder="Sem code e.g. 2-1"></div>
      <div class="col-md-3"><div class="input-group"><input type="text" name="semesters[__I__][sgpa]" class="sem-sgpa form-control" placeholder="SGPA (auto)"><div class="input-group-append"><span class="input-group-text">SGPA</span></div></div></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][credits_earned]" class="sem-credits form-control" placeholder="Credits (auto)"></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][exam_month_year]" class="form-control" placeholder="Exam e.g. Nov 2024"></div>
    </div>
    <table class="table table-sm table-bordered subj-table">
      <thead><tr>
        <th style="width:100px">Code</th><th>Subject</th>
        <th style="width:70px">Credits</th><th style="width:90px">Grade</th>
        <th style="width:90px">Points</th><th style="width:60px" class="text-center">Backlog</th>
        <th class="opt-col" style="display:none">Int</th><th class="opt-col" style="display:none">Ext</th><th class="opt-col" style="display:none">Total</th>
        <th style="width:40px"></th>
      </tr></thead>
      <tbody class="subj-body"></tbody>
    </table>
    <button type="button" class="btn btn-sm btn-secondary add-subj"><i class="fa fa-plus"></i> Add Subject</button>
    <button type="button" class="btn btn-sm btn-link toggle-opt">show/hide internal-external</button>
  </div>
</script>

<script type="text/template" id="subj-template">
  <tr class="subj-row">
    <td><input type="text" name="subjects[__I__][__J__][subject_code]" class="form-control form-control-sm"></td>
    <td><input type="text" name="subjects[__I__][__J__][subject_name]" class="form-control form-control-sm"></td>
    <td><input type="number" step="0.5" name="subjects[__I__][__J__][credits]" class="subj-credits form-control form-control-sm"></td>
    <td><select name="subjects[__I__][__J__][grade]" class="subj-grade form-control form-control-sm"><option value="">—</option>@foreach($GRADES as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach</select></td>
    <td><input type="number" step="0.01" name="subjects[__I__][__J__][grade_points]" class="subj-points form-control form-control-sm" placeholder="auto"></td>
    <td class="text-center"><input type="checkbox" name="subjects[__I__][__J__][is_backlog]" value="1" class="subj-backlog"></td>
    <td class="opt-col" style="display:none"><input type="number" name="subjects[__I__][__J__][internal]" class="form-control form-control-sm"></td>
    <td class="opt-col" style="display:none"><input type="number" name="subjects[__I__][__J__][external]" class="form-control form-control-sm"></td>
    <td class="opt-col" style="display:none"><input type="number" name="subjects[__I__][__J__][total]" class="form-control form-control-sm"></td>
    <td><button type="button" class="btn btn-sm btn-danger remove-subj"><i class="fa fa-remove"></i></button></td>
  </tr>
</script>

<script>
(function(){
  var GP = {'O':10,'A+':9,'A':8,'B+':7,'B':6,'C':5,'F':0,'AB':0};
  // start semester index above existing count to avoid collisions
  var semIdx = {{ count($sems) }};
  var subjIdx = {};

  function pointsFor(grade, credits){
    var g=(grade||'').toUpperCase();
    if(!(g in GP)||credits===''||credits==null) return '';
    var c=parseFloat(credits); if(isNaN(c)) return '';
    return +(c*GP[g]).toFixed(2);
  }
  function recalcOverall(){
    var tGp=0,tCr=0;
    document.querySelectorAll('.sem-block').forEach(function(b){
      b.querySelectorAll('.subj-row').forEach(function(r){
        var cr=parseFloat(r.querySelector('.subj-credits').value);
        var pts=parseFloat(r.querySelector('.subj-points').value);
        if(!isNaN(cr)&&cr>0){ tCr+=cr; if(!isNaN(pts)) tGp+=pts; }
      });
    });
    var cg=document.getElementById('f_cgpa'), cr=document.getElementById('f_credits');
    if(cg&&!cg.dataset.touched) cg.value=tCr>0?(tGp/tCr).toFixed(2):'';
    if(cr&&!cr.dataset.touched) cr.value=tCr>0?tCr:'';
  }
  function recalcSem(block){
    var sGp=0,sCr=0;
    block.querySelectorAll('.subj-row').forEach(function(r){
      var cr=parseFloat(r.querySelector('.subj-credits').value);
      var pts=parseFloat(r.querySelector('.subj-points').value);
      if(!isNaN(cr)&&cr>0){ sCr+=cr; if(!isNaN(pts)) sGp+=pts; }
    });
    var sg=block.querySelector('.sem-sgpa'), sc=block.querySelector('.sem-credits');
    if(!sg.dataset.touched) sg.value=sCr>0?(sGp/sCr).toFixed(2):'';
    if(!sc.dataset.touched) sc.value=sCr>0?sCr:'';
    recalcOverall();
  }
  function addSubject(block,i){
    if(subjIdx[i]==null){
      // continue numbering after existing rows
      subjIdx[i]=block.querySelectorAll('.subj-row').length;
    }
    var j=subjIdx[i]++;
    var html=document.getElementById('subj-template').innerHTML.replace(/__I__/g,i).replace(/__J__/g,j);
    var tb=document.createElement('tbody'); tb.innerHTML=html.trim();
    block.querySelector('.subj-body').appendChild(tb.firstElementChild);
  }
  function addSemester(){
    var i=semIdx++;
    var html=document.getElementById('sem-template').innerHTML.replace(/__I__/g,i);
    var wrap=document.createElement('div'); wrap.innerHTML=html;
    var block=wrap.firstElementChild;
    document.getElementById('semesters-wrap').appendChild(block);
    subjIdx[i]=0; addSubject(block,i);
  }

  document.getElementById('add-sem').addEventListener('click', addSemester);
  ['f_cgpa','f_credits'].forEach(function(id){var el=document.getElementById(id); if(el) el.addEventListener('input',function(){el.dataset.touched='1';});});

  var wrap=document.getElementById('semesters-wrap');
  wrap.addEventListener('click', function(e){
    var block=e.target.closest('.sem-block');
    if(e.target.closest('.add-subj')){ addSubject(block, +block.getAttribute('data-idx')); }
    else if(e.target.closest('.remove-subj')){ e.target.closest('.subj-row').remove(); recalcSem(block); }
    else if(e.target.closest('.remove-sem')){ block.remove(); recalcOverall(); }
    else if(e.target.closest('.toggle-opt')){ block.querySelectorAll('.opt-col').forEach(function(c){ c.style.display=(c.style.display==='none'?'':'none'); }); }
  });
  wrap.addEventListener('input', function(e){
    var block=e.target.closest('.sem-block'); if(!block) return;
    var row=e.target.closest('.subj-row');
    if(row && (e.target.classList.contains('subj-grade')||e.target.classList.contains('subj-credits'))){
      var g=row.querySelector('.subj-grade').value, c=row.querySelector('.subj-credits').value;
      var pe=row.querySelector('.subj-points');
      if(!pe.dataset.touched){ pe.value=pointsFor(g,c); }
      if((g||'').toUpperCase()==='F'){ row.querySelector('.subj-backlog').checked=true; }
    }
    if(row && e.target.classList.contains('subj-points')) e.target.dataset.touched='1';
    if(e.target.classList.contains('sem-sgpa')||e.target.classList.contains('sem-credits')) e.target.dataset.touched='1';
    recalcSem(block);
  });
})();
</script>

@endsection
