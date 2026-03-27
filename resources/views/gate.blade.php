<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $wedding['groom'] }} & {{ $wedding['bride'] }} — Enter</title>
<meta name="description" content="You're invited to the wedding of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['venue'] }}, {{ $wedding['city'] }}.">
<meta name="twitter:card" content="summary_large_image">
<meta property="og:title" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — Wedding Invitation">
<meta property="og:description" content="You're invited to the wedding of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['venue'] }}, {{ $wedding['city'] }}.">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ config('app.url') }}/assets/img/og-cover.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/png">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:site_name" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }}">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#22304A">
<meta name="color-scheme" content="only light">
<meta name="darkreader-lock">
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
  src:url('/assets/fonts/Montserrat-Light.ttf') format('truetype');
  font-weight:300; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Regular.ttf') format('truetype');
  font-weight:400; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'BrittanySignature';
  src:url('/assets/fonts/BrittanySignature.ttf') format('truetype');
  font-weight:400; font-style:normal; font-display:swap;
}

html { color-scheme:only light; background-color:#22304A !important; }
@media (prefers-color-scheme:dark) { html { filter:none !important; background-color:#22304A !important; } }
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

body {
  font-family:'Montserrat',sans-serif;
  background:#22304A;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:24px;
}

body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:0; opacity:0.3;
}

.gate-wrap {
  position:relative; z-index:1;
  width:100%; max-width:380px;
  text-align:center;
}

.gate-ornament {
  color:#9AA9B8; letter-spacing:0.4em; font-size:11px;
  margin-bottom:32px; font-weight:200;
}

.gate-names {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(40px,8vw,56px); line-height:1;
  color:#FAFBFC; margin-bottom:8px;
  text-shadow:0 0 40px rgba(255,255,255,0.15), 0 2px 20px rgba(0,0,0,0.4);
}

.gate-subtitle {
  color:#9AA9B8; font-size:11px; font-weight:200;
  letter-spacing:0.3em; text-transform:uppercase; margin-top:30px; margin-bottom:48px;
}

.gate-label {
  color:#9AA9B8; font-size:10px; font-weight:200;
  letter-spacing:0.35em; text-transform:uppercase;
  text-align:left; margin-bottom:10px; display:block;
}

.gate-input {
  width:100%;
  background:rgba(255,255,255,0.05);
  border:1px solid rgba(255,255,255,0.12);
  border-radius:4px;
  color:#FAFBFC;
  font-family:'Montserrat',sans-serif;
  font-size:16px;
  font-weight:300;
  padding:14px 16px;
  outline:none;
  transition:border-color 0.2s;
  margin-bottom:12px;
  letter-spacing:0.08em;
}
.gate-input:focus { border-color:rgba(255,255,255,0.35); }
.gate-input.error { border-color:rgba(232,112,112,0.6); }

.gate-btn {
  width:100%;
  background:transparent;
  border:1px solid rgba(255,255,255,0.25);
  color:#FAFBFC;
  font-family:'Montserrat',sans-serif;
  font-size:11px; font-weight:400;
  letter-spacing:0.35em; text-transform:uppercase;
  padding:14px; cursor:pointer;
  transition:background 0.2s, border-color 0.2s;
  border-radius:4px;
}
.gate-btn:hover { background:rgba(255,255,255,0.08); border-color:rgba(255,255,255,0.4); }

.gate-error {
  color:#E87070; font-size:12px; font-weight:300;
  letter-spacing:0.1em; margin-top:14px; min-height:18px;
}

.gate-tagline {
  color:#4B5B70; font-size:10px; font-weight:200;
  letter-spacing:0.2em; margin-top:48px;
}
</style>
</head>
<body>
<div class="gate-wrap">
  <p class="gate-ornament">✦ &nbsp; ✦ &nbsp; ✦</p>
  <h1 class="gate-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</h1>
  <p class="gate-subtitle">{{ $wedding['date'] }}</p>

  <form method="POST" action="{{ route('gate.verify') }}" id="gate-form">
    @csrf
    <label class="gate-label" for="mobile">Your mobile number</label>
    <input
      type="tel"
      id="mobile"
      name="mobile"
      class="gate-input{{ $errors->has('mobile') ? ' error' : '' }}"
      placeholder="e.g. 9123456"
      autocomplete="tel"
      inputmode="tel"
      maxlength="20"
      value="{{ old('mobile') }}"
      autofocus
    >
    <button type="submit" class="gate-btn">Enter</button>
  </form>

  @if($errors->has('mobile'))
    <p class="gate-error">{{ $errors->first('mobile') }}</p>
  @endif

  <p class="gate-tagline">By invitation only — thank you for being part of our story.</p>
</div>
</body>
</html>
