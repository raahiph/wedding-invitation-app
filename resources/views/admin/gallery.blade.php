@extends('admin.layout')

@section('title', 'Gallery')
@section('page-title', 'Gallery')
@section('page-subtitle', $photos->count() . ' photo' . ($photos->count() === 1 ? '' : 's') . ' total')

@section('content')

  {{-- Toolbar --}}
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
    <button id="select-btn" onclick="toggleSelectMode()">Select</button>
    <button id="select-all-btn" onclick="toggleAll()" style="display:none;">Select all</button>
    <span   id="bulk-count" style="display:none;font-size:12px;color:#C0392B;font-weight:500;"></span>
    <button id="delete-sel-btn" onclick="deleteSelected()" style="display:none;">Delete selected</button>
    <button id="sync-btn" onclick="syncDropbox()" style="margin-left:auto;">↓ Sync Dropbox</button>
  </div>

  @if($photos->isEmpty())
    <div class="card" style="text-align:center;padding:60px 24px;" id="gp-empty">
      <p style="font-size:12px;color:#8FA3B8;letter-spacing:0.1em;text-transform:uppercase;">No photos yet. Sync from Dropbox or share the gallery link.</p>
      <a href="{{ route('gallery') }}" target="_blank"
         style="display:inline-block;margin-top:16px;font-size:11px;color:#6EA8D0;text-decoration:none;letter-spacing:0.05em;">
        Open guest gallery →
      </a>
    </div>
  @endif

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;" id="admin-gallery-grid">

    @foreach($photos as $photo)
      <div class="gp-card" id="gp-{{ $photo->id }}" data-id="{{ $photo->id }}"
           onclick="cardClick(this, {{ $photo->id }})">
        <div class="gp-check">
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
            <polyline points="2,5 4,7.5 8,2.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <img src="{{ $photo->thumbUrl() }}" loading="lazy" alt=""
             style="width:100%;aspect-ratio:1/1;object-fit:cover;display:block;">
        <div class="gp-info">
          <p class="gp-name">{{ $photo->guest?->name ?? 'File Request' }}</p>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
            <span class="gp-date">{{ $photo->created_at->format('d M, H:i') }}</span>
            <button class="gp-del-btn" onclick="deletePhoto(event, {{ $photo->id }})">Delete</button>
          </div>
        </div>
      </div>
    @endforeach

  </div>

@push('scripts')
<style>
.gp-card {
  position:relative; border-radius:6px; overflow:hidden;
  background:#E2E8F0; box-shadow:0 1px 4px rgba(0,0,0,0.07);
}
.gp-card.selected { outline:2px solid #C0392B; }
.select-mode .gp-card { cursor:pointer; }
.select-mode .gp-del-btn { pointer-events:none; }

.gp-check {
  display:none; position:absolute; top:8px; left:8px; z-index:3;
  width:20px; height:20px; border-radius:50%;
  background:#CBD5E1; border:2px solid #CBD5E1;
  align-items:center; justify-content:center; pointer-events:none;
}
.select-mode .gp-check { display:flex; }
.gp-card.selected .gp-check { background:#C0392B; border-color:#C0392B; }

.gp-info { padding:8px 10px; background:#fff; border-top:1px solid #EDF0F4; }
.gp-name { font-size:10px; color:#6B7E96; margin-bottom:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gp-date { font-size:10px; color:#A8B8C8; }
.gp-del-btn {
  font-size:10px; padding:2px 8px; border:1px solid #F5C6CB;
  border-radius:4px; color:#C0392B; background:#FDF2F2; cursor:pointer;
}

#select-btn {
  font-size:11px; padding:6px 16px; border-radius:6px; cursor:pointer;
  font-family:inherit; font-weight:500; letter-spacing:0.05em;
  border:1px solid #C8D3DE; color:#3A4F65; background:#fff;
}
#select-btn.active { border-color:#C0392B; color:#C0392B; background:#FDF2F2; }
#select-all-btn, #delete-sel-btn {
  font-size:11px; padding:6px 16px; border-radius:6px; cursor:pointer; font-family:inherit;
}
#select-all-btn { border:1px solid #C8D3DE; color:#3A4F65; background:#fff; }
#delete-sel-btn { border:1px solid #C0392B; color:#fff; background:#C0392B; font-weight:500; }
#sync-btn {
  font-size:11px; padding:6px 16px; border-radius:6px; cursor:pointer;
  font-family:inherit; font-weight:500;
  border:1px solid #6EA8D0; color:#2A6496; background:#EAF3FB;
}
#sync-btn:disabled { opacity:0.6; cursor:not-allowed; }
</style>
<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("admin/gallery") }}';
const grid = document.getElementById('admin-gallery-grid');

let selectMode  = false;
let allSelected = false;
const selSet    = new Set(); // numeric photo IDs

// ── Select mode ───────────────────────────────────────────
function toggleSelectMode() {
  selectMode ? exitSelectMode() : enterSelectMode();
}
function enterSelectMode() {
  selectMode = true;
  grid.classList.add('select-mode');
  document.getElementById('select-btn').classList.add('active');
  document.getElementById('select-btn').textContent = 'Cancel';
  document.getElementById('select-all-btn').style.display = '';
  updateToolbar();
}
function exitSelectMode() {
  selectMode  = false;
  allSelected = false;
  selSet.clear();
  grid.classList.remove('select-mode');
  document.getElementById('select-btn').classList.remove('active');
  document.getElementById('select-btn').textContent = 'Select';
  document.getElementById('select-all-btn').style.display  = 'none';
  document.getElementById('select-all-btn').textContent    = 'Select all';
  document.getElementById('delete-sel-btn').style.display  = 'none';
  document.getElementById('bulk-count').style.display      = 'none';
  grid.querySelectorAll('.gp-card').forEach(c => c.classList.remove('selected'));
}

function cardClick(card, id) {
  if (!selectMode) return;
  if (selSet.has(id)) {
    selSet.delete(id);
    card.classList.remove('selected');
  } else {
    selSet.add(id);
    card.classList.add('selected');
  }
  updateToolbar();
}

function toggleAll() {
  allSelected = !allSelected;
  grid.querySelectorAll('.gp-card[data-id]').forEach(card => {
    const id = Number(card.dataset.id);
    if (allSelected) { selSet.add(id); card.classList.add('selected'); }
    else             { selSet.delete(id); card.classList.remove('selected'); }
  });
  document.getElementById('select-all-btn').textContent = allSelected ? 'Deselect all' : 'Select all';
  updateToolbar();
}

function updateToolbar() {
  document.getElementById('bulk-count').textContent   = selSet.size + ' selected';
  document.getElementById('bulk-count').style.display = selSet.size ? '' : 'none';
  document.getElementById('delete-sel-btn').style.display = selSet.size ? '' : 'none';
}

// ── Bulk delete ───────────────────────────────────────────
function deleteSelected() {
  if (!selSet.size) return;
  if (!confirm('Delete ' + selSet.size + ' photo' + (selSet.size > 1 ? 's' : '') + '?')) return;
  Promise.all([...selSet].map(id =>
    fetch(BASE + '/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
      .then(r => r.json()).then(res => res.ok ? id : null).catch(() => null)
  )).then(results => {
    results.filter(Boolean).forEach(id => document.getElementById('gp-' + id)?.remove());
    exitSelectMode();
    updateSubtitle();
  });
}

// ── Single delete ─────────────────────────────────────────
function deletePhoto(event, id) {
  if (selectMode) return;
  event.stopPropagation();
  if (!confirm('Delete this photo?')) return;
  fetch(BASE + '/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
    .then(r => r.json())
    .then(res => { if (res.ok) { document.getElementById('gp-' + id)?.remove(); updateSubtitle(); } });
}

function updateSubtitle() {
  const n   = grid.querySelectorAll('.gp-card').length;
  const sub = document.querySelector('.page-header p');
  if (sub) sub.textContent = n + ' photo' + (n === 1 ? '' : 's') + ' total';
}

// ── Sync from Dropbox ─────────────────────────────────────
function syncDropbox() {
  const btn = document.getElementById('sync-btn');
  btn.disabled    = true;
  btn.textContent = '↓ Syncing…';

  fetch(BASE + '/sync', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
    .then(r => r.json())
    .then(res => {
      if (res.ok && res.added > 0) {
        // Reload page so new photos appear in the grid
        location.reload();
      } else if (res.ok) {
        btn.textContent = '✓ Up to date';
        setTimeout(() => { btn.disabled = false; btn.textContent = '↓ Sync Dropbox'; }, 2500);
      } else {
        btn.textContent = '✗ Error';
        setTimeout(() => { btn.disabled = false; btn.textContent = '↓ Sync Dropbox'; }, 2500);
      }
    })
    .catch(() => {
      btn.disabled    = false;
      btn.textContent = '↓ Sync Dropbox';
    });
}
</script>
@endpush

@endsection
