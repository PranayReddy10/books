@extends("admin.admin_app")

@section("content")

@php
  $GRADES = ['O','A+','A','B+','B','C','F','Ab'];
@endphp

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          {!! Form::open(array('url' => 'admin/results/store','method'=>'post','id'=>'result-form')) !!}

          <div class="card-box">
            <h4 class="header-title m-b-20">Add Result</h4>

            <div class="form-group row">
              <label class="col-md-3 col-form-label">Hall Ticket No <span class="text-danger">*</span></label>
              <div class="col-md-9"><input type="text" name="hall_ticket_no" class="form-control" required></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Student Name</label>
              <div class="col-md-9"><input type="text" name="student_name" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Link to App User <small class="text-muted">(optional)</small></label>
              <div class="col-md-9">
                <select name="user_id" class="form-control select2">
                  <option value="">— Not linked —</option>
                  @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">University</label>
              <div class="col-md-9">
                <select name="university_id" class="form-control select2">
                  <option value="">— Select —</option>
                  @foreach($universities as $uni)
                    <option value="{{ $uni->id }}">{{ $uni->university_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Regulation</label>
              <div class="col-md-3">
                <select name="regulation" class="form-control">
                  @foreach(['R22','R18','R16','R13'] as $reg)
                    <option value="{{ $reg }}">{{ $reg }}</option>
                  @endforeach
                </select>
              </div>
              <label class="col-md-2 col-form-label">Degree</label>
              <div class="col-md-4"><input type="text" name="degree" class="form-control" placeholder="B.Tech / B.Pharm"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Branch</label>
              <div class="col-md-9"><input type="text" name="branch" class="form-control" placeholder="CSE / ECE / MECH ..."></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Current CGPA</label>
              <div class="col-md-3"><input type="text" name="current_cgpa" id="f_cgpa" class="form-control"></div>
              <label class="col-md-2 col-form-label">Total Credits</label>
              <div class="col-md-4"><input type="text" name="total_credits" id="f_credits" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Pending Backlogs</label>
              <div class="col-md-3"><input type="number" name="backlogs_count" value="0" class="form-control"></div>
              <div class="col-md-6 text-muted" style="padding-top:8px">
                <small>CGPA &amp; total credits auto-fill from semesters — you can override.</small>
              </div>
            </div>
          </div>

          <div id="semesters-wrap"></div>

          <div class="card-box">
            <button type="button" class="btn btn-secondary" id="add-sem"><i class="fa fa-plus"></i> Add Semester</button>
            <button type="submit" class="btn btn-primary float-right"><i class="fa fa-save"></i> Save Result</button>
          </div>

          {!! Form::close() !!}

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ================= templates ================= --}}
<script type="text/template" id="sem-template">
  <div class="card-box sem-block" data-idx="__I__">
    <div class="d-flex justify-content-between align-items-center m-b-15">
      <h5 class="text-primary m-0">Semester #<span class="sem-num"></span></h5>
      <button type="button" class="btn btn-sm btn-danger remove-sem"><i class="fa fa-remove"></i> Remove</button>
    </div>
    <div class="form-group row">
      <div class="col-md-3"><input type="text" name="semesters[__I__][sem_code]" class="form-control" placeholder="Sem code e.g. 2-1"></div>
      <div class="col-md-3">
        <div class="input-group">
          <input type="text" name="semesters[__I__][sgpa]" class="sem-sgpa form-control" placeholder="SGPA (auto)">
          <div class="input-group-append"><span class="input-group-text">SGPA</span></div>
        </div>
      </div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][credits_earned]" class="sem-credits form-control" placeholder="Credits (auto)"></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][exam_month_year]" class="form-control" placeholder="Exam e.g. Nov 2024"></div>
    </div>
    <table class="table table-sm table-bordered subj-table">
      <thead><tr>
        <th style="width:100px">Code</th><th>Subject</th>
        <th style="width:70px">Credits</th><th style="width:90px">Grade</th>
        <th style="width:90px">Points</th><th style="width:60px" class="text-center">Backlog</th>
        <th style="width:70px" class="text-center opt-col">Int</th>
        <th style="width:70px" class="text-center opt-col">Ext</th>
        <th style="width:70px" class="text-center opt-col">Total</th>
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
    <td>
      <select name="subjects[__I__][__J__][grade]" class="subj-grade form-control form-control-sm">
        <option value="">—</option>
        @foreach($GRADES as $g)<option value="{{ $g }}">{{ $g }}</option>@endforeach
      </select>
    </td>
    <td><input type="number" step="0.01" name="subjects[__I__][__J__][grade_points]" class="subj-points form-control form-control-sm" placeholder="auto"></td>
    <td class="text-center"><input type="checkbox" name="subjects[__I__][__J__][is_backlog]" value="1" class="subj-backlog"></td>
    <td class="opt-col"><input type="number" name="subjects[__I__][__J__][internal]" class="form-control form-control-sm"></td>
    <td class="opt-col"><input type="number" name="subjects[__I__][__J__][external]" class="form-control form-control-sm"></td>
    <td class="opt-col"><input type="number" name="subjects[__I__][__J__][total]" class="form-control form-control-sm"></td>
    <td><button type="button" class="btn btn-sm btn-danger remove-subj"><i class="fa fa-remove"></i></button></td>
  </tr>
</script>

<script>
(function(){
  var GP = {'O':10,'A+':9,'A':8,'B+':7,'B':6,'C':5,'F':0,'AB':0};
  var semIdx = 0, subjIdx = {};

  function pointsFor(grade, credits){
    var g = (grade||'').toUpperCase();
    if(!(g in GP) || credits === '' || credits == null) return '';
    var c = parseFloat(credits); if(isNaN(c)) return '';
    return +(c * GP[g]).toFixed(2);
  }

  function recalcSem(block){
    var sumGp = 0, sumCr = 0;
    block.querySelectorAll('.subj-row').forEach(function(r){
      var cr = parseFloat(r.querySelector('.subj-credits').value);
      var pts = parseFloat(r.querySelector('.subj-points').value);
      if(!isNaN(cr) && cr > 0){ sumCr += cr; if(!isNaN(pts)) sumGp += pts; }
    });
    var sgpaEl = block.querySelector('.sem-sgpa');
    var crEl = block.querySelector('.sem-credits');
    if(!sgpaEl.dataset.touched) sgpaEl.value = sumCr > 0 ? (sumGp/sumCr).toFixed(2) : '';
    if(!crEl.dataset.touched) crEl.value = sumCr > 0 ? sumCr : '';
    recalcOverall();
  }

  function recalcOverall(){
    var totalGp = 0, totalCr = 0;
    document.querySelectorAll('.sem-block').forEach(function(b){
      b.querySelectorAll('.subj-row').forEach(function(r){
        var cr = parseFloat(r.querySelector('.subj-credits').value);
        var pts = parseFloat(r.querySelector('.subj-points').value);
        if(!isNaN(cr) && cr>0){ totalCr += cr; if(!isNaN(pts)) totalGp += pts; }
      });
    });
    var cgpaEl = document.getElementById('f_cgpa');
    var credEl = document.getElementById('f_credits');
    if(cgpaEl && !cgpaEl.dataset.touched) cgpaEl.value = totalCr>0 ? (totalGp/totalCr).toFixed(2) : '';
    if(credEl && !credEl.dataset.touched) credEl.value = totalCr>0 ? totalCr : '';
  }

  function addSemester(){
    var i = semIdx++;
    var html = document.getElementById('sem-template').innerHTML.replace(/__I__/g, i);
    var wrap = document.createElement('div'); wrap.innerHTML = html;
    var block = wrap.firstElementChild;
    block.querySelector('.sem-num').textContent = (i+1);
    document.getElementById('semesters-wrap').appendChild(block);
    subjIdx[i] = 0; addSubject(block, i);
  }
  function addSubject(block, i){
    var j = subjIdx[i]++;
    var html = document.getElementById('subj-template').innerHTML.replace(/__I__/g, i).replace(/__J__/g, j);
    var tb = document.createElement('tbody'); tb.innerHTML = html.trim();
    block.querySelector('.subj-body').appendChild(tb.firstElementChild);
  }

  document.getElementById('add-sem').addEventListener('click', addSemester);

  // mark overridden fields so auto-calc won't stomp manual edits
  ['f_cgpa','f_credits'].forEach(function(id){
    var el = document.getElementById(id);
    el.addEventListener('input', function(){ el.dataset.touched = '1'; });
  });

  document.getElementById('semesters-wrap').addEventListener('click', function(e){
    var block = e.target.closest('.sem-block');
    if(e.target.closest('.add-subj')){ addSubject(block, +block.getAttribute('data-idx')); }
    else if(e.target.closest('.remove-subj')){ e.target.closest('.subj-row').remove(); recalcSem(block); }
    else if(e.target.closest('.remove-sem')){ block.remove(); recalcOverall(); }
    else if(e.target.closest('.toggle-opt')){ block.querySelectorAll('.opt-col').forEach(function(c){ c.style.display = (c.style.display==='none'?'':'none'); }); }
  });

  document.getElementById('semesters-wrap').addEventListener('input', function(e){
    var block = e.target.closest('.sem-block'); if(!block) return;
    var row = e.target.closest('.subj-row');
    if(row && (e.target.classList.contains('subj-grade') || e.target.classList.contains('subj-credits'))){
      var g = row.querySelector('.subj-grade').value;
      var c = row.querySelector('.subj-credits').value;
      var ptsEl = row.querySelector('.subj-points');
      if(!ptsEl.dataset.touched){ ptsEl.value = pointsFor(g, c); }
      // grade F -> mark backlog convenience
      if((g||'').toUpperCase()==='F'){ row.querySelector('.subj-backlog').checked = true; }
    }
    if(row && e.target.classList.contains('subj-points')){ e.target.dataset.touched='1'; }
    if(e.target.classList.contains('sem-sgpa') || e.target.classList.contains('sem-credits')){ e.target.dataset.touched='1'; }
    recalcSem(block);
  });

  addSemester(); // start with one
})();
</script>

@endsection
