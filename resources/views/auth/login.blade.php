@extends('public.layout')

@section('meta_title', 'Sign in — JNTU Books')
@section('meta_description', 'Sign in to read books on JNTU Books.')

@section('head')
<style>
  .auth-wrap{min-height:calc(100vh - 66px);display:grid;grid-template-columns:1.05fr .95fr;align-items:center;position:relative;overflow:hidden}
  .auth-wrap:before{content:'';position:absolute;inset:0;background:
     radial-gradient(55% 55% at 12% 15%, rgba(139,108,255,.20), transparent 60%),
     radial-gradient(45% 45% at 92% 20%, rgba(255,194,75,.16), transparent 60%);pointer-events:none}
  .auth-left{padding:50px}
  .auth-left .eyebrow{font-weight:600;color:var(--violet);letter-spacing:.08em;text-transform:uppercase;font-size:13px;margin-bottom:14px}
  .auth-left h1{font-size:clamp(30px,4vw,46px);line-height:1.05;font-weight:800}
  .auth-left h1 .hl{background:linear-gradient(120deg,var(--violet),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
  .auth-left p{color:var(--muted);font-size:17px;margin-top:16px;max-width:440px}
  .stage{perspective:1100px;height:340px;position:relative;margin-top:30px}
  .float{position:absolute;border-radius:14px;overflow:hidden;box-shadow:0 30px 60px rgba(20,16,60,.28);border:3px solid #fff;background:#eceafd;animation:bob 6s ease-in-out infinite}
  .f1{width:150px;height:200px;left:6%;top:30px;transform:rotateY(16deg) rotateX(6deg)}
  .f2{width:170px;height:220px;left:38%;top:10px;transform:rotateY(-10deg) rotateX(6deg);animation-delay:.6s}
  .f3{width:140px;height:186px;right:8%;top:60px;transform:rotateY(-22deg) rotateX(6deg);animation-delay:1.1s}
  @keyframes bob{0%,100%{margin-top:0}50%{margin-top:-14px}}

  .auth-card-col{padding:40px;display:flex;justify-content:center}
  .auth-card{background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 30px 60px rgba(20,16,60,.12);padding:34px;width:100%;max-width:400px}
  .auth-card h2{font-family:'Sora';font-size:24px}
  .auth-card .sub{color:var(--muted);font-size:14px;margin:6px 0 22px}
  .field{margin-bottom:16px}
  .field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
  .field input{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--line);font-size:15px;background:var(--paper)}
  .field input:focus{outline:none;border-color:var(--violet);background:#fff}
  .row-between{display:flex;align-items:center;justify-content:space-between;font-size:13px;margin-bottom:18px}
  .row-between label{color:var(--muted)}
  .btn-block{width:100%;justify-content:center;padding:13px}
  .alt{text-align:center;margin-top:18px;font-size:14px;color:var(--muted)}
  .alt a{color:var(--violet);font-weight:600}
  .err{background:#fff0f2;border:1px solid #ffd6dd;color:#c0304a;padding:10px 12px;border-radius:10px;font-size:14px;margin-bottom:16px}
  @media(max-width:860px){.auth-wrap{grid-template-columns:1fr}.auth-left{display:none}}
</style>
@endsection

@section('content')
<div class="auth-wrap">
  <div class="auth-left">
    <div class="eyebrow">Welcome back</div>
    <h1>Pick up where you <span class="hl">left off</span>.</h1>
    <p>Sign in to open the reader and keep studying. Your books and notes are waiting.</p>
    <div class="stage">
      <div class="float f1"></div>
      <div class="float f2"></div>
      <div class="float f3"></div>
    </div>
  </div>

  <div class="auth-card-col">
    <div class="auth-card">
      <h2>Sign in</h2>
      <div class="sub">New here? <a href="{{ route('register') }}" style="color:var(--violet);font-weight:600">Create a free account</a></div>

      @if($errors->any())
        <div class="err">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="field">
          <label>Email or username</label>
          <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
        </div>
        <div class="field">
          <label>Password</label>
          <input type="password" name="password" required placeholder="••••••••">
        </div>
        <div class="row-between">
          <label><input type="checkbox" name="remember"> Remember me</label>
          <a href="{{ url('/admin/forgot_password') }}" style="color:var(--violet)">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
      </form>

      <div class="alt">By continuing you agree to our terms.</div>
    </div>
  </div>
</div>
@endsection
