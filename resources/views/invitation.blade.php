<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $wedding['groom'] }} & {{ $wedding['bride'] }} — {{ $wedding['date'] }}</title>
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
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/img/512x512.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
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
  src:url('/assets/fonts/Montserrat-Italic.ttf') format('truetype');
  font-weight:400; font-style:italic; font-display:swap;
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
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-Bold.ttf') format('truetype');
  font-weight:700; font-style:normal; font-display:swap;
}
@font-face {
  font-family:'Montserrat';
  src:url('/assets/fonts/Montserrat-BoldItalic.ttf') format('truetype');
  font-weight:700; font-style:italic; font-display:swap;
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --navy:       #22304A;
  --deep-ink:   #1A2333;
  --midnight:   #111827;
  --blue-grey:  #9AA9B8;
  --silver:     #D9DEE5;
  --champagne:  #EFE5D2;
  --cream:      #F4F7FA;
  --white:      #FAFBFC;
  --powder-blue: #a0c2e8;
  --muted:      #6B7A8D;
  --light-muted:#8E9BAB;
  --border:     rgba(34,48,74,0.12);
  --text:       #1A2333;
}

html { scroll-behavior:smooth; }
body {
  font-family:'Montserrat',sans-serif;
  background:var(--cream);
  color:var(--text);
  overflow-x:hidden;
}
body::before {
  content:''; position:fixed; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events:none; z-index:9999; opacity:0.3;
}

/* ══════════════════════════════
   HERO
══════════════════════════════ */
.hero {
  position:relative; height:100vh;
  display:flex; align-items:center; justify-content:center; overflow:hidden;
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
  background: rgba(0,0,0,0.45);
}
.hero-vignette {
  position:absolute; inset:0; z-index:2; pointer-events:none;
  background:
    radial-gradient(ellipse 120% 100% at 50% 100%, transparent 40%, rgba(17,24,39,0.5) 100%);
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
  background:linear-gradient(to right, transparent, var(--navy), transparent);
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
}
.hero-eyebrow {
  font-family:'Montserrat',sans-serif; font-style:italic; font-weight:400;
  font-size:clamp(14px,1.8vw,19px); color:rgba(255,255,255,0.88);
  line-height:1.7; margin:60px 0 14px;
  animation:fadeSlideUp 1s ease 0.6s both;
  text-shadow:0 1px 10px rgba(0,0,0,0.6);
}
.hero-title {
  font-family:'BrittanySignature',cursive; font-weight:400;
  font-size:clamp(55px,10vw,108px); line-height:1;
  display:flex; flex-direction:column; align-items:center;
  animation:fadeSlideUp 1s ease 0.75s both;
  text-shadow:0 2px 12px rgba(0,0,0,0.55), 0 8px 32px rgba(0,0,0,0.35);
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
.hero-sep { margin:0 0 12px; animation:fadeSlideUp 1s ease 1.05s both; }
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
.hero-scroll {
  position:absolute; bottom:36px; left:0; right:0; z-index:4;
  display:flex; flex-direction:column; align-items:center; gap:10px;
  animation:fadeSlideUp 1s ease 2.5s both;
}
.hero-scroll span { font-size:9px; letter-spacing:0.45em; text-transform:uppercase; color:rgba(255,255,255,0.7); }
.hero-scroll-line {
  width:1px; height:95px;
  background:linear-gradient(to bottom, rgba(255,255,255,0.7), transparent);
  animation:scrollPulse 2.5s ease-in-out infinite;
}
@keyframes scrollPulse { 0%,100%{opacity:0.25} 50%{opacity:0.9} }

/* ══════════════════════════════
   AYAH BAND
══════════════════════════════ */
.ayah-band {
  background:var(--navy); padding:88px 40px; text-align:center;
  position:relative; overflow:hidden;
}
.ayah-band::before {
  content:''; position:absolute; top:0; left:0; right:0; height:2px;
  background:linear-gradient(to right, transparent, var(--blue-grey), transparent);
}
.ayah-band::after {
  content:''; position:absolute; bottom:0; left:0; right:0; height:2px;
  background:linear-gradient(to right, transparent, var(--blue-grey), transparent);
}
.band-label { font-size:8px; font-weight:300; letter-spacing:0.55em; text-transform:uppercase; color:rgba(154,169,184,0.5); margin-bottom:40px; }
.band-cd-units { display:flex; align-items:flex-start; justify-content:center; gap:clamp(20px,4vw,56px); }
.band-cd-unit { display:flex; flex-direction:column; align-items:center; gap:12px; }
.band-cd-num { font-family:'Montserrat',sans-serif; font-size:clamp(36px,6vw,72px); font-weight:300; color:rgba(239,229,210,0.96); letter-spacing:0.04em; min-width:2ch; text-align:center; line-height:1; }
.band-cd-lbl { font-size:8px; font-weight:300; letter-spacing:0.45em; text-transform:uppercase; color:rgba(154,169,184,0.4); }
.band-cd-sep { font-family:'Montserrat',sans-serif; font-size:clamp(36px,6vw,72px); font-weight:300; color:rgba(154,169,184,0.15); line-height:1; padding-top:0; }
@media(max-width:480px) { .band-cd-sep { display:none; } }

/* ══════════════════════════════
   PHOTO BANDS
══════════════════════════════ */
.photo-band { position:relative; height:540px; overflow:hidden; }
.photo-band img { width:100%; height:100%; object-fit:cover; filter:grayscale(0.05) contrast(1.05); transition:transform 1s ease; }
.photo-band:hover img { transform:scale(1.025); }
.pb-overlay {
  position:absolute; inset:0;
  background:linear-gradient(to top, rgba(17,24,39,0.82) 0%, rgba(17,24,39,0.35) 45%, rgba(17,24,39,0.08) 100%);
  display:flex; align-items:flex-end; justify-content:flex-start;
}
.pb-text { text-align:left; padding:36px 44px; max-width:560px; }
.pb-quote { font-family:'Montserrat',sans-serif; font-style:italic; font-size:clamp(16px,2.2vw,26px); font-weight:400; color:#fff; line-height:1.6; margin-bottom:10px; text-shadow:0 2px 24px rgba(0,0,0,0.5); }
.pb-cite { font-size:9px; letter-spacing:0.4em; text-transform:uppercase; color:rgba(154,169,184,0.6); }
.photo-band.pb-img-right img { object-position:right center; }
@media(max-width:760px) {
  .pb-overlay.pb-right-mobile { justify-content:flex-end; }
  .pb-overlay.pb-right-mobile .pb-text { text-align:right; }
  .photo-band.pb-img-right img { object-position:center center; }
}

/* ══════════════════════════════
   DETAILS
══════════════════════════════ */
.details-wrap { background:var(--white); border-top:1px solid var(--border); padding:100px 40px; text-align:center; }
.details-inner { max-width:920px; margin:0 auto; }
.s-label { display:block; font-size:9px; font-weight:300; letter-spacing:0.55em; text-transform:uppercase; color:var(--navy); margin-bottom:16px; }
.s-title { font-family:'Montserrat',sans-serif; font-weight:400; font-size:clamp(30px,3.8vw,48px); line-height:1.2; color:var(--text); margin-bottom:0; }
.dcards { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:var(--border); border:1px solid var(--border); margin-top:56px; }
.dcard { background:var(--white); padding:50px 24px; text-align:center; transition:background 0.35s ease; position:relative; overflow:hidden; }
.dcard::after { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:0; height:2px; background:var(--navy); transition:width 0.45s ease; }
.dcard:hover { background:rgba(217,222,229,0.18); }
.dcard:hover::after { width:100%; }
.dcard-icon { margin-bottom:18px; opacity:0.38; }
.dcard-icon svg { width:28px; height:28px; }
.dcard-lbl { font-size:8px; letter-spacing:0.5em; text-transform:uppercase; color:var(--navy); font-weight:400; margin-bottom:11px; }
.dcard-main { font-family:'Montserrat',sans-serif; font-size:21px; font-weight:400; color:var(--text); line-height:1.4; margin-bottom:7px; }
.dcard-sub { font-size:11px; font-weight:300; letter-spacing:0.1em; color:var(--muted); }
.dc-split { display:flex; gap:0; align-items:flex-start; justify-content:center; width:100%; padding-top:16px; }
.dc-gender { flex:1; }
.dc-gender-lbl { font-size:8px; letter-spacing:0.4em; text-transform:uppercase; color:var(--muted); margin-bottom:6px; font-weight:400; }
.dc-divider { width:1px; background:var(--border); align-self:stretch; margin:0 12px; }

/* ══════════════════════════════
   AYAH MID
══════════════════════════════ */
.ayah-mid { background:var(--cream); border-top:1px solid var(--border); padding:88px 40px; text-align:center; }
.ayah-mid-inner { max-width:760px; margin:0 auto; }
.ayah-mid-arabic { font-family:'Amiri',serif; font-size:clamp(20px,3vw,30px); color:var(--navy); line-height:2.2; direction:rtl; margin-bottom:24px; }
.ayah-mid-rule { width:40px; height:1px; background:linear-gradient(to right, var(--navy), var(--blue-grey)); margin:0 auto 22px; }
.ayah-mid-trans { font-family:'Montserrat',sans-serif; font-style:italic; font-size:clamp(17px,2vw,22px); font-weight:400; color:var(--muted); line-height:1.9; margin-bottom:16px; }
.ayah-mid-ref { font-size:9px; font-weight:300; letter-spacing:0.45em; text-transform:uppercase; color:var(--light-muted); }

/* ══════════════════════════════
   PALETTE
══════════════════════════════ */
.palette-wrap { background:var(--cream); padding:90px 40px; text-align:center; border-top:1px solid var(--border); }
.palette-title { font-family:'Montserrat',sans-serif; font-weight:400; font-size:clamp(30px,3.8vw,46px); line-height:1.2; color:var(--text); margin-bottom:18px; }
.palette-title em { font-style:italic; color:var(--navy); }
.palette-rule { width:40px; height:1px; background:linear-gradient(to right,var(--navy),var(--blue-grey)); margin:0 auto 14px; }
.palette-note { font-family:'Montserrat',sans-serif; font-style:italic; font-size:16px; color:var(--muted); line-height:1.7; margin-bottom:56px; }
.swatches { display:flex; justify-content:center; gap:30px; flex-wrap:wrap; }
.sw { display:flex; flex-direction:column; align-items:center; gap:12px; }
.sw-c { width:80px; height:80px; border-radius:50%; transition:transform 0.35s ease, box-shadow 0.35s ease; cursor:pointer; }
.sw:hover .sw-c { transform:translateY(-7px) scale(1.1); box-shadow:0 18px 40px rgba(34,48,74,0.25); }
.sw-name { font-size:9px; letter-spacing:0.22em; text-transform:uppercase; color:var(--muted); font-weight:400; }
.sw-hex { font-family:'Montserrat',sans-serif; font-size:13px; font-style:italic; color:var(--blue-grey); }

/* ══════════════════════════════
   GALLERY PREVIEW
══════════════════════════════ */
.gl-preview-wrap { background:var(--white); border-top:1px solid var(--border); padding:90px 40px; text-align:center; }
.gl-preview-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin:48px auto 36px; max-width:800px; }
.gl-preview-item { aspect-ratio:1/1; overflow:hidden; border-radius:2px; }
.gl-preview-item img { width:100%; height:100%; object-fit:cover; transition:transform 0.35s ease; }
.gl-preview-item:hover img { transform:scale(1.05); }
@media(max-width:640px) { .gl-preview-grid { grid-template-columns:repeat(2,1fr); } }

/* ══════════════════════════════
   FOOTER
══════════════════════════════ */
footer { background:var(--navy); padding:90px 40px; text-align:center; }
.footer-ornament { font-size:24px; color:rgba(154,169,184,0.15); letter-spacing:0.3em; margin-bottom:28px; }
.footer-names { font-family:'BrittanySignature',cursive; font-size:clamp(44px,7vw,72px); font-weight:400; color:white; margin-bottom:16px; }
.footer-rule { width:40px; height:1px; background:var(--navy); margin:22px auto; }
.footer-detail { font-size:9px; letter-spacing:0.5em; text-transform:uppercase; color:rgba(154,169,184,0.4); font-weight:300; margin-bottom:28px; }
.footer-closing { font-family:'Montserrat',sans-serif; font-style:italic; font-size:16px; color:rgba(154,169,184,0.35); line-height:1.7; }

/* ══════════════════════════════
   ANIMATIONS
══════════════════════════════ */
@keyframes fadeSlideUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeSlideDown { from { opacity:0; transform:translateX(-50%) translateY(-18px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }
.reveal { opacity:0; transform:translateY(28px); transition:opacity 1s ease, transform 1s ease; }
.reveal.in { opacity:1; transform:translateY(0); }
.reveal.d1 { transition-delay:0.12s; }
.reveal.d2 { transition-delay:0.24s; }
.reveal.d3 { transition-delay:0.36s; }
.reveal.d4 { transition-delay:0.48s; }

/* ══════════════════════════════
   RESPONSIVE
══════════════════════════════ */
@media(max-width:900px) {
  .hero-body { padding:0 24px; }
  .hero-scroll { bottom:28px; }
  .photo-band { height:360px; }
  .dcards { grid-template-columns:1fr 1fr; }
  .dcard.d3, .dcard.d4 { grid-column: span 2; }
  .dcard.d3 {
    display:flex; flex-direction:row; align-items:center; justify-content:center;
    text-align:center; gap:24px; padding:32px 28px;
  }
  .dcard.d3 .dcard-icon { margin-bottom:0; flex-shrink:0; }
  .rsvp-form-side { padding:52px 28px; }
  .swatches { gap:18px; }
  .sw-c { width:60px; height:60px; }
}
.cal-wrap { margin-top:40px; text-align:center; }
.cal-btn {
  font-family:'Montserrat',sans-serif; font-size:9px; font-weight:400;
  letter-spacing:0.25em; text-transform:uppercase; color:var(--navy);
  border:1px solid rgba(34,48,74,0.25); padding:9px 22px;
  text-decoration:none; transition:border-color 0.25s, background 0.25s;
}
.cal-btn:hover { border-color:var(--navy); background:rgba(34,48,74,0.04); }

/* ══════════════════════════════
   TIMELINE
══════════════════════════════ */
.tl-wrap { background:var(--white); border-top:1px solid var(--border); padding:100px 40px; text-align:center; }
.tl-inner { max-width:960px; margin:0 auto; }
.tl {
  display:flex; position:relative;
  margin-top:56px;
}
/* horizontal connecting line */
.tl::before {
  content:''; position:absolute;
  top:calc(44px + 8px + 18px); /* tl-time min-height(44px) + margin-bottom(8px) + dot-half(18px) */
  left:0; right:0; height:2px;
  background:var(--border); z-index:0;
}
.tl-item {
  flex:1; display:flex; flex-direction:column;
  align-items:center; position:relative; z-index:1;
  cursor:default; transition:transform 0.25s;
  opacity:0; transform:translateY(20px);
  transition:opacity 0.5s ease, transform 0.5s ease;
}
.tl-item.tl-in {
  opacity:1; transform:translateY(0);
}
.tl-item.tl-in:hover { transform:translateY(-4px); }
.tl-time {
  font-family:'Montserrat',sans-serif; font-size:10px; font-weight:400;
  letter-spacing:0.2em; text-transform:uppercase; color:var(--navy);
  min-height:44px; display:flex; align-items:flex-end; justify-content:center;
  margin-bottom:8px; line-height:1.4; text-align:center;
}
.tl-dot {
  width:36px; height:36px; border-radius:50%;
  background:var(--navy); flex-shrink:0;
  margin-bottom:12px;
  display:flex; align-items:center; justify-content:center;
  color:var(--white); font-size:14px;
  transition:background 0.25s, box-shadow 0.25s, color 0.25s;
}
.tl-label {
  font-size:10px; font-weight:300; letter-spacing:0.08em;
  color:var(--muted); line-height:1.7; text-align:center; max-width:90px;
  transition:color 0.25s;
}
.tl-item:hover .tl-dot {
  background:var(--white);
  box-shadow:0 0 0 3px var(--navy);
  color:var(--navy);
}
.tl-item:hover .tl-time { color:var(--navy); opacity:1; }
.tl-item:hover .tl-label { color:var(--navy); }
.tl-time { transition:opacity 0.25s, color 0.25s; }
/* vertical alternating (mobile) */
@media(max-width:760px) {
  .tl { flex-direction:column; padding-left:0; align-items:stretch; }
  .tl::before {
    top:0; bottom:0;
    left:50%; right:auto; width:2px; height:auto;
    transform:translateX(-50%);
  }
  .tl-item {
    display:grid;
    grid-template-columns:1fr 40px 1fr;
    column-gap:10px;
    padding-bottom:36px; position:relative;
  }
  .tl-item:last-child { padding-bottom:0; }

  /* dot always centred */
  .tl-dot {
    position:static; margin:0;
    grid-column:2; grid-row:1 / span 2;
    justify-self:center; align-self:start; margin-top:3px;
  }

  /* odd items — content on the left */
  .tl-item:nth-child(odd) .tl-time {
    grid-column:1; grid-row:1;
    min-height:unset; margin-bottom:4px;
    justify-content:flex-end; align-items:flex-start; text-align:right;
  }
  .tl-item:nth-child(odd) .tl-label  { grid-column:1; grid-row:2; text-align:right; max-width:none; }

  /* even items — content on the right */
  .tl-item:nth-child(even) .tl-time {
    grid-column:3; grid-row:1;
    min-height:unset; margin-bottom:4px;
    justify-content:flex-start; align-items:flex-start; text-align:left;
  }
  .tl-item:nth-child(even) .tl-label { grid-column:3; grid-row:2; text-align:left; max-width:none; }
}

</style>
</head>
<body>

<!-- ══════════════ HERO ══════════════ -->
<section class="hero">
  <div class="hero-bg">
    <img src="/assets/img/hero-image.jpg" alt="{{ $wedding['groom'] }} & {{ $wedding['bride'] }}">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-vignette"></div>
  <div class="hero-frame"></div>

  <div class="hero-badge">
    <p class="hero-badge-text">Together</p><br>
    <p class="hero-badge-text"> with their families</p>
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
    <p class="hero-place">
      @if($guest && $guest->attends_ceremony)
        {{ $wedding['wedding_time_words'] }}
      @elseif($guest && $guest->session == 1)
        {{ $wedding['session1_time_words'] }}
      @elseif($guest && $guest->session == 2)
        {{ $wedding['session2_time_words'] }}
      @else
        {{ $wedding['reception_time_words'] }}
      @endif
      <br>at {{ $wedding['venue'] }}, {{ $wedding['city'] }}</p>
  </div>

  <div class="hero-scroll">
    <div class="hero-scroll-line"></div>
    <span>Scroll</span>
  </div>
</section>

<!-- ══════════════ AYAH II ══════════════ -->
<div class="ayah-mid reveal">
  <div class="ayah-mid-inner">
    <span class="s-label">Destined Together</span>
    <p class="ayah-mid-arabic">وَخَلَقْنَاكُمْ أَزْوَاجًا</p>
    <div class="ayah-mid-rule"></div>
    <p class="ayah-mid-trans">"And We created you in pairs."</p>
    <p class="ayah-mid-ref">— Surah An-Naba, 78:8</p>
  </div>
</div>

<!-- ══════════════ PHOTO BAND 1 ══════════════ -->
<div class="photo-band reveal pb-img-right">
  <picture>
    <source media="(max-width:760px)" srcset="/assets/img/image1-mobile.jpg">
    <img src="/assets/img/image1.jpg">
  </picture>
  <div class="pb-overlay">
    <div class="pb-text">
      <p class="pb-quote">"Every path I walk, I want to walk with you."</p>
    </div>
  </div>
</div>

<!-- ══════════════ DETAILS ══════════════ -->
<div class="details-wrap reveal">
  <div class="details-inner">
    <span class="s-label">The Celebration</span>
    <h2 class="s-title">Date, time <em style="font-style:italic;color:var(--navy);">&amp; place</em></h2>
    <div class="dcards">
      <div class="dcard reveal d1">
        <div class="dcard-icon">
          <svg viewBox="0 0 28 28" fill="none"><rect x="2" y="5" width="24" height="21" rx="2" stroke="#22304A" stroke-width="1.2"/><line x1="2" y1="11" x2="26" y2="11" stroke="#22304A" stroke-width="1.2"/><line x1="9" y1="2" x2="9" y2="7" stroke="#22304A" stroke-width="1.2" stroke-linecap="round"/><line x1="19" y1="2" x2="19" y2="7" stroke="#22304A" stroke-width="1.2" stroke-linecap="round"/><rect x="10" y="15" width="5" height="5" rx="1" fill="#22304A"/></svg>
        </div>
        <p class="dcard-lbl">Date</p>
        <p class="dcard-main">{{ $wedding['date'] }}</p>
        <p class="dcard-sub">{{ $wedding['date_day'] }}</p>
      </div>
      <div class="dcard reveal d2">
        <div class="dcard-icon">
          <svg viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="11" stroke="#22304A" stroke-width="1.2"/><line x1="14" y1="6" x2="14" y2="14" stroke="#22304A" stroke-width="1.2" stroke-linecap="round"/><line x1="14" y1="14" x2="19" y2="17" stroke="#9AA9B8" stroke-width="1.2" stroke-linecap="round"/><circle cx="14" cy="14" r="1.5" fill="#22304A"/></svg>
        </div>
        <p class="dcard-lbl">Time</p>
        @if($guest && $guest->attends_ceremony)
          <p class="dcard-main">{{ $wedding['wedding_start'] }} – {{ $wedding['wedding_end'] }}</p>
          <p class="dcard-sub">Wedding celebration</p>
        @elseif($guest && $guest->session == 1)
          <p class="dcard-main">{{ $wedding['session1_start'] }} – {{ $wedding['session1_end'] }}</p>
          <p class="dcard-sub">Reception</p>
        @elseif($guest && $guest->session == 2)
          <p class="dcard-main">{{ $wedding['session2_start'] }} – {{ $wedding['session2_end'] }}</p>
          <p class="dcard-sub">Reception</p>
        @else
          <p class="dcard-main">{{ $wedding['reception_time'] }}</p>
          <p class="dcard-sub">Reception begins promptly</p>
        @endif
      </div>
      @if(!empty($wedding['venue_map_url']))
      <a href="{{ $wedding['venue_map_url'] }}" target="_blank" rel="noopener" class="dcard reveal d3" style="text-decoration:none;color:inherit;display:block;">
      @else
      <div class="dcard reveal d3">
      @endif
        <div class="dcard-icon">
          <svg viewBox="0 0 28 28" fill="none"><path d="M14 2C10 2 6 5.5 6 10c0 7.5 8 16 8 16s8-8.5 8-16c0-4.5-4-8-8-8z" stroke="#22304A" stroke-width="1.2" fill="none"/><circle cx="14" cy="10" r="3.5" stroke="#9AA9B8" stroke-width="1.2"/></svg>
        </div>
        <p class="dcard-lbl">Venue</p>
        <p class="dcard-main">{{ $wedding['venue'] }}</p>
        <p class="dcard-sub">{{ $wedding['city'] }}, {{ $wedding['country'] }}</p>
      @if(!empty($wedding['venue_map_url']))
      </a>
      @else
      </div>
      @endif
      <div class="dcard reveal d4">
        <div class="dcard-icon">
          <svg viewBox="0 0 28 28" fill="none"><circle cx="14" cy="11" r="5" stroke="#22304A" stroke-width="1.2"/><path d="M7 25c0-4.5 3-8 7-8s7 3.5 7 8" stroke="#22304A" stroke-width="1.2" stroke-linecap="round"/><path d="M19 8c1-1.5 3.5-1.5 4.5 0.5" stroke="#9AA9B8" stroke-width="1" stroke-linecap="round"/></svg>
        </div>
        <p class="dcard-lbl">Dress Code</p>
        @if(!empty($wedding['dress_code_ladies']) && !empty($wedding['dress_code_gents']))
          <div class="dc-split">
            <div class="dc-gender">
              <p class="dc-gender-lbl">Ladies</p>
              <p class="dcard-main">{{ $wedding['dress_code_ladies'] }}</p>
              @if(!empty($wedding['dress_note_ladies']))<p class="dcard-sub">{{ $wedding['dress_note_ladies'] }}</p>@endif
            </div>
            <div class="dc-divider"></div>
            <div class="dc-gender">
              <p class="dc-gender-lbl">Gents</p>
              <p class="dcard-main">{{ $wedding['dress_code_gents'] }}</p>
              @if(!empty($wedding['dress_note_gents']))<p class="dcard-sub">{{ $wedding['dress_note_gents'] }}</p>@endif
            </div>
          </div>
        @else
          <p class="dcard-main">{{ $wedding['dress_code'] }}</p>
          <p class="dcard-sub">{{ $wedding['dress_note'] }}</p>
        @endif
      </div>
    </div>

    <div class="cal-wrap">
      <a href="{{ route('calendar.ics') }}" class="cal-btn">Add to Calendar</a>
    </div>
  </div>
</div>

<!-- ══════════════ PHOTO BAND 2 ══════════════ -->
<div class="photo-band reveal">
  <img src="/assets/img/image2.jpg">
  <div class="pb-overlay" style="align-items:flex-end;justify-content:center;">
    <div class="pb-text" style="text-align:center;">
      <p class="pb-quote">”Hold me, and never let go.”</p>
      <p class="pb-cite">— Always yours</p>
    </div>
  </div>
</div>


<!-- ══════════════ TIMELINE (ceremony guests only) ══════════════ -->
@if($guest && $guest->attends_ceremony)
<div class="tl-wrap reveal">
  <div class="tl-inner">
    <span class="s-label">The Day</span>
    <h2 class="s-title">Timeline</h2>
    <div class="tl">
      <div class="tl-item">
        <span class="tl-time">4:45 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-person-walking-arrow-right"></i></span>
        <span class="tl-label">Arrival for Ceremony</span>
      </div>
      <div class="tl-item">
        <span class="tl-time">5:00 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-ring"></i></span>
        <span class="tl-label">Wedding Ceremony</span>
      </div>
      <div class="tl-item">
        <span class="tl-time">5:50 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-camera"></i></span>
        <span class="tl-label">Photo Session</span>
      </div>
      <div class="tl-item">
        <span class="tl-time">6:00 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-champagne-glasses"></i></span>
        <span class="tl-label">Wedding Reception</span>
      </div>
      <div class="tl-item">
        <span class="tl-time">6:00 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-music"></i></span>
        <span class="tl-label">Live Music</span>
      </div>
      <div class="tl-item">
        <span class="tl-time">7:30 pm</span>
        <span class="tl-dot"><i class="fa-solid fa-cake-candles"></i></span>
        <span class="tl-label">Cake Cutting</span>
      </div>
    </div>
  </div>
</div>
@endif

<!-- ══════════════ PALETTE ══════════════ -->
<div class="palette-wrap reveal">
  <span class="s-label">Our Palette</span>
  <h2 class="palette-title">Colors of <em>our day</em></h2>
  <div class="palette-rule"></div>
  <p class="palette-note">"Every color has a story — ours is written in these hues."</p>
  <div class="swatches">
    <div class="sw"><div class="sw-c" style="background:#152f4a;"></div><p class="sw-name">Deep Navy</p></div>
    <div class="sw"><div class="sw-c" style="background:#3f6594;"></div><p class="sw-name">Ocean Blue</p></div>
    <div class="sw"><div class="sw-c" style="background:#718db5;"></div><p class="sw-name">Steel Blue</p></div>
    <div class="sw"><div class="sw-c" style="background:#a0c2e8;"></div><p class="sw-name">Powder Blue</p></div>
    <div class="sw"><div class="sw-c" style="background:#c2dcf7;"></div><p class="sw-name">Sky Mist</p></div>
    <div class="sw"><div class="sw-c" style="background:#e1e5e8;"></div><p class="sw-name">Silver Grey</p></div>
    <div class="sw"><div class="sw-c" style="background:#22304A;"></div><p class="sw-name">Navy</p></div>
  </div>
</div>

<!-- ══════════════ GALLERY PREVIEW ══════════════ -->
@if(isset($recentPhotos) && $recentPhotos->isNotEmpty())
<div class="gl-preview-wrap reveal">
  <span class="s-label">Captured Moments</span>
  <h2 class="s-title">Our Gallery</h2>
  <div class="gl-preview-grid">
    @foreach($recentPhotos as $photo)
      <a href="{{ route('gallery') }}" class="gl-preview-item">
        <img src="{{ $photo->thumbUrl() }}" loading="lazy" alt="Gallery preview">
      </a>
    @endforeach
  </div>
  <a href="{{ route('gallery') }}" class="cal-btn">View All Photos &rarr;</a>
</div>
@endif

<!-- ══════════════ PHOTO BAND 3 ══════════════ -->
<div class="photo-band reveal">
  <img src="/assets/img/image3.jpg">
  <div class="pb-overlay">
    <div class="pb-text">
      <p class="pb-quote">"Two souls, one covenant."</p>
      <p class="pb-cite">— May 15, 2026</p>
    </div>
  </div>
</div>

<!-- ══════════════ FOOTER ══════════════ -->
<footer class="reveal">
  <div class="footer-ornament">✦ ✦ ✦</div>
  <p class="footer-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</p>
  <div class="footer-rule"></div>
  <p class="footer-detail">{{ $wedding['date'] }} · {{ $wedding['venue'] }} · {{ $wedding['city'] }}</p>
  <p class="footer-closing">"And so the most beautiful chapter begins."</p>
</footer>

<script>
  // Scroll reveal
  const items = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('in');
      // Stagger timeline items when the tl-wrap comes into view
      if (e.target.classList.contains('tl-wrap')) {
        e.target.querySelectorAll('.tl-item').forEach((item, i) => {
          setTimeout(() => item.classList.add('tl-in'), i * 120);
        });
      }
    });
  }, { threshold: 0.05 });
  items.forEach(el => observer.observe(el));

  // RSVP form
  document.getElementById('rsvp-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn  = document.getElementById('rsvp-btn');
    const msg  = document.getElementById('rsvp-msg');
    const form = this;

    btn.disabled    = true;
    btn.textContent = 'Sending…';
    msg.textContent = '';
    msg.className   = 'rsvp-msg';

    const body = new URLSearchParams({
      _token:     document.querySelector('meta[name="csrf-token"]').content,
      attending:  document.getElementById('rsvp-attending').value,
      plus_ones:  document.getElementById('rsvp-plus').value,
    });

    fetch('{{ route("rsvp.store") }}', { method:'POST', body })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          msg.textContent  = data.message;
          btn.textContent  = 'See you there ♡';
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
        msg.textContent = 'Connection error. Please try again.';
        msg.className   = 'rsvp-msg error';
        btn.disabled    = false;
        btn.textContent = 'Confirm Attendance';
      });
  });
</script>
</body>
</html>
