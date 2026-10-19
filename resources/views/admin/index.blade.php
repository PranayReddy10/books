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

  @include("admin.partials.auth_style")
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
    showConfirmButton:true,confirmButtonColor:'#2563eb'
  });
@endif
  </script>
</body>
</html>