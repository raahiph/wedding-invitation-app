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
  <div class="stat"><strong id="stat-attending">{{ $totalAttending }}</strong><span>Attending</span></div>
  <div class="stat"><strong id="stat-heads">{{ $totalHeads }}</strong><span>Total Heads</span></div>
  <div class="stat"><strong id="stat-pending">{{ $totalGuests - $guests->filter(fn($g) => $g->rsvp)->count() }}</strong><span>Pending RSVP</span></div>
</div>

{{-- Add Guest --}}
<div class="card">
  <p class="card-title">Add Guest</p>
  <form id="add-guest-form" method="POST" action="{{ route('admin.guests.store') }}">
    @csrf
    <input type="hidden" name="side" value="{{ $side }}">
    <div class="form-row">
      <input class="form-input" type="tel"  name="mobile" placeholder="Mobile number (e.g. 9123456)" required>
      <input class="form-input" type="text" name="name"   placeholder="Name (optional)">
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

@include('admin.partials.guest-table', ['showSide' => false, 'emptyMessage' => "No guests on {$sideName}'s side yet."])

@include('admin.partials.edit-modal')

@push('scripts')
<script>
window.GUEST_CFG = {
  csrfToken:    document.querySelector('meta[name="csrf-token"]').content,
  sideLabels:   { groom: '{{ $wedding['groom'] }}', bride: '{{ $wedding['bride'] }}', other: 'Other' },
  hasSideFilter: false,
  hasSideCol:    false,
  sessionCol:    7,
  rsvpNameCol:   3,
  statsMode:    'dom',
  statsUrl:     '',
  currentSide:  '{{ $side }}',
};
</script>
<script src="/assets/js/admin-guests.js"></script>
@endpush

@endsection
