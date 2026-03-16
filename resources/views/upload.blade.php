<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $wedding['groom'] }} & {{ $wedding['bride'] }} — Share a Photo</title>
<meta name="description" content="Share your photos from the wedding of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['city'] }}.">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#22304A">
<meta name="twitter:card" content="summary_large_image">
<meta property="og:title" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — Share a Photo">
<meta property="og:description" content="Upload your photos from our wedding day — {{ $wedding['date'] }}, {{ $wedding['city'] }}.">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ config('app.url') }}/assets/img/og-cover.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/png">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:site_name" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ config('app.url') }}/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="{{ config('app.url') }}/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="{{ config('app.url') }}/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="{{ config('app.url') }}/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="{{ config('app.url') }}/assets/img/512x512.png">
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
  src:url('/assets/fonts/Montserrat-Light.ttf') format('truetype');
  font-weight:300; font-style:normal; font-display:swap;
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

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --navy:      #22304A;
  --deep-ink:  #1A2333;
  --blue-grey: #9AA9B8;
  --champagne: #EFE5D2;
  --cream:     #F4F7FA;
  --white:     #FAFBFC;
  --muted:     #6B7A8D;
  --border:    rgba(34,48,74,0.12);
}

html, body { height:100%; }
body {
  font-family:'Montserrat',sans-serif;
  background:var(--navy);
  color:var(--deep-ink);
  display:flex; flex-direction:column;
  align-items:center; justify-content:center;
  min-height:100vh;
  padding:32px 20px;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:9999; opacity:0.3;
}

/* ── Card ─────────────────────────────────────────────── */
.up-card {
  background:var(--white);
  width:100%; max-width:440px;
  padding:48px 40px 40px;
  text-align:center;
  box-shadow:0 24px 80px rgba(0,0,0,0.3);
}

.up-ornament { font-size:16px; color:rgba(154,169,184,0.4); letter-spacing:0.3em; margin-bottom:20px; }

.up-names {
  font-family:'BrittanySignature',cursive; font-weight: 400;
  font-size:clamp(36px,9vw,56px);
  line-height:1; color:var(--navy);
  margin-bottom:40px;
  margin-top: 40px;
}

.up-sub {
  font-size:9px; font-weight:200; letter-spacing:0.45em;
  text-transform:uppercase; color:var(--muted);
  margin-bottom:36px;
}

.up-rule { width:40px; height:1px; background:var(--border); margin:0 auto 32px; }

.up-label {
  font-size:10px; font-weight:300; letter-spacing:0.2em;
  text-transform:uppercase; color:var(--muted);
  margin-bottom:20px;
}

/* ── Drop zone ─────────────────────────────────────────── */
.up-zone {
  border:1px dashed rgba(34,48,74,0.25);
  padding:40px 24px;
  cursor:pointer;
  transition:background 0.2s, border-color 0.2s;
  margin-bottom:16px;
  position:relative;
}
.up-zone:hover, .up-zone.drag { background:rgba(34,48,74,0.03); border-color:var(--navy); }
.up-zone svg { display:block; margin:0 auto 14px; opacity:0.3; }
.up-zone-text {
  font-size:11px; font-weight:300; letter-spacing:0.15em;
  color:var(--muted); line-height:1.8;
}
.up-zone-hint {
  font-size:9px; font-weight:200; letter-spacing:0.1em;
  color:rgba(107,122,141,0.55); margin-top:6px;
}
input[type=file] { display:none; }

/* ── Queue ─────────────────────────────────────────────── */
.up-queue { margin-top:16px; display:none; }
.up-queue-list { list-style:none; display:flex; flex-direction:column; gap:8px; }
.up-queue-item { display:grid; grid-template-columns:1fr auto; column-gap:10px; row-gap:4px; align-items:center; }
.up-queue-name { font-size:10px; font-weight:300; color:var(--deep-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; text-align:left; }
.up-queue-status { font-size:10px; font-weight:300; color:var(--muted); white-space:nowrap; }
.up-queue-status.done  { color:#5B9E6E; }
.up-queue-status.error { color:#E87070; }
.up-queue-bar  { grid-column:1/-1; height:2px; background:var(--border); border-radius:2px; overflow:hidden; }
.up-queue-fill { height:100%; width:0%; background:var(--navy); border-radius:2px; transition:width 0.1s linear; }
.up-queue-fill.done { background:#5B9E6E; }

/* ── Done state ────────────────────────────────────────── */
.up-done {
  display:none; flex-direction:column; align-items:center; gap:12px;
  padding:20px 0 4px;
}
.up-done-icon { color:#5B9E6E; }
.up-done-text { font-size:11px; font-weight:300; letter-spacing:0.15em; color:var(--muted); }
.up-more-btn {
  font-family:'Montserrat',sans-serif;
  font-size:9px; font-weight:400; letter-spacing:0.3em; text-transform:uppercase;
  color:var(--navy); background:transparent;
  border:1px solid rgba(34,48,74,0.2);
  padding:9px 20px; cursor:pointer; margin-top:4px;
  transition:background 0.2s, border-color 0.2s;
}
.up-more-btn:hover { background:rgba(34,48,74,0.04); border-color:var(--navy); }

@media(max-width:480px) { .up-card { padding:36px 24px 32px; } }

.up-gallery-link {
  display:inline-flex; align-items:center; gap:7px;
  margin-top:24px;
  font-size:9px; font-weight:300; letter-spacing:0.35em; text-transform:uppercase;
  color:rgba(154,169,184,0.55); text-decoration:none;
  transition:color 0.2s;
}
.up-gallery-link:hover { color:var(--champagne); }
.up-gallery-link svg { opacity:0.6; }
</style>
</head>
<body>

<div class="up-card">
  <p class="up-ornament">✦ ✦ ✦</p>
  <h1 class="up-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</h1>
  <p class="up-sub">{{ $wedding['date'] }}</p>
  <div class="up-rule"></div>
  <p class="up-label">Share your photos from our day</p>

  <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp" multiple>

  <div class="up-zone" id="up-zone" role="button" tabindex="0" aria-label="Choose photos to upload">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22304A" stroke-width="1.2">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="17 8 12 3 7 8"/>
      <line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
    <p class="up-zone-text">Tap to choose photos<br>or drag &amp; drop here</p>
    <p class="up-zone-hint">JPG, PNG or WEBP · up to 30 MB each</p>
  </div>

  <div class="up-queue" id="up-queue">
    <ul class="up-queue-list" id="up-queue-list"></ul>
  </div>

  <div class="up-done" id="up-done">
    <svg class="up-done-icon" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <circle cx="12" cy="12" r="10"/>
      <polyline points="9 12 11 14 15 10"/>
    </svg>
    <p class="up-done-text">Photos shared — thank you!</p>
    <button class="up-more-btn" id="up-more-btn">Share more</button>
  </div>
</div>

<a href="{{ route('gallery') }}" class="up-gallery-link">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
  View gallery
</a>

<script>
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
const photoInput = document.getElementById('photo-input');
const zone       = document.getElementById('up-zone');
const queueEl    = document.getElementById('up-queue');
const queueList  = document.getElementById('up-queue-list');
const doneEl     = document.getElementById('up-done');
let activeUploads = 0;

zone.addEventListener('click', () => photoInput.click());
zone.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') photoInput.click(); });

photoInput.addEventListener('change', function () {
  const files = Array.from(this.files);
  if (!files.length) return;
  this.value = '';
  startUploads(files);
});

// Drag & drop
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag'));
zone.addEventListener('drop', e => {
  e.preventDefault();
  zone.classList.remove('drag');
  const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
  if (files.length) startUploads(files);
});

function startUploads(files) {
  zone.style.display   = 'none';
  queueEl.style.display = 'block';
  doneEl.style.display  = 'none';
  files.forEach(uploadFile);
}

function uploadFile(file) {
  const li     = document.createElement('li');
  li.className = 'up-queue-item';
  li.innerHTML = `
    <span class="up-queue-name">${escHtml(file.name)}</span>
    <span class="up-queue-status">0%</span>
    <div class="up-queue-bar"><div class="up-queue-fill"></div></div>`;
  queueList.appendChild(li);

  const statusEl = li.querySelector('.up-queue-status');
  const fillEl   = li.querySelector('.up-queue-fill');

  activeUploads++;

  const form = new FormData();
  form.append('photo', file);
  form.append('_token', csrfToken);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '{{ route("gallery.upload") }}');

  xhr.upload.addEventListener('progress', e => {
    if (!e.lengthComputable) return;
    const pct = Math.round((e.loaded / e.total) * 100);
    fillEl.style.width   = pct + '%';
    statusEl.textContent = pct < 100 ? pct + '%' : 'Processing…';
  });

  xhr.addEventListener('load', () => {
    activeUploads--;
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.ok) {
        fillEl.style.width   = '100%';
        fillEl.classList.add('done');
        statusEl.textContent = 'Done';
        statusEl.className   = 'up-queue-status done';
      } else {
        markError(fillEl, statusEl, res.message || 'Failed');
      }
    } catch { markError(fillEl, statusEl, 'Failed'); }
    checkDone();
  });

  xhr.addEventListener('error', () => {
    activeUploads--;
    markError(fillEl, statusEl, 'Network error');
    checkDone();
  });

  xhr.send(form);
}

function markError(fillEl, statusEl, msg) {
  fillEl.style.width      = '100%';
  fillEl.style.background = '#E87070';
  statusEl.textContent    = msg;
  statusEl.className      = 'up-queue-status error';
}

function checkDone() {
  if (activeUploads > 0) return;
  setTimeout(() => {
    queueEl.style.display = 'none';
    doneEl.style.display  = 'flex';
    queueList.innerHTML   = '';
  }, 1200);
}

document.getElementById('up-more-btn').addEventListener('click', () => {
  doneEl.style.display = 'none';
  zone.style.display   = 'block';
});

function escHtml(str) {
  return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
</script>

</body>
</html>
