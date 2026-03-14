<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $wedding['groom'] }} & {{ $wedding['bride'] }} — {{ $wedding['date_short'] }}</title>
<meta name="description" content="You're invited to the wedding of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['venue'] }}, {{ $wedding['city'] }}.">
<meta name="twitter:card" content="summary_large_image">
<meta property="og:title" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — Wedding Invitation">
<meta property="og:description" content="You're invited to the wedding of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['venue'] }}, {{ $wedding['city'] }}.">
<meta property="og:type" content="website">
<meta property="og:image" content="/assets/img/og-cover.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/img/512x512.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@font-face {
  font-family:'BrittanySignature';
  src:url('/assets/fonts/BrittanySignature.ttf') format('truetype');
  font-weight:400; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-ExtraLight.ttf') format('truetype');
  font-weight:200; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-ExtraLightItalic.ttf') format('truetype');
  font-weight:200; font-style:italic; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Light.ttf') format('truetype');
  font-weight:300; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-LightItalic.ttf') format('truetype');
  font-weight:300; font-style:italic; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Regular.ttf') format('truetype');
  font-weight:400; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Medium.ttf') format('truetype');
  font-weight:500; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-SemiBold.ttf') format('truetype');
  font-weight:600; font-style:normal; font-display:swap;
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --navy:      #22304A;
  --muted:     #6B7A8D;
  --light-muted:#8E9BAB;
  --text:      #1A2333;
  --border:    rgba(34,48,74,0.12);
  --white:     #FAFBFC;
}

html { scroll-behavior:smooth; }
body {
  font-family:'Montserrat',sans-serif;
  background:#111827;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:9999; opacity:0.3;
}

/* ── Hero ──────────────────────────────────────── */

.hero {
  position:relative; height:100vh;
  display:flex; align-items:center; justify-content:center; overflow:hidden;
}
@media (max-width: 768px) {
  .hero {
  position:relative; height:90vh;
  display:flex; align-items:center; justify-content:center; overflow:hidden;
}
}
.hero-bg { position:absolute; inset:0; z-index:0; }
.hero-bg img {
  width:100%; height:100%;
  object-fit:cover; object-position:center center;
  animation:kenBurns 24s ease-in-out infinite alternate;
}
@keyframes kenBurns {
  from { transform:scale(1.0) translateX(0); }
  to   { transform:scale(1.07) translateX(-1%); }
}

.hero-overlay {
  position:absolute; inset:0; z-index:1;
  background:rgba(0,0,0,0.45);
}
.hero-vignette {
  position:absolute; inset:0; z-index:2; pointer-events:none;
  background:radial-gradient(ellipse 120% 100% at 50% 100%, transparent 40%, rgba(17,24,39,0.5) 100%);
}

.hero-frame {
  position:absolute; inset:0; z-index:3; pointer-events:none;
}
.hero-frame::before {
  content:''; position:absolute; top:36px; left:36px; right:36px; height:1px;
  background:linear-gradient(to right, rgba(217,222,229,0.0), rgba(217,222,229,0.18), rgba(217,222,229,0.0));
}
.hero-frame::after {
  content:''; position:absolute; bottom:0; left:0; right:0; height:2px;
  background:linear-gradient(to right, transparent, #22304A, transparent);
}

.hero-badge {
  position:absolute; top:80px; left:50%; transform:translateX(-50%);
  z-index:4; text-align:center; white-space:nowrap;
  animation:fadeSlideDown 1.2s ease 0.3s both;
}
.hero-badge-text {
  font-family:'Montserrat',sans-serif; font-size:10px; font-weight:300;
  color:rgba(255,255,255,0.7); letter-spacing:0.55em; text-transform:uppercase;
  text-shadow:0 1px 8px rgba(0,0,0,0.6);
}

.hero-body {
  position:relative; z-index:4;
  width:100%; padding:0 80px;
  display:flex; flex-direction:column;
  align-items:center; text-align:center;
  margin-bottom:160px;
}

.hero-eyebrow {
  font-family:'Montserrat',sans-serif; font-style:italic; font-weight:400;
  font-size:clamp(14px,1.8vw,19px); color:rgba(255,255,255,0.88);
  line-height:1.7; margin:10px 0 14px;
  animation:fadeSlideUp 1s ease 0.6s both;
  text-shadow:0 1px 10px rgba(0,0,0,0.6);
}

.hero-title {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(55px,10vw,108px); line-height:1;
  display:flex; flex-direction:column; align-items:center;
  animation:fadeSlideUp 1s ease 0.75s both;
  text-shadow:0 2px 12px rgba(0,0,0,0.55), 0 8px 32px rgba(0,0,0,0.35);
  margin-top: 4rem;
  margin-bottom: 2rem;
}
.hero-title .name-m { display:block; color:#ffffff; }
.hero-title .name-f { display:block; color:#ffffff; }

.hero-amp {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(34px,5.5vw,64px);
  color:rgba(255,255,255,0.75); margin:0;
  display:block;
  animation:fadeSlideUp 1s ease 0.85s both;
}

.hero-sep {
  margin:0 0 12px;
  animation:fadeSlideUp 1s ease 1.05s both;
}
.sep-date {
  font-family:'Montserrat',sans-serif; font-size:clamp(15px,2vw,22px); font-weight:300;
  color:rgba(255,255,255,0.92); letter-spacing:0.35em; white-space:nowrap;
  text-shadow:0 1px 12px rgba(0,0,0,0.7);
}

.hero-place {
  font-family:'Montserrat',sans-serif; font-style:italic; font-weight:400;
  font-size:clamp(13px,1.5vw,17px); color:rgba(255,255,255,0.75);
  line-height:1.8; text-shadow:0 1px 8px rgba(0,0,0,0.6);
  animation:fadeSlideUp 1s ease 1.15s both;
}

.hero-countdown {
  position:absolute; bottom:150px; left:0; right:0;
  z-index:4;
  display:flex; flex-direction:column; align-items:center; gap:14px;
  animation:fadeSlideUp 1s ease 2s both;
}
.cd-label {
  font-family:'Montserrat',sans-serif; font-size:8px; font-weight:300;
  letter-spacing:0.55em; text-transform:uppercase; color:rgba(255,255,255,0.4);
}
.cd-units {
  display:flex; align-items:flex-start; gap:24px;
}
.cd-unit {
  display:flex; flex-direction:column; align-items:center; gap:6px;
}
.cd-num {
  font-family:'Montserrat',sans-serif; font-size:clamp(26px,4vw,44px);
  font-weight:300; color:rgba(255,255,255,0.92); letter-spacing:0.04em;
  min-width:2ch; text-align:center; line-height:1;
}
.cd-lbl {
  font-family:'Montserrat',sans-serif; font-size:7px; font-weight:300;
  letter-spacing:0.45em; text-transform:uppercase; color:rgba(255,255,255,0.4);
}
.cd-sep {
  font-family:'Montserrat',sans-serif; font-size:clamp(26px,4vw,44px);
  font-weight:300; color:rgba(255,255,255,0.2); line-height:1;
}

.hero-scroll {
  position:absolute; bottom:36px; left:0; right:0; z-index:4;
  display:flex; flex-direction:column; align-items:center; gap:10px;
  animation:fadeSlideUp 1s ease 2.5s both;
}
.hero-scroll span { font-size:9px; letter-spacing:0.45em; text-transform:uppercase; color:rgba(255,255,255,0.7); }
.hero-scroll-line {
  width:3px; height:52px;
  background:linear-gradient(to bottom, rgba(255,255,255,0.7), transparent);
  animation:scrollPulse 2.5s ease-in-out infinite;
}
@keyframes scrollPulse { 0%,100%{opacity:0.25} 50%{opacity:0.9} }

/* ── RSVP section ──────────────────────────────── */
.rsvp-section {
  background:var(--white);
  border-top:1px solid var(--border);
}
.rsvp-inner {
  max-width:600px; margin:0 auto;
  padding:88px 40px;
  display:flex; flex-direction:column;
  align-items:center; text-align:center;
}
.s-label {
  display:block; font-size:9px; font-weight:300; letter-spacing:0.55em;
  text-transform:uppercase; color:var(--navy); margin-bottom:16px;
}
.s-title {
  font-family:'Montserrat',sans-serif; font-weight:400;
  font-size:clamp(30px,3.8vw,48px); line-height:1.2;
  color:var(--text); margin-bottom:24px;
}
.rsvp-dear {
  font-family:'Montserrat',sans-serif; font-size:9px; font-weight:300;
  letter-spacing:0.45em; text-transform:uppercase; color:var(--muted);
  margin-bottom:20px;
}
.rsvp-guest-name {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(38px,6vw,58px); line-height:1.1;
  color:var(--navy); display:block; margin-bottom:20px;
  margin-top:20px;
}
.rsvp-ayah-line {
  font-family:'Amiri',serif; font-size:18px; direction:rtl;
  color:var(--navy); line-height:2; margin-bottom:8px;
}
.rsvp-ayah-trans {
  font-family:'Montserrat',sans-serif; font-style:italic; font-size:13px;
  color:var(--light-muted); line-height:1.65; margin-bottom:32px;
}
.rsvp-lead {
  font-family:'Montserrat',sans-serif; font-style:italic; font-size:16px;
  font-weight:400; color:var(--muted); line-height:1.8; margin-bottom:24px;
}
.rsvp-submitted-note {
  font-family:'Montserrat',sans-serif; font-size:13px; font-weight:400;
  color:var(--muted); background:rgba(34,48,74,0.05);
  border-left:2px solid rgba(34,48,74,0.25);
  padding:12px 16px; margin-bottom:28px; line-height:1.6; text-align:left;
}
.form { display:flex; flex-direction:column; gap:20px; width:100%; }
.field input, .field select {
  width:100%; background:transparent; border:none;
  border-bottom:1px solid rgba(34,48,74,0.18);
  padding:14px 0; font-family:'Montserrat',sans-serif;
  font-size:12px; font-weight:400; letter-spacing:0.06em;
  color:var(--text); outline:none; transition:border-color 0.3s ease; appearance:none;
}
.field input::placeholder { color:rgba(110,122,141,0.55); }
.field input:focus, .field select:focus { border-color:var(--navy); }
.field select { cursor:pointer; color:var(--muted); }
.radio-group { display:flex; width:100%; gap:14px; }
.radio-option { flex:1; position:relative; }
.radio-option input[type="radio"] { position:absolute; opacity:0; width:0; height:0; }
.radio-option label {
  display:flex; flex-direction:column; align-items:center; gap:10px;
  padding:30px 16px 26px; cursor:pointer;
  border:1px solid rgba(34,48,74,0.15);
  background:transparent;
  transition:border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
  user-select:none;
}
.radio-option label:hover {
  border-color:rgba(34,48,74,0.35);
  background:rgba(34,48,74,0.025);
}
.radio-icon {
  font-size:22px; line-height:1; display:block;
  transition:color 0.3s ease, transform 0.3s ease;
}
.radio-icon i { pointer-events:none; }
.radio-yes .radio-icon,
.radio-no  .radio-icon { color:rgba(110,122,141,0.35); }
.radio-yes input[type="radio"]:checked + label .radio-icon { color:#C0392B; transform:scale(1.18); }
.radio-no  input[type="radio"]:checked + label .radio-icon { color:#9AA9B8; transform:scale(1.18); }
.radio-title {
  font-family:'Montserrat',sans-serif; font-size:10px; font-weight:400;
  letter-spacing:0.22em; text-transform:uppercase; line-height:1;
  color:var(--light-muted);
  transition:color 0.3s ease;
}
.radio-sub {
  font-family:'Montserrat',sans-serif; font-size:11px; font-style:italic; font-weight:400;
  color:rgba(110,122,141,0.5); line-height:1;
  transition:color 0.3s ease;
}
.radio-option input[type="radio"]:checked + label {
  border-color:var(--navy);
  background:rgba(34,48,74,0.04);
  box-shadow:inset 0 0 0 1px var(--navy);
}
.radio-option input[type="radio"]:checked + label .radio-title { color:var(--navy); }
.radio-option input[type="radio"]:checked + label .radio-sub  { color:var(--muted); }
.rsvp-btn {
  margin-top:10px; padding:16px 52px; background:transparent;
  border:1px solid var(--navy); color:var(--navy);
  font-family:'Montserrat',sans-serif; font-size:9px; font-weight:400;
  letter-spacing:0.5em; text-transform:uppercase; cursor:pointer;
  transition:all 0.5s ease; align-self:center; position:relative; overflow:hidden;
  white-space:nowrap; min-width:240px; box-sizing:border-box;
}
.rsvp-btn::before {
  content:''; position:absolute; inset:0; background:#9AA9B8;
  transform:translateX(-100%); transition:transform 0.45s ease; z-index:-1;
}
.rsvp-btn:hover { color:var(--navy); border-color:#9AA9B8; }
.rsvp-btn:hover::before { transform:translateX(0); }
.rsvp-msg { margin-top:16px; font-size:13px; font-style:italic; color:var(--navy); min-height:20px; }
.rsvp-msg.error { color:#c0392b; }
.cal-wrap { margin-top:36px; text-align:center; width:100%; }
.cal-btn {
  font-family:'Montserrat',sans-serif; font-size:9px; font-weight:400;
  letter-spacing:0.25em; text-transform:uppercase; color:var(--navy);
  border:1px solid rgba(34,48,74,0.25); padding:9px 22px;
  text-decoration:none; transition:border-color 0.25s, background 0.25s;
}
.cal-btn:hover { border-color:var(--navy); background:rgba(34,48,74,0.04); }

@keyframes fadeSlideUp {
  from { opacity:0; transform:translateY(24px); }
  to   { opacity:1; transform:translateY(0); }
}
@keyframes fadeSlideDown {
  from { opacity:0; transform:translateX(-50%) translateY(-18px); }
  to   { opacity:1; transform:translateX(-50%) translateY(0); }
}

@media(max-width:900px) {
  .hero-body { padding:0 24px; }
  .cd-units { gap:16px; }
}
@media(max-width:480px) {
  .cd-units { gap:12px; }
  .cd-sep { display:none; }
  .rsvp-inner { padding:52px 28px; }
}
</style>
</head>
<body>

<section class="hero">
  <div class="hero-bg">
    <img src="/assets/img/hero-image.jpg" alt="{{ $wedding['groom'] }} & {{ $wedding['bride'] }}">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-vignette"></div>
  <div class="hero-frame"></div>

  <div class="hero-badge">
    <p class="hero-badge-text">Save the Date</p>
  </div>

  <div class="hero-body">
    <h1 class="hero-title">
      <span class="name-m">{{ $wedding['groom'] }}</span>
      <span class="hero-amp">&</span>
      <span class="name-f">{{ $wedding['bride'] }}</span>
    </h1>
    <p class="hero-eyebrow">request the pleasure of your company<br>at the celebration of our marriage</p>
    <div class="hero-sep">
      <span class="sep-date">{{ $wedding['date_short'] }}</span>
    </div>
    <p class="hero-eyebrow">Formal invitation to follow.</p>
  </div>

  <div class="hero-countdown">
    <p class="cd-label">Counting down</p>
    <div class="cd-units">
      <div class="cd-unit">
        <span class="cd-num" id="cd-days">00</span>
        <span class="cd-lbl">Days</span>
      </div>
      <span class="cd-sep">·</span>
      <div class="cd-unit">
        <span class="cd-num" id="cd-hours">00</span>
        <span class="cd-lbl">Hours</span>
      </div>
      <span class="cd-sep">·</span>
      <div class="cd-unit">
        <span class="cd-num" id="cd-mins">00</span>
        <span class="cd-lbl">Minutes</span>
      </div>
      <span class="cd-sep">·</span>
      <div class="cd-unit">
        <span class="cd-num" id="cd-secs">00</span>
        <span class="cd-lbl">Seconds</span>
      </div>
    </div>
  </div>

  <div class="hero-scroll">
    <div class="hero-scroll-line"></div>
    <span>RSVP below</span>
  </div>
</section>

<!-- ══════════════ RSVP ══════════════ -->
<div class="rsvp-section">
  <div class="rsvp-inner">
    <!-- <span class="s-label">RSVP</span> -->
    @if($guest->name)
      <p class="rsvp-dear">Dearest</p>
      <span class="rsvp-guest-name">{{ $guest->name }}</span>
    @endif
    <h2 class="s-title">Will you <em style="font-style:italic;color:var(--navy);">join us?</em></h2>
    <p class="rsvp-lead">Kindly respond by {{ $wedding['rsvp_by'] }}.<br>We cannot wait to celebrate with you.</p>

    @if($rsvp ?? false)
      <p class="rsvp-submitted-note">
        You responded as <strong>{{ $rsvp->attending ? 'attending' : 'not attending' }}</strong>. You can update your response below.
      </p>
    @endif

    <form class="form" id="rsvp-form">
      @csrf
      <div class="radio-group">
        <div class="radio-option radio-yes">
          <input type="radio" id="att-yes" name="attending" value="1"
                 {{ isset($rsvp) && $rsvp->attending ? 'checked' : '' }}>
          <label for="att-yes">
            <span class="radio-icon"><i class="fa-solid fa-heart"></i></span>
            <span class="radio-title">Joyfully accepts</span>
            <span class="radio-sub">I'll be there</span>
          </label>
        </div>
        <div class="radio-option radio-no">
          <input type="radio" id="att-no" name="attending" value="0"
                 {{ isset($rsvp) && !$rsvp->attending ? 'checked' : '' }}>
          <label for="att-no">
            <span class="radio-icon"><i class="fa-solid fa-heart-crack"></i></span>
            <span class="radio-title">Regretfully declines</span>
            <span class="radio-sub">Unable to attend</span>
          </label>
        </div>
      </div>
      <button type="submit" class="rsvp-btn" id="rsvp-btn">{{ isset($rsvp) ? 'Update Response' : 'Confirm Attendance' }}</button>
      <p class="rsvp-msg" id="rsvp-msg"></p>
    </form>

    <div class="cal-wrap">
      <a href="{{ route('calendar.ics') }}" class="cal-btn">Add to Calendar</a>
    </div>
  </div>
</div>

<script>
  // Countdown timer
  const target = new Date('{{ $wedding['datetime_iso'] }}');

  function pad(n) { return String(n).padStart(2, '0'); }

  const timer = setInterval(tick, 1000);

  function tick() {
    const diff = target - new Date();
    if (diff <= 0) {
      ['cd-days','cd-hours','cd-mins','cd-secs'].forEach(id => {
        document.getElementById(id).textContent = '00';
      });
      clearInterval(timer);
      return;
    }
    const days  = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);
    const mins  = Math.floor((diff % 3600000)  / 60000);
    const secs  = Math.floor((diff % 60000)    / 1000);
    document.getElementById('cd-days').textContent  = pad(days);
    document.getElementById('cd-hours').textContent = pad(hours);
    document.getElementById('cd-mins').textContent  = pad(mins);
    document.getElementById('cd-secs').textContent  = pad(secs);
  }

  tick();


  // RSVP form
  document.getElementById('rsvp-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn  = document.getElementById('rsvp-btn');
    const msg  = document.getElementById('rsvp-msg');

    const checkedRadio = document.querySelector('input[name="attending"]:checked');
    if (!checkedRadio) {
      msg.textContent = 'Please select an option.';
      msg.className   = 'rsvp-msg error';
      return;
    }

    const attending = checkedRadio.value;

    btn.disabled    = true;
    btn.textContent = 'Sending…';
    msg.textContent = '';
    msg.className   = 'rsvp-msg';

    const body = new URLSearchParams({
      _token:    document.querySelector('meta[name="csrf-token"]').content,
      attending,
    });

    fetch('{{ route("rsvp.store") }}', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body,
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          msg.textContent      = data.message;
          btn.textContent      = attending === '1' ? 'See you there ♡' : 'You\'ll be missed.';
          btn.style.background = '#22304A';
          btn.style.color      = '#fff';
          btn.style.border     = '1px solid #22304A';
        } else {
          msg.textContent = data.message || 'Something went wrong.';
          msg.className   = 'rsvp-msg error';
          btn.disabled    = false;
          btn.textContent = 'Confirm Attendance';
        }
      })
      .catch(() => {
        msg.textContent = 'Session expired. Please refresh the page and try again.';
        msg.className   = 'rsvp-msg error';
        btn.disabled    = false;
        btn.textContent = 'Confirm Attendance';
      });
  });
</script>
</body>
</html>
