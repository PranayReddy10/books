@extends("admin.admin_app")

@section("content")

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
              <div class="col-md-3"><input type="text" name="current_cgpa" class="form-control"></div>
              <label class="col-md-2 col-form-label">Total Credits</label>
              <div class="col-md-4"><input type="text" name="total_credits" class="form-control"></div>
            </div>
            <div class="form-group row">
              <label class="col-md-3 col-form-label">Pending Backlogs</label>
              <div class="col-md-3"><input type="number" name="backlogs_count" value="0" class="form-control"></div>
            </div>
          </div>

          {{-- Dynamic semesters --}}
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

{{-- Row templates --}}
<script type="text/template" id="sem-template">
  <div class="card-box sem-block" data-idx="__I__">
    <div class="d-flex justify-content-between align-items-center m-b-15">
      <h5 class="text-primary m-0">Semester #<span class="sem-num"></span></h5>
      <button type="button" class="btn btn-sm btn-danger remove-sem"><i class="fa fa-remove"></i> Remove</button>
    </div>
    <div class="form-group row">
      <div class="col-md-3"><input type="text" name="semesters[__I__][sem_code]" class="form-control" placeholder="Sem code e.g. 2-1"></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][sgpa]" class="form-control" placeholder="SGPA"></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][credits_earned]" class="form-control" placeholder="Credits earned"></div>
      <div class="col-md-3"><input type="text" name="semesters[__I__][exam_month_year]" class="form-control" placeholder="Exam e.g. Nov 2024"></div>
    </div>
    <table class="table table-sm table-bordered subj-table">
      <thead><tr><th>Code</th><th>Subject</th><th>Int</th><th>Ext</th><th>Total</th><th>Grade</th><th>Cr</th><th>Backlog</th><th></th></tr></thead>
      <tbody class="subj-body"></tbody>
    </table>
    <button type="button" class="btn btn-sm btn-secondary add-subj"><i class="fa fa-plus"></i> Add Subject</button>
  </div>
</script>

<script type="text/template" id="subj-template">
  <tr class="subj-row">
    <td><input type="text" name="subjects[__I__][__J__][subject_code]" class="form-control form-control-sm"></td>
    <td><input type="text" name="subjects[__I__][__J__][subject_name]" class="form-control form-control-sm"></td>
    <td style="width:70px"><input type="number" name="subjects[__I__][__J__][internal]" class="form-control form-control-sm"></td>
    <td style="width:70px"><input type="number" name="subjects[__I__][__J__][external]" class="form-control form-control-sm"></td>
    <td style="width:70px"><input type="number" name="subjects[__I__][__J__][total]" class="form-control form-control-sm"></td>
    <td style="width:70px"><input type="text" name="subjects[__I__][__J__][grade]" class="form-control form-control-sm"></td>
    <td style="width:60px"><input type="text" name="subjects[__I__][__J__][credits]" class="form-control form-control-sm"></td>
    <td style="width:70px" class="text-center"><input type="checkbox" name="subjects[__I__][__J__][is_backlog]" value="1"></td>
    <td style="width:40px"><button type="button" class="btn btn-sm btn-danger remove-subj"><i class="fa fa-remove"></i></button></td>
  </tr>
</script>

<script>
(function(){
  var semIdx = 0;
  var subjIdx = {}; // per-semester subject counter

  function addSemester(){
    var i = semIdx++;
    var html = document.getElementById('sem-template').innerHTML.replace(/__I__/g, i);
    var wrap = document.createElement('div');
    wrap.innerHTML = html;
    var block = wrap.firstElementChild;
    block.querySelector('.sem-num').textContent = (i + 1);
    document.getElementById('semesters-wrap').appendChild(block);
    subjIdx[i] = 0;
    addSubject(block, i); // start with one subject row
  }

  function addSubject(block, i){
    var j = subjIdx[i]++;
    var html = document.getElementById('subj-template').innerHTML
                 .replace(/__I__/g, i).replace(/__J__/g, j);
    var tmp = document.createElement('tbody');
    tmp.innerHTML = html.trim();
    block.querySelector('.subj-body').appendChild(tmp.firstElementChild);
  }

  document.getElementById('add-sem').addEventListener('click', addSemester);

  document.getElementById('semesters-wrap').addEventListener('click', function(e){
    var block = e.target.closest('.sem-block');
    if (e.target.closest('.add-subj')) {
      var i = parseInt(block.getAttribute('data-idx'), 10);
      addSubject(block, i);
    } else if (e.target.closest('.remove-subj')) {
      e.target.closest('.subj-row').remove();
    } else if (e.target.closest('.remove-sem')) {
      block.remove();
    }
  });

  // Start with one semester so the form isn't empty.
  addSemester();
})();
</script>

@endsection
