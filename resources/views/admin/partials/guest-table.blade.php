@php $showSide = $showSide ?? true; @endphp

<div class="tbl-filters">
  <input class="form-input tbl-search" type="search" id="guest-search" placeholder="Search mobile, name…" autocomplete="off">
  <select class="form-input tbl-select" id="guest-status">
    <option value="">All statuses</option>
    <option value="yes">Attending</option>
    <option value="no">Not attending</option>
    <option value="pending">Pending RSVP</option>
  </select>
  @if($showSide)
  <select class="form-input tbl-select" id="guest-side">
    <option value="">All sides</option>
    <option value="groom">{{ $wedding['groom'] }}'s side</option>
    <option value="bride">{{ $wedding['bride'] }}'s side</option>
    <option value="other">Other</option>
  </select>
  @endif
  <span class="tbl-count" id="guest-count"></span>
</div>

<div class="tbl-wrap">
  <table id="guest-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Mobile</th>
        <th>Name</th>
        @if($showSide)<th>Side</th>@endif
        <th>RSVP Name</th>
        <th>Attending</th>
        <th>+Guests</th>
        <th>Ceremony</th>
        <th>Session</th>
        <th>Submitted</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="guest-tbody">
      @forelse($guests as $i => $guest)
      @php
        $attStatus = !$guest->rsvp ? 'pending' : ($guest->rsvp->attending ? 'yes' : 'no');
        $sideLabel = $guest->side === 'groom' ? $wedding['groom'] : ($guest->side === 'bride' ? $wedding['bride'] : 'Other');
        $sideCls   = 'side-' . $guest->side;
      @endphp
      <tr data-status="{{ $attStatus }}" data-side="{{ $guest->side }}">
        <td class="td-dim">{{ $i + 1 }}</td>
        <td class="td-hi">{{ $guest->mobile }}</td>
        <td class="guest-name">{{ $guest->name ?: '—' }}</td>
        @if($showSide)
        <td><span class="badge {{ $sideCls }} guest-side">{{ $sideLabel }}</span></td>
        @endif
        <td>{{ $guest->rsvp->full_name ?? '—' }}</td>
        <td>
          <span class="badge attending-badge badge-{{ $attStatus }}">
            {{ $attStatus === 'yes' ? 'Yes' : ($attStatus === 'no' ? 'No' : 'Pending') }}
          </span>
        </td>
        <td class="plus-ones">{{ $guest->rsvp ? $guest->rsvp->plus_ones : '—' }}</td>
        <td>
          <form method="POST" action="{{ route('admin.guests.ceremony', $guest) }}">
            @csrf
            <button type="submit" class="ceremony-btn {{ $guest->attends_ceremony ? 'ceremony-yes' : 'ceremony-no' }}">
              {{ $guest->attends_ceremony ? 'Yes' : 'No' }}
            </button>
          </form>
        </td>
        <td>
          @if(!$guest->attends_ceremony && $guest->session)
            <span class="badge" style="background:#EAF1FB;color:#2A4E96;border-color:#C3D4F5">S{{ $guest->session }}</span>
          @else
            <span class="td-dim">—</span>
          @endif
        </td>
        <td class="td-dim">{{ $guest->rsvp ? $guest->rsvp->updated_at->format('d M, H:i') : '—' }}</td>
        <td style="white-space:nowrap">
          <button class="edit-btn"
            data-url="{{ route('admin.guests.update', $guest) }}"
            data-mobile="{{ $guest->mobile }}"
            data-name="{{ $guest->name }}"
            data-notes="{{ $guest->notes }}"
            data-side="{{ $guest->side }}"
            data-attending="{{ $attStatus === 'pending' ? '' : $attStatus }}"
            data-plus="{{ $guest->rsvp ? $guest->rsvp->plus_ones : 0 }}"
            data-rsvp-name="{{ $guest->rsvp->full_name ?? '' }}"
            data-ceremony="{{ $guest->attends_ceremony ? '1' : '0' }}"
            data-session="{{ $guest->session ?? '' }}">Edit</button>
          <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="del-btn" style="margin-left:8px">Remove</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="{{ $showSide ? 11 : 10 }}" style="text-align:center;color:#8FA3B8;padding:32px;">
          {{ $emptyMessage ?? 'No guests yet.' }}
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
