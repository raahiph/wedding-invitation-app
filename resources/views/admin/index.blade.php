@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', $wedding['groom'] . ' & ' . $wedding['bride'] . ' · Guest Management')

@section('content')

{{-- Countdown Mode Toggle --}}
<div class="mode-card {{ $rsvpMode ? 'active' : '' }}" id="rsvp-mode-card">
  <div>
    <p class="mode-label">RSVP Mode</p>
    <p class="mode-desc" id="rsvp-mode-desc">{{ $rsvpMode ? 'ON — verified guests see the save-the-date & RSVP page' : 'OFF — verified guests see the full invitation' }}</p>
  </div>
  <label class="toggle-switch" title="Toggle RSVP mode">
    <input type="checkbox" id="rsvp-mode-toggle" {{ $rsvpMode ? 'checked' : '' }}>
    <span class="toggle-slider"></span>
  </label>
</div>

{{-- Stats --}}
<div class="stats">
  <div class="stat"><strong id="stat-total">{{ $totalGuests }}</strong><span>Invited</span></div>
  <div class="stat"><strong id="stat-est-headcount">{{ $estHeadcount }}</strong><span>Est. Headcount</span></div>
  <div class="stat"><strong id="stat-attending">{{ $totalAttending }}</strong><span>Attending</span></div>
  <div class="stat"><strong id="stat-heads">{{ $totalHeads }}</strong><span>Total Heads</span></div>
  <div class="stat"><strong id="stat-pending">{{ $totalGuests - $guests->filter(fn($g) => $g->rsvp)->count() }}</strong><span>Pending RSVP</span></div>
  <div class="stat stat-groom"><strong id="stat-groom">{{ $groomCount }}</strong><span>{{ $wedding['groom'] }}'s Attending</span></div>
  <div class="stat stat-bride"><strong id="stat-bride">{{ $brideCount }}</strong><span>{{ $wedding['bride'] }}'s Attending</span></div>
  <div class="stat"><strong id="stat-rsvp-sent">{{ $rsvpSentCount }} / {{ $totalGuests }}</strong><span>RSVP Link Sent</span></div>
  <div class="stat"><strong id="stat-invitation-sent">{{ $inviteSentCount }} / {{ $totalAttending }}</strong><span>Invite Sent</span></div>
</div>

{{-- Add Guest --}}
<div class="card">
  <p class="card-title">Add Guest</p>
  <form id="add-guest-form" method="POST" action="{{ route('admin.guests.store') }}">
    @csrf
    <div class="form-row">
      <input class="form-input" type="tel"  name="mobile" placeholder="Mobile number (e.g. 9123456)" required>
      <input class="form-input" type="text" name="nickname" placeholder="Nickname" required>
      <input class="form-input" type="text" name="notes"  placeholder="Notes (optional)">
      <select class="form-input" name="side">
        <option value="other">Side</option>
        <option value="groom">{{ $wedding['groom'] }}'s side</option>
        <option value="bride">{{ $wedding['bride'] }}'s side</option>
        <option value="other">Other</option>
      </select>
      <button type="submit" class="form-btn">Add</button>
    </div>
  </form>
  @if(session('admin_msg'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('admin_msg'))));</script>
  @endif
  @if($errors->has('mobile'))
    <script>document.addEventListener('DOMContentLoaded', () => showToast(@json($errors->first('mobile')), true));</script>
  @endif
</div>

{{-- Import Guests --}}
<div class="card">
  <p class="card-title">Import Guests via CSV</p>
  <div class="csv-row">
    <label class="csv-label" for="csv-file">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      <span id="csv-file-label">Choose CSV file…</span>
    </label>
    <input type="file" id="csv-file" accept=".csv,text/csv" style="display:none">
    <button class="form-btn" id="csv-import-btn">Import</button>
    <a href="/assets/sample-guests.csv" download class="csv-sample-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Sample CSV
    </a>
  </div>
</div>

@include('admin.partials.guest-table', ['showSide' => true])

@include('admin.partials.edit-modal')

@push('scripts')
<script>
window.GUEST_CFG = {
  csrfToken:    document.querySelector('meta[name="csrf-token"]').content,
  sideLabels:   { groom: '{{ $wedding['groom'] }}', bride: '{{ $wedding['bride'] }}', other: 'Other' },
  hasSideFilter: true,
  hasSideCol:    true,
  sessionCol:    9,
  nicknameCol:   4,
  statsMode:    'server',
  statsUrl:     '{{ route('admin.stats') }}',
  currentSide:  '',
};
</script>
<script src="/assets/js/admin-guests.js?v={{ filemtime(public_path('assets/js/admin-guests.js')) }}"></script>
<script>
(function () {
  const toggle   = document.getElementById('rsvp-mode-toggle');
  const card     = document.getElementById('rsvp-mode-card');
  const desc     = document.getElementById('rsvp-mode-desc');
  const csrf     = document.querySelector('meta[name="csrf-token"]').content;
  const onDesc   = 'ON — verified guests see the save-the-date & RSVP page';
  const offDesc  = 'OFF — verified guests see the full invitation';

  toggle.addEventListener('change', function () {
    toggle.disabled = true;
    fetch('{{ route('admin.countdown.toggle') }}', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
    })
    .then(r => r.json())
    .then(d => {
      if (!d.ok) { toggle.checked = !toggle.checked; return; }
      card.classList.toggle('active', d.active);
      desc.textContent = d.active ? onDesc : offDesc;
    })
    .catch(() => { toggle.checked = !toggle.checked; })
    .finally(() => { toggle.disabled = false; });
  });
})();
</script>
@endpush

@endsection
