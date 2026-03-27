@extends('admin.layout')

@php
  $sideName  = $side === 'groom' ? $wedding['groom'] : $wedding['bride'];
  $sideClass = $side === 'groom' ? 'stat-groom' : 'stat-bride';
@endphp

@section('title', $sideName . "'s Guests")
@section('page-title', $sideName . "'s Guests")
@section('page-subtitle', 'Manage guests on ' . $sideName . "'s side")

@section('content')

{{-- Stats --}}
<div class="stats">
  <div class="stat {{ $sideClass }}"><strong id="stat-total">{{ $totalGuests }}</strong><span>Invited</span></div>
  <div class="stat"><strong id="stat-est-headcount">{{ $estHeadcount }}</strong><span>Est. Headcount</span></div>
  <div class="stat"><strong id="stat-attending">{{ $totalAttending }}</strong><span>Attending</span></div>
  <div class="stat"><strong id="stat-heads">{{ $totalHeads }}</strong><span>Total Heads</span></div>
  <div class="stat"><strong id="stat-pending">{{ $totalGuests - $guests->filter(fn($g) => $g->rsvp)->count() }}</strong><span>Pending RSVP</span></div>
  <div class="stat"><strong id="stat-rsvp-sent">{{ $rsvpSentCount }} / {{ $totalGuests }}</strong><span>RSVP Link Sent</span></div>
  <div class="stat"><strong id="stat-invitation-sent">{{ $inviteSentCount }} / {{ $totalAttending }}</strong><span>Invite Sent</span></div>
</div>

{{-- Add Guest --}}
<div class="card">
  <p class="card-title">Add Guest</p>
  <form id="add-guest-form" method="POST" action="{{ route('admin.guests.store') }}">
    @csrf
    <input type="hidden" name="side" value="{{ $side }}">
    <div class="form-row">
      <input class="form-input" type="tel"  name="mobile" placeholder="Mobile number (e.g. 9123456)" required>
      <input class="form-input" type="text" name="nickname" placeholder="Nickname" required>
      <input class="form-input" type="text" name="notes"  placeholder="Notes (optional)">
      <button type="submit" class="form-btn">Add</button>
    </div>
  </form>
  @if(session('admin_msg'))
    <p class="flash">{{ session('admin_msg') }}</p>
  @endif
  @if($errors->has('mobile'))
    <p class="flash err">{{ $errors->first('mobile') }}</p>
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
  <p class="flash" id="csv-flash" style="display:none"></p>
</div>

@include('admin.partials.guest-table', ['showSide' => false, 'emptyMessage' => "No guests on {$sideName}'s side yet."])

@include('admin.partials.edit-modal')

@push('scripts')
<script>
window.GUEST_CFG = {
  csrfToken:    document.querySelector('meta[name="csrf-token"]').content,
  sideLabels:   { groom: '{{ $wedding['groom'] }}', bride: '{{ $wedding['bride'] }}', other: 'Other' },
  hasSideFilter: false,
  hasSideCol:    false,
  sessionCol:    8,
  nicknameCol:   4,
  statsMode:    'dom',
  statsUrl:     '',
  currentSide:  '{{ $side }}',
};
</script>
<script src="/assets/js/admin-guests.js?v={{ filemtime(public_path('assets/js/admin-guests.js')) }}"></script>
@endpush

@endsection
