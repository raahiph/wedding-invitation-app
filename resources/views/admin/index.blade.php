@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', $wedding['groom'] . ' & ' . $wedding['bride'] . ' · Guest Management')

@section('content')

{{-- Countdown Mode Toggle --}}
<div class="mode-card {{ $rsvpMode ? 'active' : '' }}">
  <div>
    <p class="mode-label">RSVP Mode</p>
    <p class="mode-desc">{{ $rsvpMode ? 'ON — verified guests see the save-the-date & RSVP page' : 'OFF — verified guests see the full invitation' }}</p>
  </div>
  <form method="POST" action="{{ route('admin.countdown.toggle') }}" class="toggle-form">
    @csrf
    <label class="toggle-switch" title="Toggle RSVP mode">
      <input type="checkbox" {{ $rsvpMode ? 'checked' : '' }} onchange="this.form.submit()">
      <span class="toggle-slider"></span>
    </label>
  </form>
</div>

{{-- Stats --}}
<div class="stats">
  <div class="stat"><strong id="stat-total">{{ $totalGuests }}</strong><span>Invited</span></div>
  <div class="stat"><strong id="stat-attending">{{ $totalAttending }}</strong><span>Attending</span></div>
  <div class="stat"><strong id="stat-heads">{{ $totalHeads }}</strong><span>Total Heads</span></div>
  <div class="stat"><strong id="stat-pending">{{ $totalGuests - $guests->filter(fn($g) => $g->rsvp)->count() }}</strong><span>Pending RSVP</span></div>
  <div class="stat stat-groom"><strong id="stat-groom">{{ $groomCount }}</strong><span>{{ $wedding['groom'] }}'s Side</span></div>
  <div class="stat stat-bride"><strong id="stat-bride">{{ $brideCount }}</strong><span>{{ $wedding['bride'] }}'s Side</span></div>
</div>

{{-- Add Guest --}}
<div class="card">
  <p class="card-title">Add Guest</p>
  <form id="add-guest-form" method="POST" action="{{ route('admin.guests.store') }}">
    @csrf
    <div class="form-row">
      <input class="form-input" type="tel"  name="mobile" placeholder="Mobile number (e.g. 9123456)" required>
      <input class="form-input" type="text" name="name"   placeholder="Name (optional)">
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
    <p class="flash">{{ session('admin_msg') }}</p>
  @endif
  @if($errors->has('mobile'))
    <p class="flash err">{{ $errors->first('mobile') }}</p>
  @endif
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
  sessionCol:    8,
  rsvpNameCol:   4,
  statsMode:    'server',
  statsUrl:     '{{ route('admin.stats') }}',
  currentSide:  '',
};
</script>
<script src="/assets/js/admin-guests.js"></script>
@endpush

@endsection
