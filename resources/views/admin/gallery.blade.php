@extends('admin.layout')

@section('title', 'Gallery')
@section('page-title', 'Gallery')
@section('page-subtitle', $photos->count() . ' photo' . ($photos->count() === 1 ? '' : 's') . ' uploaded')

@section('content')

@if($photos->isEmpty())
  <div class="card" style="text-align:center;padding:60px 24px;">
    <p style="font-size:12px;color:#8FA3B8;letter-spacing:0.1em;text-transform:uppercase;">No photos uploaded yet.</p>
    <a href="{{ route('gallery') }}" target="_blank"
       style="display:inline-block;margin-top:16px;font-size:11px;color:#6EA8D0;text-decoration:none;letter-spacing:0.05em;">
      Open guest gallery →
    </a>
  </div>
@else

  {{-- Bulk action toolbar --}}
  <div id="bulk-bar" style="display:none;align-items:center;gap:12px;margin-bottom:16px;padding:10px 16px;background:#FDF2F2;border:1px solid #F5C6CB;border-radius:6px;">
    <span id="bulk-count" style="font-size:12px;color:#C0392B;font-weight:500;"></span>
    <button onclick="deleteSelected()" style="font-size:11px;padding:5px 14px;border:1px solid #C0392B;border-radius:4px;color:#fff;background:#C0392B;cursor:pointer;">
      Delete selected
    </button>
    <button onclick="clearSelection()" style="font-size:11px;padding:5px 14px;border:1px solid #CBD5E1;border-radius:4px;color:#6B7E96;background:#fff;cursor:pointer;">
      Cancel
    </button>
    <label style="margin-left:auto;font-size:11px;color:#C0392B;cursor:pointer;display:flex;align-items:center;gap:6px;">
      <input type="checkbox" id="select-all" onchange="toggleAll(this.checked)"> Select all
    </label>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;" id="admin-gallery-grid">
    @foreach($photos as $photo)
      <div style="position:relative;border-radius:6px;overflow:hidden;background:#E2E8F0;box-shadow:0 1px 4px rgba(0,0,0,0.07);"
           id="gp-{{ $photo->id }}" class="gp-card" data-id="{{ $photo->id }}">

        {{-- Selection overlay --}}
        <div class="gp-select-overlay" onclick="toggleSelect({{ $photo->id }})"
             style="position:absolute;inset:0;z-index:2;cursor:pointer;display:none;"></div>
        <div class="gp-check" id="gp-check-{{ $photo->id }}"
             style="position:absolute;top:8px;left:8px;z-index:3;width:20px;height:20px;border-radius:50%;
                    background:#fff;border:2px solid #CBD5E1;display:none;align-items:center;justify-content:center;pointer-events:none;">
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
            <polyline points="2,5 4,7.5 8,2.5" stroke="#C0392B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <img src="{{ $photo->thumbUrl() }}" loading="lazy" alt=""
             style="width:100%;aspect-ratio:1/1;object-fit:cover;display:block;">
        <div style="padding:8px 10px;background:#fff;border-top:1px solid #EDF0F4;">
          <p style="font-size:10px;color:#6B7E96;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            {{ $photo->guest?->name ?: 'Anonymous' }}
          </p>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
            <span style="font-size:10px;color:#A8B8C8;">{{ $photo->created_at->format('d M, H:i') }}</span>
            <button onclick="deletePhoto({{ $photo->id }})" class="del-btn"
                    style="font-size:10px;padding:2px 8px;border:1px solid #F5C6CB;border-radius:4px;color:#C0392B;background:#FDF2F2;cursor:pointer;">
              Delete
            </button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

@push('scripts')
<style>
.gp-card.selected { outline:2px solid #C0392B; }
.gp-card.selected .gp-check { background:#FDF2F2 !important; border-color:#C0392B !important; }
</style>
<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("admin/gallery") }}';
let selected = new Set();
let selectMode = false;

// ── Long-press to enter select mode ──────────────────────
document.querySelectorAll('.gp-card').forEach(card => {
  let timer;
  card.addEventListener('pointerdown', () => {
    timer = setTimeout(() => enterSelectMode(Number(card.dataset.id)), 500);
  });
  card.addEventListener('pointerup',   () => clearTimeout(timer));
  card.addEventListener('pointerleave',() => clearTimeout(timer));
});

function enterSelectMode(id) {
  selectMode = true;
  document.querySelectorAll('.gp-select-overlay').forEach(el => el.style.display = 'block');
  document.querySelectorAll('.gp-check').forEach(el => el.style.display = 'flex');
  toggleSelect(id);
}

function toggleSelect(id) {
  if (!selectMode) return;
  const card  = document.getElementById('gp-' + id);
  const check = document.getElementById('gp-check-' + id);
  if (selected.has(id)) {
    selected.delete(id);
    card.classList.remove('selected');
    check.style.background   = '#fff';
    check.style.borderColor  = '#CBD5E1';
  } else {
    selected.add(id);
    card.classList.add('selected');
    check.style.background   = '#FDF2F2';
    check.style.borderColor  = '#C0392B';
  }
  updateBulkBar();
}

function toggleAll(checked) {
  document.querySelectorAll('.gp-card').forEach(card => {
    const id = Number(card.dataset.id);
    if (checked) {
      selected.add(id);
      card.classList.add('selected');
      document.getElementById('gp-check-' + id).style.background  = '#FDF2F2';
      document.getElementById('gp-check-' + id).style.borderColor = '#C0392B';
    } else {
      selected.delete(id);
      card.classList.remove('selected');
      document.getElementById('gp-check-' + id).style.background  = '#fff';
      document.getElementById('gp-check-' + id).style.borderColor = '#CBD5E1';
    }
  });
  updateBulkBar();
}

function updateBulkBar() {
  const bar = document.getElementById('bulk-bar');
  if (!selectMode) { bar.style.display = 'none'; return; }
  bar.style.display = 'flex';
  document.getElementById('bulk-count').textContent = selected.size + ' selected';
  document.getElementById('select-all').checked = selected.size === document.querySelectorAll('.gp-card').length;
}

function clearSelection() {
  selectMode = false;
  selected.clear();
  document.querySelectorAll('.gp-card').forEach(c => c.classList.remove('selected'));
  document.querySelectorAll('.gp-select-overlay').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.gp-check').forEach(el => {
    el.style.display = 'none';
    el.style.background   = '#fff';
    el.style.borderColor  = '#CBD5E1';
  });
  document.getElementById('select-all').checked = false;
  updateBulkBar();
}

// ── Bulk delete ───────────────────────────────────────────
function deleteSelected() {
  if (!selected.size) return;
  if (!confirm('Delete ' + selected.size + ' photo' + (selected.size > 1 ? 's' : '') + '?')) return;
  const ids = [...selected];
  Promise.all(ids.map(id =>
    fetch(BASE + '/' + id, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    }).then(r => r.json()).then(res => res.ok ? id : null)
  )).then(results => {
    results.filter(Boolean).forEach(id => document.getElementById('gp-' + id)?.remove());
    clearSelection();
    const grid = document.getElementById('admin-gallery-grid');
    if (grid && !grid.querySelectorAll('.gp-card').length) location.reload();
  });
}

// ── Single delete ─────────────────────────────────────────
function deletePhoto(id) {
  if (!confirm('Delete this photo?')) return;
  fetch(BASE + '/' + id, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
  })
  .then(r => r.json())
  .then(res => {
    if (res.ok) {
      document.getElementById('gp-' + id)?.remove();
      const grid = document.getElementById('admin-gallery-grid');
      if (grid && !grid.querySelectorAll('.gp-card').length) location.reload();
    }
  })
  .catch(() => alert('Delete failed.'));
}
</script>
@endpush

@endsection
