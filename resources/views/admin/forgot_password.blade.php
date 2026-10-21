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

  <!-- App css -->
  
  <link href="{{ URL::asset('admin_assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ URL::asset('admin_assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
  <script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
  @include("admin.partials.auth_style")
</head>

<body>
  <div class="login-shell">

    <div class="brand-panel">
      <div class="logo"><span class="dot"></span> {{ getcong('app_name') ?: 'JNTU Books' }}</div>
      <h1>Locked out? <span class="hl">We'll get you back in</span>.</h1>
      <p>Enter the email on your admin account and we'll send you a link to set a new password.</p>
    </div>

    <div class="form-panel">
      <div class="login-card">
        @if(getcong('admin_logo'))
          <img src="{{ URL::asset('/'.getcong('admin_logo')) }}" alt="Logo" class="card-logo">
        @endif
        <div class="kicker">Admin panel</div>
        <h2>{{trans('words.forgot_pass')}}</h2>
        <div class="sub">We'll email you a reset link.</div>

        {!! Form::open(array('url' => 'admin/forgot_password','id'=>'loginform','role'=>'form')) !!}
          <div class="fld">
            <label>{{trans('words.email')}}</label>
            <input name="email" type="text" required placeholder="you@example.com">
          </div>
          <button class="btn-login" type="submit">{{trans('words.reset_password')}}</button>
        {!! Form::close() !!}

        <a href="{{ URL::to('admin/login') }}" class="alt-link"><i class="fa fa-arrow-left"></i> Back to {{trans('words.login_text')}}</a>
      </div>
    </div>

  </div>

  <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/popper.min.js') }}"></script>
  <script src="{{ URL::asset('admin_assets/js/bootstrap.min.js') }}"></script>

  <script type="text/javascript">

@if(Session::has('flash_message'))     

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'success',
    title: '{{ Session::get('flash_message') }}'
  })     
  
@endif

@if(Session::has('error_flash_message'))     

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: false,
    /*didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }*/
  })

  Toast.fire({
    icon: 'error',
    title: '{{ Session::get('error_flash_message') }}'
  })     
  
@endif

@if (count($errors) > 0)
              
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
        showConfirmButton: true,
        confirmButtonColor: '#2563eb'
        }) 
@endif

</script>

</body>

</html>