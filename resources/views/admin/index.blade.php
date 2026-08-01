<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{getcong('app_name')}} Admin">
  <meta name="author" content="Viaviwebtech">

  @if(getcong('app_logo'))
  <link rel="shortcut icon" href="{{ URL::asset('/'.getcong('app_logo')) }}">
  @else
  <link rel="shortcut icon" href="{{ URL::asset('site_assets/images/favicon.png') }}">
  @endif
  <title>{{getcong('app_name')}} Admin</title>

  <link href="{{ URL::asset('admin_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('admin_assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('admin_assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
  <script src="{{ URL::asset('admin_assets/js/modernizr.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>

  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{--ink:#0e1030;--violet:#5b3df6;--violet-2:#8b6cff;--gold:#ffc24b;--line:#e7e6fb;--muted:#6b6f9c}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',system-ui,sans-serif;background:#eef0fb;color:var(--ink);min-height:100vh}
    .login-shell{display:grid;grid-template-columns:1.05fr .95fr;min-height:100vh}
    /* Left brand panel */
    .brand-panel{position:relative;overflow:hidden;background:linear-gradient(160deg,#171445,#0c0a2e);color:#fff;padding:56px;display:flex;flex-direction:column;justify-content:center}
    .brand-panel:before{content:'';position:absolute;inset:0;background:
      radial-gradient(50% 50% at 20% 20%, rgba(139,108,255,.35), transparent 60%),
      radial-gradient(45% 45% at 85% 80%, rgba(255,194,75,.22), transparent 60%);pointer-events:none}
    .brand-panel .logo{display:flex;align-items:center;gap:10px;font-family:'Sora';font-weight:800;font-size:22px;position:relative}
    .brand-panel .logo .dot{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,var(--violet),var(--gold));transform:rotate(8deg)}
    .brand-panel h1{font-family:'Sora';font-weight:800;font-size:clamp(28px,3.4vw,44px);line-height:1.08;margin-top:34px;position:relative}
    .brand-panel h1 .hl{background:linear-gradient(120deg,var(--violet-2),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
    .brand-panel p{color:#b9bce8;margin-top:16px;max-width:420px;position:relative;font-size:16px}
    .brand-panel .badges{display:flex;gap:10px;margin-top:28px;position:relative;flex-wrap:wrap}
    .brand-panel .badge{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:999px;padding:8px 14px;font-size:13px;font-weight:600}
    /* Right form panel */
    .form-panel{display:flex;align-items:center;justify-content:center;padding:40px}
    .login-card{background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 30px 60px rgba(20,16,60,.12);padding:38px;width:100%;max-width:410px}
    .login-card .kicker{color:var(--violet);font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-size:12px}
    .login-card h2{font-family:'Sora';font-size:26px;margin:6px 0 4px}
    .login-card .sub{color:var(--muted);font-size:14px;margin-bottom:24px}
    .fld{margin-bottom:16px}
    .fld label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
    .fld input[type=text],.fld input[type=password]{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--line);font-size:15px;background:#f6f5ff;transition:.15s}
    .fld input:focus{outline:none;border-color:var(--violet);background:#fff;box-shadow:0 0 0 4px rgba(91,61,246,.12)}
    .row-between{display:flex;align-items:center;justify-content:space-between;margin:6px 0 20px;font-size:13px}
    .row-between label{color:var(--muted);display:flex;align-items:center;gap:6px;margin:0}
    .row-between a{color:var(--violet);font-weight:600;text-decoration:none}
    .btn-login{width:100%;padding:13px;border:none;border-radius:12px;background:var(--violet);color:#fff;font-weight:700;font-size:15px;cursor:pointer;box-shadow:0 10px 24px rgba(91,61,246,.3);transition:.18s;font-family:'Sora'}
    .btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(91,61,246,.4)}
    .card-logo{max-width:150px;margin-bottom:20px}
    @media(max-width:880px){.login-shell{grid-template-columns:1fr}.brand-panel{display:none}}
  </style>
</head>

<body>
  <div class="login-shell">

    <div class="brand-panel">
      <div class="logo"><span class="dot"></span> {{ getcong('app_name') ?: 'JNTU Books' }}</div>
      <h1>Admin <span class="hl">control center</span>.</h1>
      <p>Manage books, posts, media, users and everything that powers the app and website — all in one place.</p>
      <div class="badges">
        <span class="badge"><i class="fa fa-book"></i> Books</span>
        <span class="badge"><i class="fa fa-photo"></i> Media & Posts</span>
        <span class="badge"><i class="fa fa-users"></i> Users</span>
        <span class="badge"><i class="fa fa-bell"></i> Notifications</span>
      </div>
    </div>

    <div class="form-panel">
      <div class="login-card">
        @if(getcong('admin_logo'))
          <img src="{{ URL::asset('/'.getcong('admin_logo')) }}" alt="Logo" class="card-logo">
        @endif
        <div class="kicker">Admin panel</div>
        <h2>Sign in</h2>
        <div class="sub">Enter your credentials to continue.</div>

        {!! Form::open(array('url' => 'admin/login','id'=>'loginform','role'=>'form')) !!}
          <div class="fld">
            <label>{{trans('words.email')}}</label>
            <input name="email" type="text" required placeholder="you@example.com">
          </div>
          <div class="fld">
            <label>{{trans('words.password')}}</label>
            <input name="password" type="password" required placeholder="••••••••">
          </div>
          <div class="row-between">
            <label><input id="checkbox-signup" type="checkbox" name="remember" value="remember"> Remember me</label>
            <a href="{{ URL::to('admin/forgot_password') }}"><i class="fa fa-lock"></i> {{trans('words.forgot_pass_text')}}</a>
          </div>
          <button class="btn-login" type="submit">{{trans('words.login_text')}}</button>
        {!! Form::close() !!}
      </div>
    </div>

  </div>

  <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/popper.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.core.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/jquery.app.js') }}"></script>

  <script type="text/javascript">
@if(Session::has('flash_message'))
  const Toast = Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:false});
  Toast.fire({icon:'success',title:'{{ Session::get('flash_message') }}'});
@endif

@if (count($errors) > 0)
  Swal.fire({
    icon:'error',title:'Oops...',
    html:'<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
    showConfirmButton:true,confirmButtonColor:'#5b3df6',background:"#1a2234",color:"#fff"
  });
@endif
  </script>
</body>
</html>