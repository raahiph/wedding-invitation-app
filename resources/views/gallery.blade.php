<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $wedding['groom'] }} & {{ $wedding['bride'] }} — Our Gallery</title>
<meta name="description" content="Browse wedding photos shared by guests of {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — {{ $wedding['date'] }}, {{ $wedding['city'] }}.">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#22304A">
<meta name="color-scheme" content="only light">
<meta name="darkreader-lock">
<meta name="twitter:card" content="summary_large_image">
<meta property="og:title" content="{{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }} — Our Gallery">
<meta property="og:description" content="Browse wedding photos shared by guests — {{ $wedding['date'] }}">
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
  --midnight:  #111827;
  --blue-grey: #9AA9B8;
  --champagne: #EFE5D2;
  --cream:     #F4F7FA;
  --white:     #FAFBFC;
  --muted:     #6B7A8D;
  --border:    rgba(34,48,74,0.12);
}

html { scroll-behavior:smooth; color-scheme:only light; background-color:#F4F7FA !important; }
@media (prefers-color-scheme:dark) { html { filter:none !important; background-color:#F4F7FA !important; } }
body {
  font-family:'Montserrat',sans-serif;
  background:var(--cream);
  color:var(--deep-ink);
  min-height:100vh;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:9999; opacity:0.3;
}

/* ── Header ─────────────────────────────────────────── */
.gl-header {
  background:var(--navy);
  padding:52px 40px 44px;
  text-align:center;
  position:relative;
}
.gl-header-ornament { font-size:18px; color:rgba(154,169,184,0.25); letter-spacing:0.3em; margin-bottom:16px; }
.gl-header-names {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(40px,8vw,64px); line-height:1;
  color:white;
  text-shadow:0 2px 24px rgba(0,0,0,0.4);
  margin-bottom:40px;
  margin-top:40px;
}
.gl-header-sub {
  font-size:10px; font-weight:200; letter-spacing:0.45em;
  text-transform:uppercase; color:rgba(154,169,184,0.5);
}
.gl-back {
  position:absolute; top:20px; left:24px;
  font-size:10px; font-weight:300; letter-spacing:0.3em; text-transform:uppercase;
  color:rgba(154,169,184,0.5); text-decoration:none;
  transition:color 0.2s;
}
.gl-back:hover { color:var(--champagne); }

/* ── Upload bar ─────────────────────────────────────── */
.gl-upload-bar {
  background:var(--white);
  border-bottom:1px solid var(--border);
  padding:20px 40px;
  display:flex; align-items:center; justify-content:center; gap:16px;
  flex-wrap:wrap;
  position:sticky; top:0; z-index:100;
  box-shadow:0 2px 12px rgba(34,48,74,0.06);
}
.gl-upload-btn {
  display:inline-flex; align-items:center; gap:8px;
  background:transparent;
  border:1px solid rgba(34,48,74,0.25);
  color:var(--navy);
  font-family:'Montserrat',sans-serif;
  font-size:10px; font-weight:500; letter-spacing:0.35em; text-transform:uppercase;
  padding:12px 24px; cursor:pointer;
  transition:background 0.2s, border-color 0.2s;
}
.gl-upload-btn:hover { background:rgba(34,48,74,0.05); border-color:var(--navy); }
.gl-upload-btn:disabled { opacity:0.45; cursor:not-allowed; }
.gl-upload-btn svg { flex-shrink:0; }

/* ── Upload queue ───────────────────────────────────── */
.gl-queue {
  display:none;
  background:var(--white);
  border-bottom:1px solid var(--border);
  padding:12px 40px 16px;
  position:sticky; top:65px; z-index:99;
  box-shadow:0 2px 8px rgba(34,48,74,0.05);
}
.gl-queue-list { list-style:none; display:flex; flex-direction:column; gap:8px; max-width:520px; margin:0 auto; }
.gl-queue-item { display:grid; grid-template-columns:1fr auto; column-gap:10px; row-gap:4px; align-items:center; }
.gl-queue-name {
  font-size:10px; font-weight:300; color:var(--deep-ink);
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.gl-queue-status { font-size:10px; font-weight:300; color:var(--muted); white-space:nowrap; }
.gl-queue-status.done  { color:#5B9E6E; }
.gl-queue-status.error { color:#E87070; }
.gl-queue-bar { grid-column:1/-1; height:2px; background:var(--border); border-radius:2px; overflow:hidden; }
.gl-queue-fill { height:100%; width:0%; background:var(--navy); border-radius:2px; transition:width 0.1s linear; }
.gl-queue-fill.done { background:#5B9E6E; }

@media(max-width:540px) { .gl-queue { padding:10px 20px 14px; } }

/* ── Grid ───────────────────────────────────────────── */
.gl-body { padding:36px 32px 80px; max-width:1400px; margin:0 auto; }
.gl-empty {
  text-align:center; padding:80px 24px;
  font-size:12px; font-weight:300; letter-spacing:0.2em;
  color:var(--muted); text-transform:uppercase;
}
.gl-grid {
  columns:4; column-gap:10px;
}
.gl-item {
  break-inside:avoid; margin-bottom:10px;
  overflow:hidden; border-radius:2px;
  box-shadow:0 2px 10px rgba(0,0,0,0.07);
  animation:fadeIn 0.5s ease both;
  cursor:pointer;
}
.gl-item img {
  width:100%; display:block;
  transition:transform 0.4s ease;
}
.gl-item:hover img { transform:scale(1.04); }

@media(max-width:1100px) { .gl-grid { columns:3; } }
@media(max-width:700px)  { .gl-grid { columns:2; } }
@media(max-width:440px)  { .gl-grid { columns:2; column-gap:6px; } .gl-item { margin-bottom:6px; } .gl-body { padding:24px 14px 60px; } .gl-upload-bar { padding:16px 20px; } }

@keyframes fadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

/* ── Lightbox ───────────────────────────────────────── */
.lb {
  position:fixed; inset:0; z-index:9000;
  background:rgba(10,14,20,0.96);
  display:none; align-items:center; justify-content:center;
  padding:20px;
}
.lb.open { display:flex; }
.lb-img-wrap {
  max-width:min(92vw, 1100px);
  max-height:92vh;
  display:flex; align-items:center; justify-content:center;
}
.lb-img {
  max-width:100%; max-height:92vh;
  object-fit:contain;
  border-radius:2px;
  box-shadow:0 8px 60px rgba(0,0,0,0.6);
  transition:opacity 0.18s ease;
  display:block;
}
.lb-img.loading { opacity:0; }

.lb-btn {
  position:fixed; background:rgba(0,0,0,0.32);
  border:1px solid rgba(255,255,255,0.15); color:#fff;
  cursor:pointer; border-radius:50%;
  width:44px; height:44px;
  display:flex; align-items:center; justify-content:center;
  transition:background 0.18s;
  font-size:0; line-height:0;
  z-index:9001;
}
.lb-btn:hover { background:rgba(0,0,0,0.6); }
.lb-btn svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }

.lb-close { top:16px; right:16px; }
.lb-prev  { left:16px;  top:50%; transform:translateY(-50%); }
.lb-next  { right:16px; top:50%; transform:translateY(-50%); }

.lb-counter {
  position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
  font-size:10px; font-weight:300; letter-spacing:0.25em;
  color:rgba(255,255,255,0.6); background:rgba(0,0,0,0.3);
  padding:3px 10px; border-radius:20px; white-space:nowrap;
  z-index:9001;
  z-index:2;
}
</style>
</head>
<body>

<header class="gl-header">
  <a href="/" class="gl-back">← Invitation</a>
  <p class="gl-header-ornament">✦ ✦ ✦</p>
  <h1 class="gl-header-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</h1>
  <p class="gl-header-sub">Our Gallery By You! &nbsp;·&nbsp; {{ $wedding['date'] }}</p>
</header>

<div class="gl-upload-bar">
  <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp" multiple style="display:none">
  <button class="gl-upload-btn" id="upload-btn" onclick="document.getElementById('photo-input').click()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    Share Photos
  </button>
</div>
<div class="gl-queue" id="gl-queue">
  <ul class="gl-queue-list" id="gl-queue-list"></ul>
</div>

<div class="gl-body">
  @if($photos->isEmpty())
    <p class="gl-empty">No photos yet — be the first to share a moment.</p>
  @endif
  <div class="gl-grid" id="gl-grid">
    @foreach($photos as $photo)
      <div class="gl-item" data-id="{{ $photo->id }}" data-src="{{ $photo->thumbUrl() }}">
        <img src="{{ $photo->thumbUrl() }}" loading="lazy" alt="Guest photo">
      </div>
    @endforeach
  </div>
</div>

{{-- Lightbox --}}
<div class="lb" id="lb" role="dialog" aria-modal="true">
  <div class="lb-img-wrap">
    <img class="lb-img" id="lb-img" src="" alt="Gallery photo">
  </div>
  <button class="lb-btn lb-close" id="lb-close" aria-label="Close">
    <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <button class="lb-btn lb-prev" id="lb-prev" aria-label="Previous">
    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <button class="lb-btn lb-next" id="lb-next" aria-label="Next">
    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  </button>
  <span class="lb-counter" id="lb-counter"></span>
</div>

<script>
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
const grid       = document.getElementById('gl-grid');
const emptyMsg   = document.querySelector('.gl-empty');
const queueEl    = document.getElementById('gl-queue');
const queueList  = document.getElementById('gl-queue-list');
const uploadBtn  = document.getElementById('upload-btn');
let   knownIds   = new Set([...document.querySelectorAll('.gl-item')].map(el => Number(el.dataset.id)));
let   activeUploads = 0;

// ── File picker ──────────────────────────────────────────────────
document.getElementById('photo-input').addEventListener('change', function () {
  const files = Array.from(this.files);
  if (!files.length) return;
  this.value = '';
  files.forEach(uploadFile);
});

// ── Upload a single file ─────────────────────────────────────────
function uploadFile(file) {
  const li       = document.createElement('li');
  li.className   = 'gl-queue-item';
  li.innerHTML   = `
    <span class="gl-queue-name">${escHtml(file.name)}</span>
    <span class="gl-queue-status">0%</span>
    <div class="gl-queue-bar"><div class="gl-queue-fill"></div></div>`;
  queueList.appendChild(li);

  const statusEl = li.querySelector('.gl-queue-status');
  const fillEl   = li.querySelector('.gl-queue-fill');

  queueEl.style.display = 'block';
  activeUploads++;
  uploadBtn.disabled = true;

  const form = new FormData();
  form.append('photo', file);
  form.append('_token', csrfToken);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '{{ route("gallery.upload") }}');

  xhr.upload.addEventListener('progress', function (e) {
    if (!e.lengthComputable) return;
    const pct = Math.round((e.loaded / e.total) * 100);
    fillEl.style.width    = pct + '%';
    statusEl.textContent  = pct < 100 ? pct + '%' : 'Processing…';
  });

  xhr.addEventListener('load', function () {
    activeUploads--;
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.ok) {
        fillEl.style.width   = '100%';
        fillEl.classList.add('done');
        statusEl.textContent = 'Done';
        statusEl.className   = 'gl-queue-status done';
        prependPhoto(res.photo.id, res.photo.thumb_url);
        if (emptyMsg) emptyMsg.remove();
      } else {
        markError(fillEl, statusEl, res.message || 'Failed');
      }
    } catch {
      markError(fillEl, statusEl, 'Failed');
    }
    checkQueueDone();
  });

  xhr.addEventListener('error', function () {
    activeUploads--;
    markError(fillEl, statusEl, 'Network error');
    checkQueueDone();
  });

  xhr.send(form);
}

function markError(fillEl, statusEl, msg) {
  fillEl.style.width   = '100%';
  fillEl.style.background = '#E87070';
  statusEl.textContent = msg;
  statusEl.className   = 'gl-queue-status error';
}

function checkQueueDone() {
  if (activeUploads === 0) {
    uploadBtn.disabled = false;
    // Auto-hide queue after 3s
    setTimeout(() => {
      queueEl.style.display = 'none';
      queueList.innerHTML   = '';
    }, 3000);
  }
}

function escHtml(str) {
  return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// ── Prepend a new photo card ─────────────────────────────────────
function prependPhoto(id, thumbUrl) {
  if (knownIds.has(id)) return;
  knownIds.add(id);
  const div = document.createElement('div');
  div.className = 'gl-item';
  div.dataset.id  = id;
  div.dataset.src = thumbUrl;
  div.style.animationDelay = '0s';
  div.innerHTML = '<img src="' + thumbUrl + '" loading="lazy" alt="Guest photo">';
  grid.prepend(div);
  attachLightbox(div);
}

// ── Poll for new photos every 15s ───────────────────────────────
function poll() {
  fetch('{{ route("gallery.photos") }}', { cache: 'no-store' })
    .then(r => r.json())
    .then(photos => {
      photos.forEach(p => prependPhoto(p.id, p.thumb_url));
      if (photos.length && emptyMsg) emptyMsg.remove();
    })
    .catch(() => {});
}
setInterval(poll, 15000);

// ── Lightbox ─────────────────────────────────────────────────────
const lb        = document.getElementById('lb');
const lbImg     = document.getElementById('lb-img');
const lbCounter = document.getElementById('lb-counter');
let lbItems = [], lbIndex = 0;

function getItems() {
  return Array.from(grid.querySelectorAll('.gl-item'));
}

function openLb(index) {
  lbItems = getItems();
  lbIndex = index;
  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
  showLbPhoto();
}

function closeLb() {
  lb.classList.remove('open');
  document.body.style.overflow = '';
}

function showLbPhoto() {
  const item = lbItems[lbIndex];
  if (!item) return;
  const src = item.dataset.src;
  lbImg.classList.add('loading');
  const tmp = new Image();
  tmp.onload = () => { lbImg.src = src; lbImg.classList.remove('loading'); };
  tmp.src = src;
  lbCounter.textContent = (lbIndex + 1) + ' / ' + lbItems.length;
}

function lbPrev() { lbItems = getItems(); lbIndex = (lbIndex - 1 + lbItems.length) % lbItems.length; showLbPhoto(); }
function lbNext() { lbItems = getItems(); lbIndex = (lbIndex + 1) % lbItems.length; showLbPhoto(); }

document.getElementById('lb-close').addEventListener('click', closeLb);
document.getElementById('lb-prev').addEventListener('click', lbPrev);
document.getElementById('lb-next').addEventListener('click', lbNext);
lb.addEventListener('click', e => { if (e.target === lb) closeLb(); });

document.addEventListener('keydown', e => {
  if (!lb.classList.contains('open')) return;
  if (e.key === 'Escape')     closeLb();
  if (e.key === 'ArrowLeft')  lbPrev();
  if (e.key === 'ArrowRight') lbNext();
});

// Touch swipe
let tsX = 0;
lb.addEventListener('touchstart', e => { tsX = e.touches[0].clientX; }, { passive:true });
lb.addEventListener('touchend',   e => {
  const dx = e.changedTouches[0].clientX - tsX;
  if (Math.abs(dx) > 50) { dx < 0 ? lbNext() : lbPrev(); }
});

function attachLightbox(el) {
  el.addEventListener('click', () => {
    const items = getItems();
    openLb(items.indexOf(el));
  });
}

// Attach to all existing items
getItems().forEach(attachLightbox);
</script>

</body>
</html>
