<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $album->name }} — {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }}</title>
<meta name="description" content="{{ $album->name }} · {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }}, {{ $wedding['date'] }}">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#22304A">
<meta name="color-scheme" content="only light">
<meta name="darkreader-lock">
<meta property="og:title" content="{{ $album->name }} — {{ $wedding['groom'] }} &amp; {{ $wedding['bride'] }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ config('app.url') }}/assets/img/og-cover.png">
<link rel="icon" type="image/png" sizes="16x16" href="{{ config('app.url') }}/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="{{ config('app.url') }}/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="{{ config('app.url') }}/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="{{ config('app.url') }}/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="{{ config('app.url') }}/assets/img/512x512.png">
<style>
@font-face { font-family:'BrittanySignature'; src:url('/assets/fonts/BrittanySignature.ttf') format('truetype'); font-weight:400; font-style:normal; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-ExtraLight.ttf') format('truetype'); font-weight:200; font-style:normal; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Light.ttf') format('truetype'); font-weight:300; font-style:normal; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Regular.ttf') format('truetype'); font-weight:400; font-style:normal; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Medium.ttf') format('truetype'); font-weight:500; font-style:normal; font-display:swap; }

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --navy:      #22304A;
  --champagne: #EFE5D2;
  --cream:     #F4F7FA;
  --border:    rgba(34,48,74,0.12);
  --muted:     #6B7A8D;
}

html { scroll-behavior:smooth; color-scheme:only light; background:#F4F7FA !important; }
@media (prefers-color-scheme:dark) { html { filter:none !important; background:#F4F7FA !important; } }
body {
  font-family:'Montserrat',sans-serif;
  background:var(--cream);
  color:#1A2333;
  min-height:100vh;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:9999; opacity:0.3;
}

/* ── Header ─────────────────────────────────────────── */
.hd {
  background:var(--navy);
  padding:52px 40px 44px;
  text-align:center;
  position:relative;
}
.hd-ornament {
  font-size:18px; color:rgba(154,169,184,0.25);
  letter-spacing:0.3em; margin-bottom:16px;
}
.hd-names {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(40px,8vw,64px); line-height:1;
  color:white;
  text-shadow:0 2px 24px rgba(0,0,0,0.4);
  margin-bottom:16px; margin-top:40px;
}
.hd-album {
  display:inline-block;
  font-size:10px; font-weight:500; letter-spacing:0.35em; text-transform:uppercase;
  color:rgba(154,169,184,0.6);
  border:1px solid rgba(154,169,184,0.2);
  padding:5px 14px; border-radius:20px; margin-bottom:10px;
}
.hd-sub {
  font-size:10px; font-weight:200; letter-spacing:0.45em;
  text-transform:uppercase; color:rgba(154,169,184,0.45);
  display:block;
}

/* ── Body ───────────────────────────────────────────── */
.body { padding:36px 32px 80px; max-width:1400px; margin:0 auto; }

.empty {
  text-align:center; padding:80px 24px;
  font-size:12px; font-weight:300; letter-spacing:0.2em;
  color:var(--muted); text-transform:uppercase;
}

/* ── Masonry grid ───────────────────────────────────── */
.grid { columns:4; column-gap:10px; }
.item {
  break-inside:avoid; margin-bottom:10px;
  position:relative; overflow:hidden; border-radius:2px;
  box-shadow:0 2px 10px rgba(0,0,0,0.07);
  cursor:pointer;
  animation:fadeIn 0.5s ease both;
}
.item img { width:100%; display:block; transition:transform 0.4s ease; }
.item:hover img { transform:scale(1.04); }

/* Group count badge */
.group-count {
  position:absolute; top:8px; right:8px;
  font-size:9px; font-weight:600; letter-spacing:0.04em;
  background:rgba(0,0,0,0.42); color:rgba(255,255,255,0.92);
  padding:3px 8px; border-radius:10px;
  pointer-events:none;
  display:flex; align-items:center; gap:4px;
}
.group-count svg { opacity:0.75; }

/* GIF badge */
.gif-badge {
  position:absolute; bottom:8px; left:8px;
  font-size:9px; font-weight:700; letter-spacing:0.06em;
  background:rgba(0,0,0,0.45); color:#fff;
  padding:2px 7px; border-radius:3px; pointer-events:none;
}

/* Video items */
.vid-wrap { position:relative; }
.play-overlay {
  position:absolute; inset:0;
  display:flex; align-items:center; justify-content:center;
  background:rgba(0,0,0,0.22); transition:background 0.22s;
}
.item:hover .play-overlay { background:rgba(0,0,0,0.12); }
.play-circle {
  width:46px; height:46px; border-radius:50%;
  background:rgba(255,255,255,0.22);
  border:1.5px solid rgba(255,255,255,0.4);
  display:flex; align-items:center; justify-content:center;
  transition:transform 0.2s, background 0.2s;
}
.item:hover .play-circle { transform:scale(1.08); background:rgba(255,255,255,0.32); }
.play-circle svg { width:17px; height:17px; margin-left:3px; }

@media(max-width:1100px) { .grid { columns:3; } }
@media(max-width:700px)  { .grid { columns:2; } }
@media(max-width:440px) {
  .grid { columns:2; column-gap:6px; }
  .item { margin-bottom:6px; }
  .body { padding:24px 14px 60px; }
}

/* Stagger first 12 */
.item:nth-child(1) { animation-delay:.00s }.item:nth-child(2) { animation-delay:.03s }
.item:nth-child(3) { animation-delay:.06s }.item:nth-child(4) { animation-delay:.09s }
.item:nth-child(5) { animation-delay:.12s }.item:nth-child(6) { animation-delay:.15s }
.item:nth-child(7) { animation-delay:.18s }.item:nth-child(8) { animation-delay:.21s }
.item:nth-child(9) { animation-delay:.24s }.item:nth-child(10){ animation-delay:.27s }
.item:nth-child(11){ animation-delay:.30s }.item:nth-child(12){ animation-delay:.33s }

@keyframes fadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

/* ── Footer ─────────────────────────────────────────── */
.footer {
  text-align:center; padding:16px 24px 44px;
  font-size:10px; font-weight:300; letter-spacing:0.28em; text-transform:uppercase;
  color:var(--muted);
}

/* ── Lightbox ───────────────────────────────────────── */
.lb {
  position:fixed; inset:0; z-index:9000;
  background:rgba(10,14,20,0.96);
  display:none; flex-direction:column;
}
.lb.open { display:flex; }

.lb-wrap {
  flex:1; display:flex; align-items:center; justify-content:center;
  padding:60px 70px 12px;
  overflow:hidden; min-height:0;
}

.lb-img {
  max-width:100%; max-height:100%;
  object-fit:contain; border-radius:2px;
  box-shadow:0 8px 60px rgba(0,0,0,0.6);
  transition:opacity 0.18s ease; display:block;
}
.lb-img.fading { opacity:0; }

/* ── Filmstrip ──────────────────────────────────────── */
.lb-strip {
  display:none; flex-direction:row; align-items:center;
  gap:5px; padding:10px 70px 18px;
  overflow-x:auto; scroll-behavior:smooth;
  scrollbar-width:none; flex-shrink:0;
}
.lb-strip::-webkit-scrollbar { display:none; }

.st {
  flex-shrink:0; width:68px; height:48px;
  border-radius:2px; overflow:hidden;
  cursor:pointer; opacity:0.38;
  border:2px solid transparent;
  transition:opacity 0.18s, border-color 0.18s;
  position:relative;
}
.st:hover { opacity:0.7; }
.st.active { opacity:1; border-color:rgba(255,255,255,0.55); }
.st img, .st video { width:100%; height:100%; object-fit:cover; display:block; pointer-events:none; }
.st-play {
  position:absolute; inset:0;
  display:flex; align-items:center; justify-content:center;
  font-size:11px; color:rgba(255,255,255,0.9);
  background:rgba(0,0,0,0.35); pointer-events:none;
}

/* ── Lightbox buttons ───────────────────────────────── */
.lb-btn {
  position:fixed; z-index:9001;
  background:rgba(0,0,0,0.32);
  border:1px solid rgba(255,255,255,0.15); color:#fff;
  cursor:pointer; border-radius:50%; width:44px; height:44px;
  display:flex; align-items:center; justify-content:center;
  transition:background 0.18s; font-size:0; line-height:0;
}
.lb-btn:hover { background:rgba(0,0,0,0.6); }
.lb-btn svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }

.lb-close { top:16px; right:16px; }
.lb-dl    { top:16px; right:68px; text-decoration:none; display:flex; align-items:center; justify-content:center; }
.lb-prev  { left:16px;  top:50%; transform:translateY(-50%); }
.lb-next  { right:16px; top:50%; transform:translateY(-50%); }

.lb-counter {
  position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
  font-size:10px; font-weight:300; letter-spacing:0.25em;
  color:rgba(255,255,255,0.6); background:rgba(0,0,0,0.3);
  padding:3px 10px; border-radius:20px; white-space:nowrap;
  z-index:9001; pointer-events:none;
}

@media(max-width:680px) {
  .lb-wrap  { padding:60px 50px 12px; }
  .lb-strip { padding:10px 50px 16px; }
  .lb-prev  { left:8px; }
  .lb-next  { right:8px; }
  .st       { width:54px; height:38px; }
}
</style>
</head>
<body>

@php $totalPhotos = $groups->sum(fn($g) => $g->count()); @endphp

<header class="hd">
  <p class="hd-ornament">✦ ✦ ✦</p>
  <h1 class="hd-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</h1>
  <span class="hd-album">{{ $album->name }}</span>
  <span class="hd-sub">{{ $wedding['date'] }}@if($totalPhotos > 0) &nbsp;·&nbsp; {{ $totalPhotos }} photo{{ $totalPhotos === 1 ? '' : 's' }}@endif</span>
</header>

<div class="body">
  @if($groups->isEmpty())
    <p class="empty">Photos coming soon.</p>
  @else
    <div class="grid" id="grid">
      @foreach($groups as $gi => $group)
        @php
          $rep   = $group->first(fn($p) => !$p->isVideo() && $p->thumb_path) ?? $group->first();
          $count = $group->count();
        @endphp
        <div class="item" data-gi="{{ $gi }}">
          @if($rep && $rep->isVideo())
            <div class="vid-wrap">
              <video src="{{ $rep->thumbUrl() }}" preload="metadata" muted playsinline
                     style="width:100%;display:block;"></video>
              <div class="play-overlay">
                <div class="play-circle">
                  <svg viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
              </div>
            </div>
          @elseif($rep)
            <img src="{{ $rep->thumbUrl() }}"
                 loading="{{ $gi < 8 ? 'eager' : 'lazy' }}"
                 alt="{{ $album->name }} {{ $gi + 1 }}">
            @if($rep->isGif())<span class="gif-badge">GIF</span>@endif
          @endif
          @if($count > 1)
            <span class="group-count">
              <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="14" height="14" rx="2"/><path d="M8 2h12a2 2 0 0 1 2 2v12"/></svg>
              {{ $count }}
            </span>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</div>

@if($totalPhotos > 0)
  <p class="footer">{{ $groups->count() }} session{{ $groups->count() === 1 ? '' : 's' }} &nbsp;·&nbsp; {{ $totalPhotos }} photo{{ $totalPhotos === 1 ? '' : 's' }}</p>
@endif

{{-- Lightbox --}}
<div class="lb" id="lb" role="dialog" aria-modal="true">
  <div class="lb-wrap" id="lb-wrap">
    <img class="lb-img" id="lb-img" src="" alt="">
  </div>
  <div class="lb-strip" id="lb-strip"></div>
  <a class="lb-btn lb-dl" id="lb-dl" href="#" title="Download">
    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
  </a>
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

@php
$groupsData = $groups->map(fn($g) => $g->map(fn($p) => [
    'src'      => $p->thumbUrl(),
    'type'     => $p->type,
    'download' => $p->downloadUrl(),
])->values()->all())->all();
@endphp
<script>
const GROUPS = @json($groupsData);

const lb        = document.getElementById('lb');
const lbWrap    = document.getElementById('lb-wrap');
const lbImg     = document.getElementById('lb-img');
const lbDl      = document.getElementById('lb-dl');
const lbStrip   = document.getElementById('lb-strip');
const lbCounter = document.getElementById('lb-counter');
let lbVideo = null;
let gIdx = 0, pIdx = 0;

function openGroup(gi, pi) {
  gIdx = gi; pIdx = pi || 0;
  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
  render();
}

function closeLb() {
  lb.classList.remove('open');
  document.body.style.overflow = '';
  if (lbVideo) { lbVideo.pause(); lbVideo.src = ''; }
}

function render() {
  var group = GROUPS[gIdx];
  var photo = group[pIdx];

  lbDl.href = photo.download;
  lbCounter.textContent = group.length > 1 ? (pIdx + 1) + ' / ' + group.length : '';

  renderStrip(group);

  if (photo.type === 'video') {
    lbImg.style.display = 'none';
    if (!lbVideo) {
      lbVideo = document.createElement('video');
      lbVideo.controls    = true;
      lbVideo.loop        = true;
      lbVideo.playsInline = true;
      lbVideo.style.cssText = 'max-width:100%;max-height:100%;border-radius:2px;box-shadow:0 8px 60px rgba(0,0,0,.6);display:block;';
      lbWrap.appendChild(lbVideo);
    }
    lbVideo.src = photo.src;
    lbVideo.style.display = 'block';
    lbVideo.load();
    lbVideo.play().catch(function(){});
  } else {
    if (lbVideo) { lbVideo.pause(); lbVideo.src = ''; lbVideo.style.display = 'none'; }
    lbImg.style.display = 'block';
    lbImg.classList.add('fading');
    var tmp = new Image();
    tmp.onload = function() { lbImg.src = photo.src; lbImg.classList.remove('fading'); };
    tmp.src = photo.src;
  }
}

function renderStrip(group) {
  if (group.length <= 1) {
    lbStrip.style.display = 'none';
    lbStrip.innerHTML = '';
    return;
  }
  lbStrip.style.display = 'flex';
  lbStrip.innerHTML = group.map(function(p, i) {
    var cls = i === pIdx ? 'st active' : 'st';
    if (p.type === 'video') {
      return '<div class="' + cls + '" data-pi="' + i + '"><video src="' + p.src + '" preload="metadata" muted playsinline></video><div class="st-play">▶</div></div>';
    }
    return '<img class="' + cls + '" src="' + p.src + '" data-pi="' + i + '" loading="lazy">';
  }).join('');
  lbStrip.querySelectorAll('.st').forEach(function(el) {
    el.addEventListener('click', function() { pIdx = +el.dataset.pi; render(); });
  });
  var active = lbStrip.querySelector('.st.active');
  if (active) active.scrollIntoView({ behavior:'smooth', inline:'center', block:'nearest' });
}

function prev() {
  if (pIdx > 0) { pIdx--; }
  else if (gIdx > 0) { gIdx--; pIdx = GROUPS[gIdx].length - 1; }
  else { gIdx = GROUPS.length - 1; pIdx = GROUPS[gIdx].length - 1; }
  render();
}

function next() {
  if (pIdx < GROUPS[gIdx].length - 1) { pIdx++; }
  else if (gIdx < GROUPS.length - 1) { gIdx++; pIdx = 0; }
  else { gIdx = 0; pIdx = 0; }
  render();
}

document.getElementById('lb-close').addEventListener('click', closeLb);
document.getElementById('lb-prev').addEventListener('click', prev);
document.getElementById('lb-next').addEventListener('click', next);
lb.addEventListener('click', function(e) { if (e.target === lb) closeLb(); });

document.addEventListener('keydown', function(e) {
  if (!lb.classList.contains('open')) return;
  if (e.key === 'Escape')     closeLb();
  if (e.key === 'ArrowLeft')  prev();
  if (e.key === 'ArrowRight') next();
});

var tsX = 0;
lb.addEventListener('touchstart', function(e) { tsX = e.touches[0].clientX; }, { passive:true });
lb.addEventListener('touchend', function(e) {
  var dx = e.changedTouches[0].clientX - tsX;
  if (Math.abs(dx) > 50) dx < 0 ? next() : prev();
});

document.querySelectorAll('.item[data-gi]').forEach(function(el) {
  el.addEventListener('click', function() { openGroup(+el.dataset.gi); });
});
</script>

</body>
</html>
