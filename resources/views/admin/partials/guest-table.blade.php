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
  <select class="form-input tbl-select" id="guest-rsvp-sent">
    <option value="">All (RSVP link)</option>
    <option value="1">RSVP link sent</option>
    <option value="0">RSVP link not sent</option>
  </select>
  <select class="form-input tbl-select" id="guest-invitation-sent">
    <option value="">All (invitation)</option>
    <option value="1">Invitation sent</option>
    <option value="0">Invitation not sent</option>
  </select>
  <button class="tbl-clear" id="filter-clear" style="display:none">Clear filters</button>
  <span class="tbl-count" id="guest-count"></span>
</div>

<div class="bulk-bar" id="bulk-bar" style="display:none">
  <span class="bulk-count" id="bulk-count"></span>
  <button class="bulk-bypass-btn" id="bulk-bypass-btn">Bypass RSVP</button>
  <button class="bulk-delete-btn" id="bulk-delete-btn">Delete Selected</button>
  <button class="bulk-clear-btn" id="bulk-clear-btn">Clear</button>
</div>

<div class="tbl-wrap">
  <table id="guest-table">
    <thead>
      <tr>
        <th style="width:36px;padding-right:0"><input type="checkbox" id="select-all" class="row-cb" title="Select all"></th>
        <th data-sort="num">#</th>
        <th data-sort="text">Mobile</th>
        <th data-sort="text">Name</th>
        <th data-sort="text">Nickname</th>
        @if($showSide)<th data-sort="text">Side</th>@endif
        <th data-sort="status">Attending</th>
        <th data-sort="num">+Guests</th>
        <th data-sort="yesno">Ceremony</th>
        <th data-sort="text">Session</th>
        <th data-sort="date">Submitted</th>
        <th data-sort="yesno">RSVP Sent</th>
        <th data-sort="yesno">Invite Sent</th>
        <th data-sort="yesno">Bypass</th>
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
      <tr data-status="{{ $attStatus }}" data-side="{{ $guest->side }}" data-id="{{ $guest->id }}" data-rsvp-sent="{{ $guest->rsvp_sent ? '1' : '0' }}" data-invitation-sent="{{ $guest->invitation_sent ? '1' : '0' }}" data-bypass="{{ $guest->rsvp_bypass ? '1' : '0' }}">
        <td style="padding-right:0"><input type="checkbox" class="row-cb" value="{{ $guest->id }}"></td>
        <td class="td-dim">{{ $i + 1 }}</td>
        <td class="td-hi">{{ $guest->mobile }}</td>
        <td class="guest-name">{{ $guest->name ?: '—' }}</td>
        <td>{{ $guest->nickname ?: '—' }}</td>
        @if($showSide)
        <td><span class="badge {{ $sideCls }} guest-side">{{ $sideLabel }}</span></td>
        @endif
        <td>
          <span class="badge attending-badge badge-{{ $attStatus }}">
            {{ $attStatus === 'yes' ? 'Yes' : ($attStatus === 'no' ? 'No' : 'Pending') }}
          </span>
        </td>
        <td class="plus-ones">{{ $guest->plus_ones }}</td>
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
        <td>
          <form method="POST" action="{{ route('admin.guests.rsvp-sent', $guest) }}">
            @csrf
            <button type="submit" class="rsvp-sent-btn {{ $guest->rsvp_sent ? 'ceremony-yes' : 'ceremony-no' }}">{{ $guest->rsvp_sent ? 'Yes' : 'No' }}</button>
          </form>
        </td>
        <td>
          <form method="POST" action="{{ route('admin.guests.invitation-sent', $guest) }}">
            @csrf
            <button type="submit" class="invitation-sent-btn {{ $guest->invitation_sent ? 'ceremony-yes' : 'ceremony-no' }}">{{ $guest->invitation_sent ? 'Yes' : 'No' }}</button>
          </form>
        </td>
        <td class="bypass-cell">
          @if($guest->rsvp_bypass)
            <span class="badge ceremony-yes bypass-badge">Yes</span>
          @else
            <span class="td-dim bypass-badge">—</span>
          @endif
        </td>
        <td style="white-space:nowrap">
          <button class="edit-btn"
            data-url="{{ route('admin.guests.update', $guest) }}"
            data-mobile="{{ $guest->mobile }}"
            data-name="{{ $guest->name }}"
            data-notes="{{ $guest->notes }}"
            data-side="{{ $guest->side }}"
            data-attending="{{ $attStatus === 'pending' ? '' : $attStatus }}"
            data-plus="{{ $guest->plus_ones }}"
            data-nickname="{{ $guest->nickname ?? '' }}"
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
        <td colspan="{{ $showSide ? 14 : 13 }}" style="text-align:center;color:#8FA3B8;padding:32px;">
          {{ $emptyMessage ?? 'No guests yet.' }}
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
