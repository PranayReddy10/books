@extends("admin.admin_app")

@section("content")

@php
  $GRADES = ['O','A+','A','B+','B','C','F','Ab'];
@endphp

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">

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

        {{-- Actions sidebar --}}
        <div class="col-md-4">
          <div class="card-box">
            <h4 class="header-title m-b-20">Actions</h4>
            @if(!$result->verified)
              <a href="{{ url('admin/results/verify/'.$result->id) }}" class="btn btn-success btn-block m-b-10"><i class="fa fa-check"></i> Verify &amp; Lock</a>
            @else
              <a href="{{ url('admin/results/unverify/'.$result->id) }}" class="btn btn-warning btn-block m-b-10"><i class="fa fa-undo"></i> Un-verify &amp; Unlock</a>
            @endif
            <a href="{{ url('admin/results/regenerate/'.$result->id) }}" class="btn btn-info btn-block m-b-10"><i class="fa fa-refresh"></i> Regenerate Card</a>
            <a href="{{ url('admin/results/delete/'.$result->id) }}" class="btn btn-danger btn-block m-b-10 data_remove"><i class="fa fa-trash"></i> Delete</a>
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
