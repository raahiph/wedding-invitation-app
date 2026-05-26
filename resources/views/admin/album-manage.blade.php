@extends('admin.layout')

@section('title', $album->name)
@section('page-title', $album->name)
@section('page-subtitle', $photos->count() . ' photo' . ($photos->count() === 1 ? '' : 's'))

@section('content')

  {{-- Top actions bar --}}
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
    <a href="{{ route('admin.albums.index') }}"
       style="font-size:11px;color:#6B7E96;text-decoration:none;display:flex;align-items:center;gap:5px;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      All Albums
    </a>

    <div style="width:1px;height:16px;background:#DDE2E8;margin:0 4px;"></div>

    {{-- Rename --}}
    <div style="display:flex;align-items:center;gap:6px;">
      <input type="text" id="rename-input" value="{{ $album->name }}"
             style="font-size:13px;font-weight:500;color:#0F1923;border:1px solid transparent;border-radius:4px;padding:4px 8px;font-family:inherit;background:transparent;min-width:180px;max-width:320px;"
             onblur="renameAlbum()" onkeydown="if(event.key==='Enter'){this.blur();}">
      <button onclick="document.getElementById('rename-input').focus();document.getElementById('rename-input').select();"
              style="background:none;border:none;cursor:pointer;color:#9AA9B8;padding:2px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </button>
    </div>

    {{-- Share link --}}
    <div style="margin-left:auto;display:flex;align-items:center;gap:6px;">
      <input type="text" id="share-url" value="{{ $album->shareUrl() }}"
             style="font-size:10px;color:#6B7E96;border:1px solid #DDE2E8;border-radius:4px;padding:5px 8px;font-family:monospace;background:#F7F9FB;width:260px;"
             readonly onclick="this.select()">
      <button id="copy-btn" onclick="copyLink()"
              style="font-size:11px;padding:5px 14px;border:1px solid #C8D3DE;border-radius:6px;background:#fff;cursor:pointer;color:#3A4F65;font-family:inherit;font-weight:500;white-space:nowrap;">
        Copy Link
      </button>
      <a href="{{ $album->shareUrl() }}" target="_blank"
         style="font-size:11px;padding:5px 12px;border:1px solid #DDE2E8;border-radius:6px;background:#fff;color:#6B7E96;text-decoration:none;white-space:nowrap;">
        Preview
      </a>
    </div>
  </div>

  {{-- Dropbox folder section --}}
  <div class="card" style="margin-bottom:16px;">
    <p class="card-title">Dropbox Folder</p>
    <p style="font-size:11px;color:#6B7E96;margin-bottom:12px;line-height:1.6;">
      Link this album to a subfolder inside your Dropbox app folder (<code style="font-size:10px;background:#F0F2F5;padding:1px 5px;border-radius:3px;">{{ env('DROPBOX_APP_FOLDER') }}</code>).
      Photos, GIFs and videos added to that folder will appear here when you sync.
      Leave empty to use manual upload only.
    </p>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <input type="text" id="dbx-folder-input" value="{{ $album->dropbox_folder ?? '' }}"
             placeholder="/Wedding Day  or  /Albums/Pre-Wedding"
             style="flex:1;min-width:200px;font-size:12px;font-family:monospace;border:1px solid #C8D3DE;border-radius:6px;padding:8px 12px;color:#1A2332;background:#fff;outline:none;">
      <button onclick="saveDropboxFolder()"
              style="font-size:11px;padding:8px 16px;border-radius:6px;cursor:pointer;font-family:inherit;font-weight:500;border:1px solid #C8D3DE;color:#3A4F65;background:#fff;white-space:nowrap;">
        Save Folder
      </button>
      <button id="sync-btn" onclick="syncFromDropbox()"
              style="font-size:11px;padding:8px 18px;border-radius:6px;cursor:pointer;font-family:inherit;font-weight:500;border:1px solid #6EA8D0;color:#2A6496;background:#EAF3FB;white-space:nowrap;">
        ↓ Sync from Dropbox
      </button>
    </div>
    <p id="sync-status" style="font-size:11px;color:#6B7E96;margin-top:8px;display:none;"></p>
  </div>

  {{-- Access --}}
  <div class="card" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
      <div>
        <p class="card-title" style="margin-bottom:3px;">Access</p>
        <p style="font-size:11px;color:#6B7E96;line-height:1.6;">
          When enabled, visitors must verify their mobile number (the wedding gate) before viewing this album.
        </p>
      </div>
      <label class="toggle-switch" style="flex-shrink:0;">
        <input type="checkbox" id="gate-toggle" {{ $album->gated ? 'checked' : '' }} onchange="setAlbumGated(this.checked)">
        <span class="toggle-slider"></span>
      </label>
    </div>
    <p id="gate-status" style="font-size:11px;margin-top:10px;display:none;"></p>
  </div>

  {{-- Upload zone --}}
  <div class="card" id="drop-zone" style="margin-bottom:16px;">
    <div style="text-align:center;padding:20px 16px;">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9AA9B8" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:10px;">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
      </svg>
      <p style="font-size:12px;color:#6B7E96;margin-bottom:3px;font-weight:500;">Drag &amp; drop photos here</p>
      <p style="font-size:11px;color:#9AA9B8;margin-bottom:14px;">JPG · PNG · WebP · GIF · MP4 &nbsp;·&nbsp; up to 200 MB each &nbsp;·&nbsp; max 50 at once</p>
      <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4" multiple style="display:none">
      <button onclick="document.getElementById('photo-input').click()"
              style="font-size:11px;padding:7px 18px;border-radius:6px;cursor:pointer;font-family:inherit;font-weight:500;letter-spacing:0.05em;border:1px solid #C8D3DE;color:#3A4F65;background:#fff;">
        Choose Photos
      </button>
    </div>
  </div>

  {{-- Queue --}}
  <div id="queue" style="display:none;margin-bottom:12px;" class="card">
    <ul id="queue-list" style="list-style:none;display:flex;flex-direction:column;gap:8px;max-width:560px;margin:0 auto;"></ul>
  </div>

  {{-- Group toolbar --}}
  @php
    $groupedMap = [];  // group_key → sequential number
    $gi = 1;
    foreach ($photos->whereNotNull('group_key')->groupBy('group_key') as $key => $_) {
        $groupedMap[$key] = $gi++;
    }
    $groupHues = [210, 145, 270, 30, 180, 340];
  @endphp

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
    <span style="font-size:11px;color:#9AA9B8;">
      @if(count($groupedMap) > 0)
        {{ count($groupedMap) }} group{{ count($groupedMap) === 1 ? '' : 's' }} &nbsp;·&nbsp;
      @endif
      {{ $photos->count() }} photo{{ $photos->count() === 1 ? '' : 's' }}
    </span>
    <button id="sel-toggle" onclick="toggleSelectMode()"
            style="font-size:11px;padding:5px 14px;border-radius:6px;cursor:pointer;font-family:inherit;font-weight:500;border:1px solid #C8D3DE;color:#3A4F65;background:#fff;">
      Select
    </button>
  </div>

  {{-- Photo grid --}}
  @if($photos->isEmpty())
    <p id="empty-msg" style="text-align:center;padding:48px 24px;color:#9AA9B8;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;">
      No photos yet — upload above to get started.
    </p>
  @endif

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;" id="photo-grid">
    @foreach($photos as $photo)
      @php
        $gn  = $photo->group_key ? ($groupedMap[$photo->group_key] ?? null) : null;
        $hue = $gn ? $groupHues[($gn - 1) % count($groupHues)] : null;
      @endphp
      <div class="alb-card" id="aph-{{ $photo->id }}"
           data-id="{{ $photo->id }}"
           data-group-key="{{ $photo->group_key ?? '' }}"
           @if($hue) style="outline:2px solid hsl({{ $hue }},55%,58%);outline-offset:-2px;" @endif>
        <div style="position:relative;aspect-ratio:1/1;overflow:hidden;background:#D1D9E0;">
          {{-- Select overlay --}}
          <div class="sel-overlay" onclick="toggleSelect({{ $photo->id }}, '{{ $photo->group_key ?? '' }}')">
            <div class="sel-check">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
          </div>
          @if($photo->isVideo())
            <video src="{{ $photo->thumbUrl() }}" preload="metadata" muted playsinline
                   style="width:100%;height:100%;object-fit:cover;display:block;"></video>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.25);pointer-events:none;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="white" opacity="0.9"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </div>
          @else
            <img src="{{ $photo->thumbUrl() }}" loading="lazy" alt=""
                 style="width:100%;height:100%;object-fit:cover;display:block;">
            @if($photo->isGif())
              <span style="position:absolute;bottom:6px;left:6px;font-size:9px;font-weight:600;letter-spacing:0.05em;background:rgba(0,0,0,0.55);color:#fff;padding:2px 6px;border-radius:3px;">GIF</span>
            @endif
          @endif
          @if($gn)
            <span class="grp-badge" data-group-key="{{ $photo->group_key }}"
                  style="background:hsl({{ $hue }},55%,92%);color:hsl({{ $hue }},55%,32%);">
              G{{ $gn }}
            </span>
          @endif
        </div>
        <div style="padding:6px 8px;background:#fff;border-top:1px solid #EDF0F4;display:flex;align-items:center;justify-content:space-between;gap:4px;">
          <div style="display:flex;gap:4px;">
            @if($photo->group_key)
              <button onclick="setGroupCover({{ $photo->id }}, this)" class="aph-cover{{ $photo->is_group_cover ? ' aph-cover-active' : '' }}" title="Set as group cover">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="{{ $photo->is_group_cover ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </button>
            @endif
          </div>
          <button onclick="deletePhoto({{ $photo->id }})" class="aph-del">Delete</button>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Floating selection bar --}}
  <div id="sel-bar">
    <span id="sel-count">0 selected</span>
    <button class="btn-sel-group"   onclick="groupSelected()">Group</button>
    <button class="btn-sel-ungroup" onclick="ungroupSelected()">Ungroup</button>
    <button class="btn-sel-delete"  onclick="deleteSelected()">Delete</button>
    <button class="btn-sel-cancel"  onclick="clearSelection()">Cancel</button>
  </div>

@push('scripts')
<style>
.alb-card { border-radius:6px; overflow:hidden; background:#E2E8F0; box-shadow:0 1px 4px rgba(0,0,0,0.07); transition:outline-color 0.15s; }
.aph-del { font-size:10px; padding:2px 8px; border:1px solid #F5C6CB; border-radius:4px; color:#C0392B; background:#FDF2F2; cursor:pointer; }
.aph-cover { padding:3px 5px; border:1px solid #DDE2E8; border-radius:4px; color:#9AA9B8; background:#fff; cursor:pointer; display:flex; align-items:center; }
.aph-cover:hover { border-color:#F0C040; color:#D4A012; }
.aph-cover-active { border-color:#F0C040 !important; color:#D4A012 !important; background:#FFFBEE !important; }
#drop-zone { cursor:default; transition:border-color 0.2s,background 0.2s; }
#drop-zone.dragging { border-color:#6EA8D0 !important; background:#F0F7FD !important; }
.q-item { display:grid; grid-template-columns:1fr auto; column-gap:10px; row-gap:4px; align-items:center; }
.q-name { font-size:10px; font-weight:300; color:#1A2332; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.q-status { font-size:10px; color:#6B7E96; white-space:nowrap; }
.q-status.done  { color:#5B9E6E; }
.q-status.error { color:#E87070; }
.q-bar { grid-column:1/-1; height:2px; background:#E2E8F0; border-radius:2px; overflow:hidden; }
.q-fill { height:100%; width:0%; background:#1A2332; border-radius:2px; transition:width 0.1s linear; }
.q-fill.done { background:#5B9E6E; }

/* ── Selection mode ──────────────────────────────────── */
.sel-overlay {
  display:none; position:absolute; inset:0; z-index:3;
  cursor:pointer; transition:background 0.15s;
}
.body-select .sel-overlay { display:block; }
.alb-card.selected .sel-overlay { background:rgba(42,100,150,0.18); }
.alb-card.selected { outline:2px solid #2A6496 !important; outline-offset:-2px; }

.sel-check {
  position:absolute; top:7px; left:7px;
  width:20px; height:20px; border-radius:5px;
  background:#fff; border:1.5px solid #C8D3DE;
  display:flex; align-items:center; justify-content:center;
  pointer-events:none; transition:background 0.15s, border-color 0.15s;
}
.alb-card.selected .sel-check { background:#2A6496; border-color:#2A6496; }
.sel-check svg { width:11px; height:11px; stroke:white; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; display:none; }
.alb-card.selected .sel-check svg { display:block; }

/* Group badge */
.grp-badge {
  position:absolute; top:7px; right:7px; z-index:2;
  font-size:9px; font-weight:700; letter-spacing:0.04em;
  padding:2px 7px; border-radius:10px; pointer-events:none;
  cursor:pointer;
}
.body-select .grp-badge { pointer-events:auto; cursor:pointer; }

/* Floating bar */
#sel-bar {
  position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
  background:#1A2332; color:#D4E0EC;
  border-radius:10px; padding:10px 16px;
  display:none; align-items:center; gap:8px;
  box-shadow:0 4px 24px rgba(0,0,0,0.22);
  font-size:12px; font-weight:400; z-index:200;
  white-space:nowrap;
}
#sel-bar.visible { display:flex; }
#sel-bar button { font-size:11px; padding:5px 14px; border-radius:6px; cursor:pointer; font-family:inherit; font-weight:500; border:none; }
.btn-sel-group   { background:#EAF3FB; color:#2A6496; }
.btn-sel-ungroup { background:#FDF2F2; color:#C0392B; }
.btn-sel-delete  { background:#C0392B; color:#fff; }
.btn-sel-cancel  { background:rgba(255,255,255,0.1); color:rgba(200,211,222,0.9); }
</style>
<script>
const CSRF      = '{{ csrf_token() }}';
const ALBUM_ID  = {{ $album->id }};
const BASE      = '{{ url("admin/albums") }}/' + ALBUM_ID;
const grid      = document.getElementById('photo-grid');
const queueEl   = document.getElementById('queue');
const queueList = document.getElementById('queue-list');
let activeUploads = 0;
let reloadPending = false;

// ── File picker ──────────────────────────────────────────────────────────────
document.getElementById('photo-input').addEventListener('change', function () {
  const files = Array.from(this.files);
  this.value = '';
  files.forEach(uploadFile);
});

// ── Drag and drop ────────────────────────────────────────────────────────────
const dropZone = document.getElementById('drop-zone');
['dragenter','dragover'].forEach(ev => dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('dragging'); }));
['dragleave','drop'].forEach(ev => dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('dragging'); }));
dropZone.addEventListener('drop', e => {
  const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
  if (files.length) files.forEach(uploadFile);
});

// ── Upload single file ───────────────────────────────────────────────────────
function uploadFile(file) {
  const li = document.createElement('li');
  li.className = 'q-item';
  li.innerHTML = `
    <span class="q-name">${escHtml(file.name)}</span>
    <span class="q-status">0%</span>
    <div class="q-bar"><div class="q-fill"></div></div>`;
  queueList.appendChild(li);

  const statusEl = li.querySelector('.q-status');
  const fillEl   = li.querySelector('.q-fill');

  queueEl.style.display = 'block';
  activeUploads++;

  const form = new FormData();
  form.append('photos[]', file);
  form.append('_token', CSRF);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', BASE + '/photos');

  xhr.upload.addEventListener('progress', e => {
    if (!e.lengthComputable) return;
    const pct = Math.round(e.loaded / e.total * 100);
    fillEl.style.width   = pct + '%';
    statusEl.textContent = pct < 100 ? pct + '%' : 'Processing…';
  });

  xhr.addEventListener('load', () => {
    activeUploads--;
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.ok) {
        fillEl.style.width = '100%';
        fillEl.classList.add('done');
        statusEl.className   = 'q-status done';
        statusEl.textContent = 'Done';
        if (!reloadPending) { reloadPending = true; setTimeout(() => location.reload(), 1400); }
      } else {
        markErr(fillEl, statusEl, 'Failed');
      }
    } catch { markErr(fillEl, statusEl, 'Failed'); }
    checkDone();
  });

  xhr.addEventListener('error', () => { activeUploads--; markErr(fillEl, statusEl, 'Network error'); checkDone(); });
  xhr.send(form);
}

function markErr(f, s, msg) { f.style.width = '100%'; f.style.background = '#E87070'; s.className = 'q-status error'; s.textContent = msg; }

function checkDone() {
  if (activeUploads === 0 && !reloadPending) setTimeout(() => { queueEl.style.display = 'none'; queueList.innerHTML = ''; }, 3000);
}

function escHtml(str) { return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

// ── Set group cover photo (tile shown in album grid) ──────────────────────────
function setGroupCover(photoId, btn) {
  fetch(BASE + '/photos/' + photoId + '/group-cover', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(res => {
      if (!res.ok) return;
      // Find the card and its group key, then clear all group-cover buttons in the same group
      const card = document.getElementById('aph-' + photoId);
      const groupKey = card?.dataset.groupKey;
      if (groupKey) {
        document.querySelectorAll('.alb-card[data-group-key="' + groupKey + '"] .aph-cover[title="Set as group cover"]').forEach(b => {
          b.classList.remove('aph-cover-active');
          b.querySelector('svg').setAttribute('fill', 'none');
        });
      }
      btn.classList.add('aph-cover-active');
      btn.querySelector('svg').setAttribute('fill', 'currentColor');
    });
}

// ── Delete photo ─────────────────────────────────────────────────────────────
function deletePhoto(photoId) {
  if (!confirm('Delete this photo?')) return;
  fetch(BASE + '/photos/' + photoId, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(res => { if (res.ok) document.getElementById('aph-' + photoId)?.remove(); });
}

// ── Rename album ─────────────────────────────────────────────────────────────
let lastSavedName = '{{ addslashes($album->name) }}';
function renameAlbum() {
  const input = document.getElementById('rename-input');
  const name  = input.value.trim();
  if (!name || name === lastSavedName) { input.value = lastSavedName; return; }
  fetch(BASE + '/rename', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ name }),
  }).then(r => r.json()).then(res => {
    if (res.ok) {
      lastSavedName = res.name;
      input.value   = res.name;
      document.title = res.name + ' — Wedding Admin';
      showToast('Album renamed');
    } else {
      input.value = lastSavedName;
    }
  });
}

// ── Album gate ────────────────────────────────────────────────────────────────
function setAlbumGated(enabled) {
  const status = document.getElementById('gate-status');
  fetch(BASE + '/gate', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ gated: enabled }),
  }).then(r => r.json()).then(res => {
    if (res.ok) {
      status.style.display = 'block';
      status.style.color   = '#5B9E6E';
      status.textContent   = res.gated ? '✓ Gate enabled — mobile verification required' : '✓ Gate disabled — album is public';
    }
  });
}

// ── Dropbox folder ────────────────────────────────────────────────────────────
function saveDropboxFolder() {
  const folder = document.getElementById('dbx-folder-input').value.trim();
  fetch(BASE + '/dropbox', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ dropbox_folder: folder }),
  }).then(r => r.json()).then(res => {
    if (res.ok) {
      document.getElementById('sync-btn').disabled = !res.dropbox_folder;
      showToast(folder ? 'Dropbox folder saved' : 'Dropbox folder cleared');
    }
  });
}

function syncFromDropbox() {
  const btn    = document.getElementById('sync-btn');
  const status = document.getElementById('sync-status');
  btn.disabled    = true;
  btn.textContent = '↓ Syncing…';
  status.style.display = 'block';
  status.style.color   = '#6B7E96';
  status.textContent   = 'Connecting to Dropbox…';

  fetch(BASE + '/sync', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(res => {
      if (res.ok) {
        const folder = res.folder_used ? ' (folder: ' + res.folder_used + ')' : '';
        if (res.added > 0) {
          status.textContent = `✓ ${res.added} new file${res.added !== 1 ? 's' : ''} added` +
            (res.errors ? ` · ${res.errors} failed` : '') + folder;
          status.style.color = '#5B9E6E';
          setTimeout(() => location.reload(), 1400);
        } else if (res.errors > 0) {
          status.textContent = `✗ 0 added, ${res.errors} failed${folder}\n` +
            (res.error_detail || []).join('\n');
          status.style.color  = '#C0392B';
          status.style.whiteSpace = 'pre-wrap';
          btn.disabled = false; btn.textContent = '↓ Sync from Dropbox';
        } else if (res.files_found === 0) {
          status.textContent = `No supported files found in Dropbox${folder}` +
            (res.total_files > 0 ? ` (${res.total_files} unsupported files present)` : '');
          status.style.color = '#C0392B';
          btn.disabled = false; btn.textContent = '↓ Sync from Dropbox';
        } else {
          status.textContent = `✓ Already up to date — ${res.files_found} file${res.files_found !== 1 ? 's' : ''} in Dropbox${folder}`;
          status.style.color = '#5B9E6E';
          btn.disabled = false; btn.textContent = '↓ Sync from Dropbox';
        }
      } else {
        status.textContent = '✗ ' + (res.message || 'Sync failed') +
          (res.folder_used ? '\nFolder: ' + res.folder_used : '');
        status.style.color      = '#C0392B';
        status.style.whiteSpace = 'pre-wrap';
        btn.disabled = false; btn.textContent = '↓ Sync from Dropbox';
      }
    })
    .catch(err => {
      status.textContent = '✗ Network error: ' + err;
      status.style.color = '#C0392B';
      btn.disabled = false; btn.textContent = '↓ Sync from Dropbox';
    });
}

// ── Selection / grouping ──────────────────────────────────────────────────────
let selectMode = false;
const selectedIds = new Set();

function toggleSelectMode() {
  selectMode = !selectMode;
  document.body.classList.toggle('body-select', selectMode);
  document.getElementById('sel-toggle').textContent = selectMode ? 'Done' : 'Select';
  document.getElementById('sel-toggle').style.background = selectMode ? '#EAF3FB' : '';
  document.getElementById('sel-toggle').style.color      = selectMode ? '#2A6496' : '';
  if (!selectMode) clearSelection();
}

function toggleSelect(id, groupKey) {
  if (!selectMode) return;
  const card = document.getElementById('aph-' + id);
  if (selectedIds.has(id)) {
    selectedIds.delete(id);
    card.classList.remove('selected');
  } else {
    selectedIds.add(id);
    card.classList.add('selected');
  }
  updateSelBar();
}

function updateSelBar() {
  const bar   = document.getElementById('sel-bar');
  const count = selectedIds.size;
  document.getElementById('sel-count').textContent = count + ' selected';
  bar.classList.toggle('visible', count > 0);

  // Group button enabled only if ≥2 selected
  document.querySelector('.btn-sel-group').disabled = count < 2;
}

function clearSelection() {
  selectedIds.forEach(id => document.getElementById('aph-' + id)?.classList.remove('selected'));
  selectedIds.clear();
  updateSelBar();
}

async function groupSelected() {
  if (selectedIds.size < 2) return;
  const res = await postJson(BASE + '/photos/group', { ids: Array.from(selectedIds) });
  if (res.ok) location.reload();
}

async function ungroupSelected() {
  if (!selectedIds.size) return;
  const res = await postJson(BASE + '/photos/ungroup', { ids: Array.from(selectedIds) });
  if (res.ok) location.reload();
}

async function deleteSelected() {
  if (!selectedIds.size) return;
  if (!confirm('Delete ' + selectedIds.size + ' photo' + (selectedIds.size === 1 ? '' : 's') + '? This cannot be undone.')) return;
  const res = await postJson(BASE + '/photos/bulk-delete', { ids: Array.from(selectedIds) });
  if (res.ok) {
    selectedIds.forEach(id => document.getElementById('aph-' + id)?.remove());
    clearSelection();
    toggleSelectMode();
  }
}

// Clicking a group badge in select mode selects all photos in that group
document.querySelectorAll('.grp-badge[data-group-key]').forEach(el => {
  el.addEventListener('click', function(e) {
    if (!selectMode) return;
    e.stopPropagation();
    const key = this.dataset.groupKey;
    document.querySelectorAll('.alb-card[data-group-key="' + key + '"]').forEach(card => {
      const id = parseInt(card.dataset.id);
      selectedIds.add(id);
      card.classList.add('selected');
    });
    updateSelBar();
  });
});

function postJson(url, data) {
  return fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify(data),
  }).then(r => r.json());
}

// ── Copy share link ───────────────────────────────────────────────────────────
function copyLink() {
  const url = document.getElementById('share-url').value;
  const btn = document.getElementById('copy-btn');
  navigator.clipboard.writeText(url).then(() => {
    btn.textContent = 'Copied!';
    btn.style.color = '#5B9E6E';
    btn.style.borderColor = '#5B9E6E';
    setTimeout(() => { btn.textContent = 'Copy Link'; btn.style.color = ''; btn.style.borderColor = ''; }, 2200);
  });
}
</script>
@endpush

@endsection
