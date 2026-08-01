@extends('public.layout')

@section('meta_title', 'Create your free account — JNTU Books')
@section('meta_description', 'Join JNTU Books free to read engineering eBooks and notes.')

@section('head')
<style>
  .auth-wrap{min-height:calc(100vh - 66px);display:grid;grid-template-columns:.9fr 1.1fr;align-items:stretch;position:relative;overflow:hidden}
  .auth-left{padding:50px;position:relative;overflow:hidden;display:flex;flex-direction:column;justify-content:center;background:linear-gradient(160deg,#efeaff,#f6f5ff)}
  .auth-left:before{content:'';position:absolute;inset:0;background:
     radial-gradient(55% 55% at 20% 20%, rgba(139,108,255,.22), transparent 60%),
     radial-gradient(45% 45% at 85% 80%, rgba(255,194,75,.18), transparent 60%);pointer-events:none}
  .auth-left .eyebrow{font-weight:600;color:var(--violet);letter-spacing:.08em;text-transform:uppercase;font-size:13px;margin-bottom:14px}
  .auth-left h1{font-size:clamp(28px,3.4vw,42px);line-height:1.06;font-weight:800}
  .auth-left h1 .hl{background:linear-gradient(120deg,var(--violet),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
  .auth-left p{color:var(--muted);font-size:16px;margin-top:16px;max-width:400px}
  .stage{perspective:1100px;height:260px;position:relative;margin-top:26px}
  .float{position:absolute;border-radius:14px;overflow:hidden;box-shadow:0 26px 50px rgba(20,16,60,.26);border:3px solid #fff;background:#e3ddfb;animation:bob 6s ease-in-out infinite}
  .f1{width:130px;height:174px;left:4%;top:24px;transform:rotateY(16deg) rotateX(6deg)}
  .f2{width:150px;height:196px;left:36%;top:6px;transform:rotateY(-10deg) rotateX(6deg);animation-delay:.6s}
  .f3{width:124px;height:164px;right:8%;top:50px;transform:rotateY(-22deg) rotateX(6deg);animation-delay:1.1s}
  @keyframes bob{0%,100%{margin-top:0}50%{margin-top:-14px}}

  .auth-card-col{padding:40px;display:flex;justify-content:center;align-items:center}
  .auth-card{background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 30px 60px rgba(20,16,60,.10);padding:32px;width:100%;max-width:560px}
  .auth-card h2{font-family:'Sora';font-size:24px}
  .auth-card .sub{color:var(--muted);font-size:14px;margin:6px 0 20px}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  .field{margin-bottom:14px}
  .field.full{grid-column:1 / -1}
  .field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
  .req{color:#e05353}
  .field input,.field select{width:100%;padding:11px 13px;border-radius:11px;border:1px solid var(--line);font-size:15px;background:var(--paper)}
  .field input:focus,.field select:focus{outline:none;border-color:var(--violet);background:#fff}
  .btn-block{width:100%;justify-content:center;padding:13px;margin-top:6px}
  .alt{text-align:center;margin-top:16px;font-size:14px;color:var(--muted)}
  .alt a{color:var(--violet);font-weight:600}
  .err{background:#fff0f2;border:1px solid #ffd6dd;color:#c0304a;padding:12px 14px;border-radius:10px;font-size:14px;margin-bottom:16px}
  .err ul{margin:0;padding-left:18px}
  @media(max-width:900px){.auth-wrap{grid-template-columns:1fr}.auth-left{display:none}.grid2{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="auth-wrap">
  <div class="auth-left">
    <div class="eyebrow">Join free</div>
    <h1>Your study library, <span class="hl">unlocked</span>.</h1>
    <p>Create a free account to read engineering eBooks and notes, and join the student feed.</p>
    <div class="stage">
      <div class="float f1"></div>
      <div class="float f2"></div>
      <div class="float f3"></div>
    </div>
  </div>

  <div class="auth-card-col">
    <div class="auth-card">
      <h2>Create your account</h2>
      <div class="sub">Already a member? <a href="{{ route('login') }}">Sign in</a></div>

      @if($errors->any())
        <div class="err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="grid2">
          <div class="field">
            <label>Full name <span class="req">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required>
          </div>
          <div class="field">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required>
          </div>
          <div class="field">
            <label>Password <span class="req">*</span></label>
            <input type="password" name="password" required>
          </div>
          <div class="field">
            <label>Confirm password <span class="req">*</span></label>
            <input type="password" name="password_confirmation" required>
          </div>
          <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}">
          </div>
          <div class="field">
            <label>Roll number <span class="req">*</span></label>
            <input type="text" name="rollnumber" value="{{ old('rollnumber') }}" required>
          </div>
          <div class="field">
            <label>University <span class="req">*</span></label>
            <select name="university" id="university_select" required onchange="filterDepartments()">
              <option value="">Select University</option>
              @foreach ($university_list as $uni)
                <option value="{{ stripslashes($uni->university_name) }}" data-id="{{ $uni->id }}" @if(old('university') == $uni->university_name) selected @endif>{{ stripslashes($uni->university_name) }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Department <span class="req">*</span></label>
            <select name="department_id" id="department_select" required>
              <option value="">Select University first</option>
            </select>
          </div>
          <div class="field">
            <label>College <span class="req">*</span></label>
            <select name="college" required>
              <option value="">Select College</option>
              @foreach ($college_list as $college)
                <option value="{{ stripslashes($college->college_name) }}" @if(old('college') == $college->college_name) selected @endif>{{ stripslashes($college->college_name) }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Gender <span class="req">*</span></label>
            <select name="gender" required>
              <option value="">Select Gender</option>
              @foreach (['Male','Female','Other'] as $g)
                <option value="{{ $g }}" @if(old('gender') == $g) selected @endif>{{ $g }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create free account</button>
      </form>

      <div class="alt">Browsing is always free. Sign in only to read.</div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  var ALL_DEPARTMENTS = [
    @foreach($department_list as $d)
    { id: {{ $d->id }}, name: "{{ addslashes(stripslashes($d->department_name)) }}", uni: {{ $d->university_id ?: 'null' }} },
    @endforeach
  ];
  var OLD_DEPARTMENT = "{{ old('department_id') }}";

  function filterDepartments() {
    var uniSelect = document.getElementById('university_select');
    var uniId = uniSelect.options[uniSelect.selectedIndex] ? uniSelect.options[uniSelect.selectedIndex].getAttribute('data-id') : '';
    var dept = document.getElementById('department_select');
    dept.innerHTML = '';
    if (!uniId) { dept.innerHTML = '<option value="">Select University first</option>'; return; }
    var matches = ALL_DEPARTMENTS.filter(function(d){ return String(d.uni) === String(uniId); });
    if (matches.length === 0) { dept.innerHTML = '<option value="">No departments for this university</option>'; return; }
    dept.innerHTML = '<option value="">Select Department</option>';
    matches.forEach(function(d){
      var o = document.createElement('option');
      o.value = d.id; o.textContent = d.name;
      if (String(OLD_DEPARTMENT) === String(d.id)) o.selected = true;
      dept.appendChild(o);
    });
  }
  filterDepartments();
</script>
@endsection
