<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') — Wedding Admin</title>
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/32x32.png">
<link rel="icon" type="image/png" sizes="180x180" href="/assets/img/180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/img/192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/assets/img/512x512.png">
<style>
@font-face { font-family:'BrittanySignature'; src:url('/assets/fonts/BrittanySignature.ttf') format('truetype'); font-weight:400; font-display:swap; }
@font-face { font-family:'Inter'; src:local('Inter'), local('Inter-Regular'); font-weight:400; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Light.ttf') format('truetype'); font-weight:300; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Regular.ttf') format('truetype'); font-weight:400; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-Medium.ttf') format('truetype'); font-weight:500; font-display:swap; }
@font-face { font-family:'Montserrat'; src:url('/assets/fonts/Montserrat-SemiBold.ttf') format('truetype'); font-weight:600; font-display:swap; }

*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
html, body { height:100%; }
body { font-family:'Montserrat',system-ui,sans-serif; background:#F0F2F5; color:#1A2332; font-size:13px; display:flex; min-height:100vh; }

/* ─── Sidebar ─────────────────────────────────── */
.sidebar {
  position:fixed; top:0; left:0; bottom:0; width:230px;
  background:#1A2332;
  border-right:1px solid rgba(0,0,0,0.15);
  display:flex; flex-direction:column;
  z-index:200; transition:transform 0.28s ease;
}

.sidebar-brand {
  padding:24px 20px 20px;
  border-bottom:1px solid rgba(255,255,255,0.07);
}
.brand-names {
  font-family:'BrittanySignature',cursive;
  font-size:22px; color:#FFFFFF; display:block; line-height:1; margin-bottom:6px;
}
.brand-badge {
  font-size:9px; letter-spacing:0.35em; text-transform:uppercase;
  color:#6B7E96; display:block; font-weight:400;
}

.sidebar-nav {
  flex:1; padding:12px 0; display:flex; flex-direction:column; overflow-y:auto;
}
.nav-section-label {
  font-size:9px; letter-spacing:0.35em; text-transform:uppercase;
  color:#4A5C70; padding:14px 20px 5px; display:block; font-weight:500;
}
.nav-link {
  display:flex; align-items:center; gap:10px;
  padding:10px 20px;
  color:#8FA3B8; text-decoration:none;
  font-size:12px; font-weight:400; letter-spacing:0.03em;
  border-left:2px solid transparent;
  transition:color 0.15s, background 0.15s, border-color 0.15s;
  white-space:nowrap;
}
.nav-link svg { width:15px; height:15px; flex-shrink:0; stroke:currentColor; fill:none; stroke-width:1.6; stroke-linecap:round; stroke-linejoin:round; }
.nav-link:hover { color:#D4E0EC; background:rgba(255,255,255,0.06); border-left-color:rgba(255,255,255,0.15); }
.nav-link.active { color:#FFFFFF; background:rgba(255,255,255,0.09); border-left-color:#6EA8D0; font-weight:500; }

.sidebar-spacer { flex:1; }

.sidebar-bottom {
  border-top:1px solid rgba(255,255,255,0.07);
  padding:8px 0;
}
.nav-link.nav-logout { color:#5A6E82; }
.nav-link.nav-logout:hover { color:#F08080; background:rgba(240,128,128,0.07); border-left-color:rgba(240,128,128,0.3); }

/* ─── Main ────────────────────────────────────── */
.admin-wrap { display:flex; width:100%; min-height:100vh; }
.admin-main { margin-left:230px; flex:1; display:flex; flex-direction:column; min-height:100vh; max-width:calc(100vw - 230px); }

/* Mobile hamburger */
.topbar { display:none; align-items:center; gap:14px; padding:14px 20px; background:#1A2332; border-bottom:1px solid rgba(0,0,0,0.15); position:sticky; top:0; z-index:100; }
.hamburger { background:none; border:none; cursor:pointer; padding:4px; color:#8FA3B8; }
.hamburger svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:1.8; stroke-linecap:round; }
.topbar-brand { font-family:'BrittanySignature',cursive; font-size:18px; color:#FFFFFF; }
.sidebar-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:150; }

/* Page structure */
.page-header {
  padding:32px 40px 24px;
  background:#F0F2F5;
  border-bottom:1px solid #DDE2E8;
}
.page-header h1 {
  font-size:20px; font-weight:500; letter-spacing:0.01em; color:#0F1923; margin-bottom:4px;
}
.page-header p { font-size:12px; color:#6B7E96; font-weight:400; }

.page-body { padding:28px 40px 52px; flex:1; }

/* ─── Stats ────────────────────────────────────── */
.stats { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.stat {
  background:#FFFFFF; border:1px solid #DDE2E8;
  padding:18px 24px; border-radius:8px; text-align:center; min-width:110px;
}
.stat strong { display:block; font-size:28px; font-weight:600; color:#0F1923; line-height:1; margin-bottom:5px; }
.stat span { font-size:10px; letter-spacing:0.15em; color:#6B7E96; text-transform:uppercase; font-weight:400; }
.stat-groom { border-color:#C3D4F5; background:#EAF0FB; }
.stat-groom strong { color:#2A4E96; }
.stat-bride { border-color:#F0C8DC; background:#FBF0F5; }
.stat-bride strong { color:#9C2A5A; }

/* ─── Card ─────────────────────────────────────── */
.card {
  background:#FFFFFF; border:1px solid #DDE2E8;
  border-radius:8px; padding:22px 24px; margin-bottom:16px;
}
.card-title { font-size:11px; letter-spacing:0.12em; text-transform:uppercase; color:#4A5C70; font-weight:600; margin-bottom:16px; }

/* ─── Mode toggle card ──────────────────────────── */
.mode-card {
  display:flex; align-items:center; justify-content:space-between; gap:16px;
  background:#FFFFFF; border:1px solid #DDE2E8;
  border-radius:8px; padding:16px 24px; margin-bottom:16px;
}
.mode-card.active { border-color:#6EA8D0; background:#F0F7FD; }
.mode-label { font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#4A5C70; font-weight:600; margin-bottom:4px; }
.mode-desc { font-size:12px; color:#6B7E96; font-weight:400; }

.toggle-form { flex-shrink:0; }
.toggle-switch { position:relative; display:inline-block; width:46px; height:24px; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
.toggle-slider { position:absolute; inset:0; background:#CBD5E0; border-radius:24px; transition:0.25s; }
.toggle-slider::before { content:''; position:absolute; width:16px; height:16px; left:4px; top:4px; background:#FFFFFF; border-radius:50%; transition:0.25s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
input:checked + .toggle-slider { background:#6EA8D0; }
input:checked + .toggle-slider::before { transform:translateX(22px); }

/* ─── Forms ─────────────────────────────────────── */
.form-row { display:flex; gap:8px; flex-wrap:wrap; }
.form-input {
  flex:1; min-width:130px;
  background:#FFFFFF; border:1px solid #C8D3DE;
  color:#1A2332; font-family:'Montserrat',sans-serif; font-size:13px; font-weight:400;
  padding:9px 12px; border-radius:6px; outline:none; transition:border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus { border-color:#6EA8D0; box-shadow:0 0 0 3px rgba(110,168,208,0.15); }
.form-input::placeholder { color:#A8B8C8; }
.form-btn {
  background:#1A2332; border:1px solid #1A2332;
  color:#FFFFFF; font-family:'Montserrat',sans-serif; font-size:11px; font-weight:500;
  letter-spacing:0.08em; padding:9px 18px; cursor:pointer; border-radius:6px;
  text-transform:uppercase; white-space:nowrap; transition:background 0.2s, border-color 0.2s;
}
.form-btn:hover { background:#2C3E54; border-color:#2C3E54; }

/* ─── Flash ──────────────────────────────────────── */
.flash { margin-top:12px; font-size:12px; color:#2E7D52; font-weight:400; }
.flash.err { color:#C0392B; }

/* ─── Table filters ─────────────────────────────── */
.tbl-filters {
  display:flex; align-items:center; gap:10px; margin-bottom:10px; flex-wrap:wrap;
}
.tbl-search { flex:1; min-width:180px; max-width:320px; }
.tbl-select { flex:0 0 auto; width:auto; min-width:150px; cursor:pointer; }
.tbl-count { font-size:11px; color:#8FA3B8; font-weight:400; margin-left:auto; white-space:nowrap; }

/* ─── Table ──────────────────────────────────────── */
.tbl-wrap { overflow-x:auto; border-radius:8px; border:1px solid #DDE2E8; background:#FFFFFF; }
table { width:100%; border-collapse:collapse; font-size:13px; }
thead { background:#F7F9FB; }
th {
  text-align:left; font-size:10px; letter-spacing:0.12em; text-transform:uppercase;
  color:#6B7E96; padding:12px 16px; border-bottom:1px solid #DDE2E8;
  font-weight:600; white-space:nowrap;
}
td { padding:12px 16px; border-bottom:1px solid #EDF0F4; vertical-align:middle; color:#3A4F65; font-weight:400; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#F7F9FB; }
.td-hi { color:#0F1923; font-weight:500; }
.td-dim { color:#8FA3B8; font-size:12px; }

/* ─── Badges ──────────────────────────────────────── */
.badge { display:inline-block; font-size:10px; padding:3px 10px; border-radius:20px; letter-spacing:0.05em; font-weight:500; }
.badge-yes     { background:#E6F5EC; color:#2E7D52; }
.badge-no      { background:#FDECEC; color:#C0392B; }
.badge-pending { background:#EEF2F7; color:#6B7E96; }
.side-groom    { background:#EAF0FB; color:#2A4E96; }
.side-bride    { background:#FBF0F5; color:#9C2A5A; }
.side-other    { background:#F3F4F6; color:#6B7280; }

/* ─── Ceremony btn ───────────────────────────────── */
.ceremony-btn {
  background:none; border:1px solid; font-family:'Montserrat',sans-serif;
  font-size:10px; font-weight:500; padding:3px 10px; border-radius:20px;
  cursor:pointer; transition:opacity 0.15s;
}
.ceremony-btn:hover { opacity:0.7; }
.ceremony-yes { color:#2E7D52; border-color:#2E7D52; background:#E6F5EC; }
.ceremony-no  { color:#6B7E96; border-color:#C8D3DE; background:#EEF2F7; }

/* ─── Edit btn ───────────────────────────────────── */
.edit-btn { background:none; border:1px solid #C8D3DE; color:#3A4F65; font-size:11px; font-family:'Montserrat',sans-serif; font-weight:500; padding:3px 10px; border-radius:6px; cursor:pointer; transition:border-color 0.2s, color 0.2s; letter-spacing:0.03em; }
.edit-btn:hover { border-color:#6EA8D0; color:#1A2332; }

/* ─── Modal ──────────────────────────────────────── */
.modal-backdrop {
  position:fixed; inset:0; background:rgba(15,25,35,0.45);
  display:flex; align-items:center; justify-content:center;
  z-index:500; padding:20px;
}
.modal {
  background:#FFFFFF; border-radius:10px; border:1px solid #DDE2E8;
  width:100%; max-width:440px; box-shadow:0 12px 40px rgba(0,0,0,0.15);
  display:flex; flex-direction:column;
}
.modal-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:18px 22px 16px; border-bottom:1px solid #EDF0F4;
}
.modal-title { font-size:12px; font-weight:600; color:#0F1923; letter-spacing:0.04em; }
.modal-subtitle { font-size:11px; color:#6B7E96; margin-top:2px; font-weight:400; }
.modal-close {
  background:none; border:none; cursor:pointer; color:#8FA3B8;
  font-size:16px; line-height:1; padding:2px 4px;
  transition:color 0.15s;
}
.modal-close:hover { color:#1A2332; }
.modal-body { padding:20px 22px; display:flex; flex-direction:column; gap:14px; }
.modal-footer { padding:14px 22px 18px; display:flex; align-items:center; gap:14px; border-top:1px solid #EDF0F4; }
.modal-msg { font-size:11px; color:#C0392B; font-weight:400; }

/* ─── Delete btn ──────────────────────────────────── */
.del-btn { background:none; border:none; color:#C0392B; font-size:11px; cursor:pointer; opacity:0.4; font-family:'Montserrat',sans-serif; letter-spacing:0.05em; font-weight:400; padding:0; transition:opacity 0.2s; }
.del-btn:hover { opacity:1; }

/* ─── Settings form ───────────────────────────────── */
.s-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.s-field { margin-bottom:0; }
.s-field label { display:block; font-size:10px; letter-spacing:0.1em; text-transform:uppercase; color:#4A5C70; font-weight:600; margin-bottom:7px; }
.s-field .hint { font-size:11px; color:#8FA3B8; margin-top:5px; font-weight:400; }
.s-save {
  background:#1A2332; border:1px solid #1A2332;
  color:#FFFFFF; font-family:'Montserrat',sans-serif; font-size:11px; font-weight:500;
  letter-spacing:0.1em; padding:11px 28px; cursor:pointer; border-radius:6px;
  text-transform:uppercase; transition:background 0.2s;
}
.s-save:hover { background:#2C3E54; border-color:#2C3E54; }
.s-saved { margin-top:14px; font-size:12px; color:#2E7D52; font-weight:400; }

/* ─── Responsive ──────────────────────────────────── */
@media(max-width:768px) {
  .sidebar { transform:translateX(-100%); }
  .sidebar.open { transform:translateX(0); }
  .sidebar-backdrop.visible { display:block; }
  .admin-main { margin-left:0; max-width:100vw; }
  .topbar { display:flex; }
  .page-header { padding:22px 20px 18px; }
  .page-body { padding:20px 20px 40px; }
  .s-grid2 { grid-template-columns:1fr; }
}
</style>
</head>
<body>

<div class="admin-wrap">

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <span class="brand-names">{{ $wedding['groom'] }} &amp;&nbsp; {{ $wedding['bride'] }}</span>
      <span class="brand-badge">Admin Panel</span>
    </div>

    <nav class="sidebar-nav">
      <span class="nav-section-label">Manage</span>

      <a href="{{ route('admin.index') }}"
         class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>

      <a href="{{ route('admin.gallery') }}"
         class="nav-link {{ request()->routeIs('admin.gallery') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Gallery
      </a>

      <span class="nav-section-label">Configuration</span>

      <a href="{{ route('admin.settings') }}"
         class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        Settings
      </a>

      <div class="sidebar-spacer"></div>
    </nav>

    <div class="sidebar-bottom">
      <a href="{{ route('admin.logout') }}" class="nav-link nav-logout">
        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <div class="sidebar-backdrop" id="backdrop"></div>

  <!-- Main -->
  <main class="admin-main">

    <!-- Mobile top bar -->
    <div class="topbar">
      <button class="hamburger" id="menu-btn" aria-label="Menu">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <span class="topbar-brand">{{ $wedding['groom'] }} & {{ $wedding['bride'] }}</span>
    </div>

    <div class="page-header">
      <h1>@yield('page-title')</h1>
      <p>@yield('page-subtitle')</p>
    </div>

    <div class="page-body">
      @yield('content')
    </div>

  </main>
</div>

<script>
  const sidebar   = document.getElementById('sidebar');
  const backdrop  = document.getElementById('backdrop');
  const menuBtn   = document.getElementById('menu-btn');

  function openSidebar() {
    sidebar.classList.add('open');
    backdrop.classList.add('visible');
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    backdrop.classList.remove('visible');
  }

  menuBtn.addEventListener('click', openSidebar);
  backdrop.addEventListener('click', closeSidebar);
</script>

@stack('scripts')
</body>
</html>
