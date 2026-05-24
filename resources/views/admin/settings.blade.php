@extends('admin.layout')

@section('title', 'Settings')
@section('page-title', 'Wedding Settings')
@section('page-subtitle', 'Edit the details shown across the invitation')

@section('content')
<link rel="stylesheet" href="/assets/js/coloris.min.css">
<script src="/assets/js/coloris.min.js"></script>

<style>
.palette-row {
  display:flex; align-items:center; gap:8px; margin-bottom:8px;
  transition:opacity 0.15s;
}
.palette-row.dragging { opacity:0.35; }
.palette-row.drag-over { outline:2px dashed #4A6FA5; border-radius:6px; }
.palette-drag-handle {
  cursor:grab; color:#B0BFCC; flex-shrink:0; padding:0 2px; user-select:none;
  font-size:14px; line-height:1;
}
.palette-drag-handle:active { cursor:grabbing; }
.clr-field {
  display:inline-block; position:relative;
  width:38px; height:38px; flex-shrink:0;
}
.clr-field input.palette-color-input {
  position:absolute; inset:0; width:100%; height:100%;
  opacity:0; cursor:pointer; z-index:1; border:none; padding:0; margin:0;
}
.clr-field > button {
  position:absolute !important; width:100% !important; height:100% !important;
  right:0 !important; top:0 !important; transform:none !important;
  border-radius:6px !important; border:none !important;
  pointer-events:none !important; z-index:0;
}
.preview-row { display:flex; gap:8px; margin-top:9px; flex-wrap:wrap; }
.preview-chip {
  display:inline-flex; align-items:center; gap:5px;
  font-size:10px; font-weight:500; letter-spacing:0.04em;
  background:#EAF1FB; color:#2A4E96; border:1px solid #C3D4F5;
  padding:3px 10px; border-radius:2px; line-height:1.6;
}
.preview-chip .chip-lbl { color:#6B8BA8; font-weight:400; }
</style>

<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf

  {{-- Names --}}
  <div class="card">
    <p class="card-title">Names</p>
    <div class="s-grid2">
      <div class="s-field">
        <label>Groom</label>
        <input class="form-input" style="width:100%" type="text" name="groom" value="{{ $settings['groom'] }}" required>
      </div>
      <div class="s-field">
        <label>Bride</label>
        <input class="form-input" style="width:100%" type="text" name="bride" value="{{ $settings['bride'] }}" required>
      </div>
    </div>
  </div>

  {{-- Date, Time & Sessions --}}
  <div class="card">
    <p class="card-title">Date, Time & Sessions</p>

    {{-- Event date picker --}}
    <div class="s-field" style="margin-bottom:22px;">
      <label>Event Date</label>
      <input class="form-input" style="width:100%;max-width:220px" type="date"
             name="event_date" id="event_date" value="{{ $settings['event_date'] ?? '' }}">
      <div class="preview-row" id="date-preview-row">
        <span class="preview-chip"><span class="chip-lbl">Long&nbsp;</span><span id="prev-date-long">{{ $settings['date'] ?? '' }}</span></span>
        <span class="preview-chip"><span class="chip-lbl">Short&nbsp;</span><span id="prev-date-short">{{ $settings['date_short'] ?? '' }}</span></span>
        <span class="preview-chip"><span class="chip-lbl">Day&nbsp;</span><span id="prev-date-day">{{ $settings['date_day'] ?? '' }}</span></span>
      </div>
    </div>

    {{-- Reception (unassigned guests fallback) --}}
    <div class="s-grid2" style="margin-bottom:22px;">
      <div class="s-field">
        <label>Reception Time <span style="font-weight:300;color:#6B8BA8;">(unassigned guests)</span></label>
        <input class="form-input" style="width:100%" type="time"
               name="reception_time_raw" id="reception_time_raw" value="{{ $settings['reception_time_raw'] ?? '' }}">
        <div class="preview-row" id="reception-preview">
          <span class="preview-chip"><span id="prev-reception-time">{{ $settings['reception_time'] ?? '' }}</span></span>
        </div>
      </div>
      <div class="s-field">
        <label>Reception Time (written)</label>
        <input class="form-input" style="width:100%" type="text" name="reception_time_words"
               value="{{ $settings['reception_time_words'] }}" placeholder="at five o'clock in the evening">
        <p class="hint">Shown verbatim on the invitation</p>
      </div>
    </div>

    {{-- Timezone --}}
    <div class="s-field" style="margin-bottom:30px;">
      <label>Timezone Offset</label>
      <input class="form-input" style="width:100%;max-width:140px" type="text"
             name="timezone" id="timezone" value="{{ $settings['timezone'] ?? '+05:00' }}" placeholder="+05:00">
      <p class="hint">UTC offset — e.g. <strong>+05:00</strong> for Maldives</p>
      <div class="preview-row" id="iso-preview">
        <span class="preview-chip"><span class="chip-lbl">ISO&nbsp;</span><span id="prev-iso">{{ $settings['datetime_iso'] ?? '' }}</span></span>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid #E2E8F0;margin-bottom:24px;">
    <p class="hint" style="margin-bottom:20px;">Ceremony guests see the wedding window. Reception guests are shown their assigned session time.</p>

    {{-- Wedding window --}}
    <div class="s-grid2" style="margin-bottom:12px;">
      <div class="s-field">
        <label>Wedding Start</label>
        <input class="form-input" style="width:100%" type="time" name="wedding_start_raw" id="wedding_start_raw" value="{{ $settings['wedding_start_raw'] ?? '16:45' }}">
        <div class="preview-row" id="wedding-start-preview">
          <span class="preview-chip"><span id="prev-wedding-start">{{ $settings['wedding_start'] ?? '' }}</span></span>
        </div>
      </div>
      <div class="s-field">
        <label>Wedding End</label>
        <input class="form-input" style="width:100%" type="time" name="wedding_end_raw" id="wedding_end_raw" value="{{ $settings['wedding_end_raw'] ?? '19:30' }}">
        <div class="preview-row" id="wedding-end-preview">
          <span class="preview-chip"><span id="prev-wedding-end">{{ $settings['wedding_end'] ?? '' }}</span></span>
        </div>
      </div>
    </div>
    <div class="s-field" style="margin-bottom:22px;">
      <label>Wedding Time (written)</label>
      <input class="form-input" style="width:100%" type="text" name="wedding_time_words"
             value="{{ $settings['wedding_time_words'] }}" placeholder="from four forty-five until seven thirty in the evening">
      <p class="hint">Shown verbatim on the invitation for ceremony guests</p>
    </div>

    {{-- Session 1 --}}
    <div class="s-grid2" style="margin-bottom:12px;">
      <div class="s-field">
        <label>Session 1 — Start</label>
        <input class="form-input" style="width:100%" type="time" name="session1_start_raw" id="session1_start_raw" value="{{ $settings['session1_start_raw'] ?? '17:30' }}">
        <div class="preview-row" id="s1-start-preview">
          <span class="preview-chip"><span id="prev-s1-start">{{ $settings['session1_start'] ?? '' }}</span></span>
        </div>
      </div>
      <div class="s-field">
        <label>Session 1 — End</label>
        <input class="form-input" style="width:100%" type="time" name="session1_end_raw" id="session1_end_raw" value="{{ $settings['session1_end_raw'] ?? '18:30' }}">
        <div class="preview-row" id="s1-end-preview">
          <span class="preview-chip"><span id="prev-s1-end">{{ $settings['session1_end'] ?? '' }}</span></span>
        </div>
      </div>
    </div>
    <div class="s-field" style="margin-bottom:22px;">
      <label>Session 1 Time (written)</label>
      <input class="form-input" style="width:100%" type="text" name="session1_time_words"
             value="{{ $settings['session1_time_words'] }}" placeholder="at five thirty in the evening">
      <p class="hint">Shown verbatim on the invitation for Session 1 guests</p>
    </div>

    {{-- Session 2 --}}
    <div class="s-grid2" style="margin-bottom:12px;">
      <div class="s-field">
        <label>Session 2 — Start</label>
        <input class="form-input" style="width:100%" type="time" name="session2_start_raw" id="session2_start_raw" value="{{ $settings['session2_start_raw'] ?? '18:30' }}">
        <div class="preview-row" id="s2-start-preview">
          <span class="preview-chip"><span id="prev-s2-start">{{ $settings['session2_start'] ?? '' }}</span></span>
        </div>
      </div>
      <div class="s-field">
        <label>Session 2 — End</label>
        <input class="form-input" style="width:100%" type="time" name="session2_end_raw" id="session2_end_raw" value="{{ $settings['session2_end_raw'] ?? '19:30' }}">
        <div class="preview-row" id="s2-end-preview">
          <span class="preview-chip"><span id="prev-s2-end">{{ $settings['session2_end'] ?? '' }}</span></span>
        </div>
      </div>
    </div>
    <div class="s-field">
      <label>Session 2 Time (written)</label>
      <input class="form-input" style="width:100%" type="text" name="session2_time_words"
             value="{{ $settings['session2_time_words'] }}" placeholder="at six thirty in the evening">
      <p class="hint">Shown verbatim on the invitation for Session 2 guests</p>
    </div>
  </div>

  {{-- Venue --}}
  <div class="card">
    <p class="card-title">Venue</p>
    <div class="s-grid2" style="margin-bottom:16px;">
      <div class="s-field">
        <label>Venue Name</label>
        <input class="form-input" style="width:100%" type="text" name="venue" value="{{ $settings['venue'] }}">
      </div>
      <div class="s-field">
        <label>City</label>
        <input class="form-input" style="width:100%" type="text" name="city" value="{{ $settings['city'] }}">
      </div>
    </div>
    <div class="s-field">
      <label>Country</label>
      <input class="form-input" style="width:100%;max-width:320px" type="text" name="country" value="{{ $settings['country'] }}">
    </div>
    <div class="s-field" style="margin-top:16px;">
      <label>Map URL</label>
      <input class="form-input" style="width:100%" type="url" name="venue_map_url" placeholder="https://maps.google.com/..." value="{{ $settings['venue_map_url'] ?? '' }}">
    </div>
  </div>

  {{-- Dress Code --}}
  <div class="card">
    <p class="card-title">Dress Code</p>
    <p class="hint" style="margin-bottom:20px;">Set a general dress code, or fill in Ladies &amp; Gents separately. If both gender-specific fields are set, the general field is ignored on the invitation.</p>
    <div class="s-grid2" style="margin-bottom:20px;">
      <div class="s-field">
        <label>General — Dress Code</label>
        <input class="form-input" style="width:100%" type="text" name="dress_code" value="{{ $settings['dress_code'] }}">
      </div>
      <div class="s-field">
        <label>General — Note</label>
        <input class="form-input" style="width:100%" type="text" name="dress_note" value="{{ $settings['dress_note'] }}">
      </div>
    </div>
    <hr style="border:none;border-top:1px solid #E2E8F0;margin-bottom:20px;">
    <div class="s-grid2" style="margin-bottom:12px;">
      <div class="s-field">
        <label>Ladies — Dress Code</label>
        <input class="form-input" style="width:100%" type="text" name="dress_code_ladies" value="{{ $settings['dress_code_ladies'] ?? '' }}" placeholder="e.g. Formal gown">
      </div>
      <div class="s-field">
        <label>Ladies — Note</label>
        <input class="form-input" style="width:100%" type="text" name="dress_note_ladies" value="{{ $settings['dress_note_ladies'] ?? '' }}" placeholder="e.g. Floor-length preferred">
      </div>
    </div>
    <div class="s-grid2">
      <div class="s-field">
        <label>Gents — Dress Code</label>
        <input class="form-input" style="width:100%" type="text" name="dress_code_gents" value="{{ $settings['dress_code_gents'] ?? '' }}" placeholder="e.g. Suit & tie">
      </div>
      <div class="s-field">
        <label>Gents — Note</label>
        <input class="form-input" style="width:100%" type="text" name="dress_note_gents" value="{{ $settings['dress_note_gents'] ?? '' }}" placeholder="e.g. Dark colours preferred">
      </div>
    </div>
  </div>

  {{-- RSVP --}}
  <div class="card">
    <p class="card-title">RSVP</p>
    <div class="s-field">
      <label>RSVP Deadline</label>
      <input class="form-input" style="width:100%;max-width:220px" type="date"
             name="rsvp_by_raw" id="rsvp_by_raw" value="{{ $settings['rsvp_by_raw'] ?? '' }}">
      <div class="preview-row" id="rsvp-preview">
        <span class="preview-chip"><span id="prev-rsvp-by">{{ $settings['rsvp_by'] ?? '' }}</span></span>
      </div>
    </div>
  </div>

  {{-- Gallery --}}
  <div class="card">
    <p class="card-title">Gallery</p>
    <div class="s-field">
      <label>Dropbox File Request URL</label>
      <input class="form-input" style="width:100%" type="url" name="dropbox_file_request_url"
             value="{{ $settings['dropbox_file_request_url'] ?? '' }}"
             placeholder="https://www.dropbox.com/request/…">
      <p style="font-size:11px;color:#8E9BAB;margin-top:6px;">When set, the upload page shows a button linking here instead of the built-in uploader.</p>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:20px;padding-top:16px;border-top:1px solid #EDF0F4;">
      <div>
        <p style="font-size:13px;font-weight:500;color:#1A2332;margin-bottom:3px;">Delete from Dropbox on removal</p>
        <p class="mode-desc">When enabled, deleting a synced photo also removes the original file from Dropbox.</p>
      </div>
      <label class="toggle-switch">
        <input type="checkbox" name="dropbox_delete_on_remove" value="1"
               {{ ($settings['dropbox_delete_on_remove'] ?? '0') === '1' ? 'checked' : '' }}>
        <span class="toggle-slider"></span>
      </label>
    </div>
  </div>

  {{-- Color Palette --}}
  <div class="card">
    <p class="card-title">Color Palette</p>
    <p style="font-size:12px;color:#8E9BAB;margin-bottom:16px;">These colors appear in the "Colors of our day" section on the invitation.</p>

    <div id="palette-list">
      @php $paletteItems = json_decode($settings['palette'] ?? '[]', true) ?: []; @endphp
      @foreach($paletteItems as $i => $swatch)
      <div class="palette-row" data-index="{{ $i }}" draggable="true">
        <span class="palette-drag-handle" title="Drag to reorder">⠿</span>
        <input type="text" class="form-input palette-color-input" name="palette[{{ $i }}][color]" value="{{ $swatch['color'] }}" data-coloris placeholder="#000000" maxlength="7">
        <input class="form-input palette-name-input" type="text" name="palette[{{ $i }}][name]" value="{{ $swatch['name'] }}" placeholder="Color name" maxlength="40" style="flex:1">
        <button type="button" class="cat-del-btn palette-remove-btn" title="Remove">✕</button>
      </div>
      @endforeach
    </div>

    <button type="button" class="form-btn" id="palette-add-btn" style="margin-top:12px;background:#F2F5F8;color:#3A4F65;border:1px solid #C8D3DE;">+ Add Color</button>
  </div>

  <button type="submit" class="s-save">Save Settings</button>

  @if(session('settings_saved'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('settings_saved'))));</script>
  @endif

</form>

{{-- ── Guest Categories ──────────────────────────────────────────── --}}
<div class="card" style="margin-top:24px">
  <p class="card-title">Guest Categories</p>
  <p style="font-size:12px;color:#8E9BAB;margin-bottom:14px;">Categories like Family, Bridesmaid, Groomsmen, etc. Assign them to guests from the guest table.</p>

  <div class="csv-row" style="margin-bottom:14px">
    <input class="form-input" type="text" id="cat-name-input" placeholder="New category name…" maxlength="60" style="flex:1;min-width:160px">
    <button class="form-btn" id="cat-add-btn">Add</button>
  </div>

  <div id="cat-list">
    @forelse($categories as $cat)
    @php $paletteItems = json_decode($settings['palette'] ?? '[]', true) ?: []; @endphp
    <div class="cat-item" data-id="{{ $cat->id }}" data-color="{{ $cat->color ?? '' }}">
      <div style="display:flex;align-items:center;gap:10px;flex:1">
        <div class="cat-color-swatch" style="background:{{ $cat->color ?? '#e1e5e8' }};" title="Change color"></div>
        <span class="cat-name">{{ $cat->name }}</span>
      </div>
      <div class="cat-palette-picker">
        <div class="cat-color-item">
          <button type="button" class="cat-color-opt-none" data-color="" title="None">✕</button>
          <span class="cat-color-lbl">None</span>
        </div>
        @foreach($paletteItems as $sw)
        <div class="cat-color-item">
          <button type="button" class="cat-color-opt {{ $cat->color === $sw['color'] ? 'selected' : '' }}" data-color="{{ $sw['color'] }}" style="background:{{ $sw['color'] }};"></button>
          <span class="cat-color-lbl">{{ $sw['name'] }}</span>
        </div>
        @endforeach
      </div>
      <button class="cat-del-btn" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" title="Remove">✕</button>
    </div>
    @empty
    <p id="cat-empty" style="font-size:12px;color:#8E9BAB">No categories yet.</p>
    @endforelse
  </div>
</div>

<script>
(function () {
  const csrf    = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const input   = document.getElementById('cat-name-input');
  const addBtn  = document.getElementById('cat-add-btn');
  const list    = document.getElementById('cat-list');

  function removeEmpty() {
    const empty = document.getElementById('cat-empty');
    if (empty) empty.remove();
  }

  // Palette swatches available (from server)
  const paletteSwatches = @json($paletteItems ?? []);

  function buildSwatchPicker(currentColor) {
    const noneItem = `<div class="cat-color-item"><button type="button" class="cat-color-opt-none" data-color="" title="None">✕</button><span class="cat-color-lbl">None</span></div>`;
    const swItems = paletteSwatches.map(sw =>
      `<div class="cat-color-item"><button type="button" class="cat-color-opt${currentColor === sw.color ? ' selected' : ''}" data-color="${sw.color}" style="background:${sw.color};"></button><span class="cat-color-lbl">${sw.name.replace(/</g,'&lt;')}</span></div>`
    ).join('');
    return `<div class="cat-palette-picker">${noneItem}${swItems}</div>`;
  }

  function buildItem(id, name, color) {
    const swatchBg = color || '#e1e5e8';
    const div = document.createElement('div');
    div.className = 'cat-item';
    div.dataset.id    = id;
    div.dataset.color = color || '';
    div.innerHTML =
      `<div style="display:flex;align-items:center;gap:10px;flex:1">`
      + `<div class="cat-color-swatch" style="background:${swatchBg};" title="Change color"></div>`
      + `<span class="cat-name">${name.replace(/</g,'&lt;')}</span>`
      + `</div>`
      + buildSwatchPicker(color || '')
      + `<button class="cat-del-btn" data-id="${id}" data-name="${name.replace(/"/g,'&quot;')}" title="Remove">✕</button>`;
    wireColorPicker(div, id);
    div.querySelector('.cat-del-btn').addEventListener('click', handleDelete);
    return div;
  }

  function closeAllPickers(except) {
    document.querySelectorAll('.cat-palette-picker.open').forEach(p => { if (p !== except) p.classList.remove('open'); });
  }

  function wireColorPicker(item, id) {
    const swatch = item.querySelector('.cat-color-swatch');
    const picker = item.querySelector('.cat-palette-picker');

    swatch.addEventListener('click', function (e) {
      e.stopPropagation();
      closeAllPickers(picker);
      picker.classList.toggle('open');
    });

    picker.querySelectorAll('.cat-color-item').forEach(colorItem => {
      colorItem.addEventListener('click', function (e) {
        e.stopPropagation();
        const color = colorItem.querySelector('[data-color]')?.dataset.color ?? '';
        fetch(`/admin/categories/${id}/color`, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ _token: csrf, color }),
        })
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          item.dataset.color = color;
          swatch.style.background = color || '#e1e5e8';
          picker.querySelectorAll('.cat-color-opt').forEach(b => b.classList.toggle('selected', b.dataset.color === color && !!color));
          picker.classList.remove('open');
          showToast('Color updated.');
        })
        .catch(() => showToast('Request failed.', true));
      });
    });
  }

  document.addEventListener('click', () => closeAllPickers(null));

  addBtn.addEventListener('click', function () {
    const name = input.value.trim();
    if (!name) return;
    addBtn.disabled = true;
    fetch('{{ route('admin.categories.store') }}', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ _token: csrf, name }),
    })
    .then(r => r.json())
    .then(d => {
      if (!d.ok) { showToast(d.message, true); return; }
      removeEmpty();
      list.appendChild(buildItem(d.category.id, d.category.name, d.category.color || ''));
      input.value = '';
      showToast('Category added.');
    })
    .catch(() => showToast('Request failed.', true))
    .finally(() => { addBtn.disabled = false; });
  });

  input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addBtn.click(); } });

  function handleDelete(e) {
    const btn  = e.currentTarget;
    const id   = btn.dataset.id;
    const name = btn.dataset.name;
    if (!confirm(`Remove category "${name}"? Guests in this category will be uncategorised.`)) return;
    btn.disabled = true;
    fetch(`/admin/categories/${id}`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ _token: csrf, _method: 'DELETE' }),
    })
    .then(r => r.json())
    .then(d => {
      if (!d.ok) { showToast('Delete failed.', true); return; }
      btn.closest('.cat-item').remove();
      if (!list.querySelector('.cat-item')) {
        const p = document.createElement('p');
        p.id = 'cat-empty'; p.style.cssText = 'font-size:12px;color:#8E9BAB';
        p.textContent = 'No categories yet.';
        list.appendChild(p);
      }
      showToast(`Category "${name}" removed.`);
    })
    .catch(() => showToast('Request failed.', true))
    .finally(() => { btn.disabled = false; });
  }

  document.querySelectorAll('.cat-del-btn:not(.palette-remove-btn)').forEach(btn => btn.addEventListener('click', handleDelete));

  // Wire color pickers on existing items
  document.querySelectorAll('.cat-item[data-id]').forEach(item => {
    wireColorPicker(item, item.dataset.id);
  });
})();
</script>

<script>
// ── Palette add/remove ──────────────────────────────────────────────────────
(function () {
  const paletteList = document.getElementById('palette-list');
  const addBtn      = document.getElementById('palette-add-btn');

  function reindex() {
    paletteList.querySelectorAll('.palette-row').forEach((row, i) => {
      row.dataset.index = i;
      row.querySelector('.palette-color-input').name = `palette[${i}][color]`;
      row.querySelector('.palette-name-input').name  = `palette[${i}][name]`;
    });
  }

  function buildRow(color, name) {
    const div = document.createElement('div');
    div.className = 'palette-row';
    div.draggable = true;
    div.innerHTML = `<span class="palette-drag-handle" title="Drag to reorder">⠿</span>`
      + `<input type="text" class="form-input palette-color-input" value="${color}" data-coloris placeholder="#000000" maxlength="7">`
      + `<input class="form-input palette-name-input" type="text" value="${name}" placeholder="Color name" maxlength="40" style="flex:1">`
      + `<button type="button" class="cat-del-btn palette-remove-btn" title="Remove">✕</button>`;
    div.querySelector('.palette-remove-btn').addEventListener('click', function () {
      div.remove();
      reindex();
    });
    wireDrag(div);
    return div;
  }

  // ── Drag-and-drop reorder ────────────────────────────────────────────────
  let dragSrc = null;

  function wireDrag(row) {
    row.addEventListener('dragstart', function (e) {
      dragSrc = row;
      row.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    });
    row.addEventListener('dragend', function () {
      row.classList.remove('dragging');
      paletteList.querySelectorAll('.palette-row').forEach(r => r.classList.remove('drag-over'));
      reindex();
    });
    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      if (row === dragSrc) return;
      paletteList.querySelectorAll('.palette-row').forEach(r => r.classList.remove('drag-over'));
      row.classList.add('drag-over');
    });
    row.addEventListener('dragleave', function () {
      row.classList.remove('drag-over');
    });
    row.addEventListener('drop', function (e) {
      e.preventDefault();
      row.classList.remove('drag-over');
      if (!dragSrc || dragSrc === row) return;
      const rows   = [...paletteList.querySelectorAll('.palette-row')];
      const srcIdx = rows.indexOf(dragSrc);
      const tgtIdx = rows.indexOf(row);
      if (srcIdx < tgtIdx) {
        paletteList.insertBefore(dragSrc, row.nextSibling);
      } else {
        paletteList.insertBefore(dragSrc, row);
      }
      reindex();
    });
  }

  addBtn.addEventListener('click', function () {
    paletteList.appendChild(buildRow('#000000', ''));
    reindex();
    paletteList.lastElementChild.querySelector('.palette-name-input').focus();
  });

  paletteList.querySelectorAll('.palette-remove-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      btn.closest('.palette-row').remove();
      reindex();
    });
  });

  paletteList.querySelectorAll('.palette-row').forEach(wireDrag);

  // Init Coloris — data-coloris attribute handles event delegation automatically
  Coloris({ format: 'hex', alpha: false });
})();
</script>

<script>
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const DAYS   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

function parseLocal(str) {
  const [y, m, d] = str.split('-').map(Number);
  return new Date(y, m - 1, d);
}
function fmtLong(str)  { const d = parseLocal(str); return MONTHS[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear(); }
function fmtShort(str) { const [y,m,d] = str.split('-'); return d + ' | ' + m + ' | ' + y.slice(2); }
function fmtDay(str)   { return DAYS[parseLocal(str).getDay()]; }
function fmtTime12(str) {
  const [h, m] = str.split(':').map(Number);
  return (h % 12 || 12) + ':' + String(m).padStart(2, '0') + ' ' + (h >= 12 ? 'pm' : 'am');
}

function refreshDate() {
  const v = document.getElementById('event_date').value;
  if (v) {
    document.getElementById('prev-date-long').textContent  = fmtLong(v);
    document.getElementById('prev-date-short').textContent = fmtShort(v);
    document.getElementById('prev-date-day').textContent   = fmtDay(v);
    document.getElementById('date-preview-row').style.display = 'flex';
  } else {
    document.getElementById('date-preview-row').style.display = 'none';
  }
  refreshIso();
}

function refreshReception() {
  const v = document.getElementById('reception_time_raw').value;
  if (v) {
    document.getElementById('prev-reception-time').textContent = fmtTime12(v);
    document.getElementById('reception-preview').style.display = 'flex';
  } else {
    document.getElementById('reception-preview').style.display = 'none';
  }
  refreshIso();
}

function refreshIso() {
  const date = document.getElementById('event_date').value;
  const time = document.getElementById('reception_time_raw').value;
  const tz   = document.getElementById('timezone').value || '+05:00';
  if (date && time) {
    document.getElementById('prev-iso').textContent = date + 'T' + time + ':00' + tz;
    document.getElementById('iso-preview').style.display = 'flex';
  } else {
    document.getElementById('iso-preview').style.display = 'none';
  }
}

function refreshRsvp() {
  const v = document.getElementById('rsvp_by_raw').value;
  if (v) {
    document.getElementById('prev-rsvp-by').textContent = fmtLong(v);
    document.getElementById('rsvp-preview').style.display = 'flex';
  } else {
    document.getElementById('rsvp-preview').style.display = 'none';
  }
}

// Simple time picker → preview wiring
function wireTime(inputId, previewRowId, prevSpanId) {
  const input = document.getElementById(inputId);
  const row   = document.getElementById(previewRowId);
  const span  = document.getElementById(prevSpanId);
  if (!input) return;
  function update() {
    if (input.value) { span.textContent = fmtTime12(input.value); row.style.display = 'flex'; }
    else             { row.style.display = 'none'; }
  }
  input.addEventListener('input', update);
}

// Wire up events
document.getElementById('event_date').addEventListener('input', refreshDate);
document.getElementById('reception_time_raw').addEventListener('input', refreshReception);
document.getElementById('timezone').addEventListener('input', refreshIso);
document.getElementById('rsvp_by_raw').addEventListener('input', refreshRsvp);
wireTime('wedding_start_raw', 'wedding-start-preview', 'prev-wedding-start');
wireTime('wedding_end_raw',   'wedding-end-preview',   'prev-wedding-end');
wireTime('session1_start_raw','s1-start-preview',      'prev-s1-start');
wireTime('session1_end_raw',  's1-end-preview',        'prev-s1-end');
wireTime('session2_start_raw','s2-start-preview',      'prev-s2-start');
wireTime('session2_end_raw',  's2-end-preview',        'prev-s2-end');

// Init visibility on page load
(function() {
  ['date-preview-row','reception-preview','iso-preview','rsvp-preview',
   'wedding-start-preview','wedding-end-preview','s1-start-preview','s1-end-preview','s2-start-preview','s2-end-preview']
  .forEach(id => {
    const el = document.getElementById(id);
    if (el && el.querySelector('[id^="prev-"]')?.textContent.trim() === '') {
      el.style.display = 'none';
    }
  });
})();
</script>

@endsection
