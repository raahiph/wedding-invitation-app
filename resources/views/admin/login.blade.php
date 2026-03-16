<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Wedding</title>
<link rel="icon" type="image/png" sizes="16x16" href="{{ config('app.url') }}/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="{{ config('app.url') }}/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="{{ config('app.url') }}/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="{{ config('app.url') }}/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="{{ config('app.url') }}/assets/img/512x512.png">
<style>
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-ExtraLight.ttf') format('truetype');
  font-weight:200; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Regular.ttf') format('truetype');
  font-weight:400; font-style:normal; font-display:swap;
}
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family:'Montserrat',sans-serif;
  background:#111827; color:#D9DEE5;
  min-height:100vh;
  display:flex; align-items:center; justify-content:center;
  padding:24px;
}
.box { width:100%; max-width:340px; text-align:center; }
h1 { font-weight:200; letter-spacing:0.15em; font-size:20px; margin-bottom:8px; color:#FAFBFC; }
.sub { color:#4B5B70; font-size:10px; letter-spacing:0.25em; text-transform:uppercase; margin-bottom:40px; }
.field { margin-bottom:12px; text-align:left; }
.field label { display:block; font-size:10px; font-weight:200; letter-spacing:0.3em; text-transform:uppercase; color:#9AA9B8; margin-bottom:8px; }
.field input {
  width:100%; background:rgba(255,255,255,0.05);
  border:1px solid rgba(255,255,255,0.12);
  border-radius:4px; color:#FAFBFC;
  font-family:'Montserrat',sans-serif; font-size:15px; font-weight:300;
  padding:12px 14px; outline:none; transition:border-color 0.2s;
}
.field input:focus { border-color:rgba(255,255,255,0.35); }
button {
  width:100%; background:transparent;
  border:1px solid rgba(255,255,255,0.25);
  color:#FAFBFC; font-family:'Montserrat',sans-serif;
  letter-spacing:0.3em; font-size:11px;
  padding:14px; cursor:pointer;
  border-radius:4px; text-transform:uppercase; margin-top:8px;
  transition:background 0.2s, border-color 0.2s;
}
button:hover { background:rgba(255,255,255,0.08); border-color:rgba(255,255,255,0.4); }
.err { color:#E87070; font-size:12px; margin-top:14px; letter-spacing:0.05em; }
</style>
</head>
<body>
<div class="box">
  <h1>Admin Access</h1>
  <p class="sub">Raahif &amp; Hadhu · 2026</p>
  <form method="POST" action="{{ route('admin.login.post') }}">
    @csrf
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" autocomplete="username" autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password">
    </div>
    <button type="submit">Enter</button>
  </form>
  @if($errors->has('password'))
    <p class="err">{{ $errors->first('password') }}</p>
  @endif
</div>
</body>
</html>
