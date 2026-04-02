@extends('admin.layout')

@section('title', 'Settings')
@section('page-title', 'Wedding Settings')
@section('page-subtitle', 'Edit the details shown across the invitation')

@section('content')

<style>
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
  </div>

  <button type="submit" class="s-save">Save Settings</button>

  @if(session('settings_saved'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('settings_saved'))));</script>
  @endif

</form>

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
