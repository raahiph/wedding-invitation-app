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
  <form method="POST" action="{{ route('admin.guests.store') }}">
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

{{-- Guest Table --}}
<div class="tbl-filters">
  <input class="form-input tbl-search" type="search" id="guest-search" placeholder="Search mobile, name…" autocomplete="off">
  <select class="form-input tbl-select" id="guest-status">
    <option value="">All statuses</option>
    <option value="yes">Attending</option>
    <option value="no">Not attending</option>
    <option value="pending">Pending RSVP</option>
  </select>
  <select class="form-input tbl-select" id="guest-side">
    <option value="">All sides</option>
    <option value="groom">{{ $wedding['groom'] }}'s side</option>
    <option value="bride">{{ $wedding['bride'] }}'s side</option>
    <option value="other">Other</option>
  </select>
  <span class="tbl-count" id="guest-count"></span>
</div>

<div class="tbl-wrap">
  <table id="guest-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Mobile</th>
        <th>Name</th>
        <th>Side</th>
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
        <td><span class="badge {{ $sideCls }} guest-side">{{ $sideLabel }}</span></td>
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
          <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}"
                onsubmit="return confirm('Remove {{ $guest->mobile }}?')" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="del-btn" style="margin-left:8px">Remove</button>
          </form>
        </td>
      </tr>
      @empty
      <tr id="empty-row">
        <td colspan="10" style="text-align:center;color:#8FA3B8;padding:32px;">No guests yet.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Edit Guest Modal --}}
<div id="edit-modal" class="modal-backdrop" style="display:none" aria-modal="true" role="dialog">
  <div class="modal">
    <div class="modal-header">
      <div>
        <p class="modal-title">Edit Guest</p>
        <p class="modal-subtitle" id="modal-mobile"></p>
      </div>
      <button class="modal-close" id="modal-close" aria-label="Close">✕</button>
    </div>
    <div class="modal-body">
      <div class="s-grid2">
        <div class="s-field">
          <label>Name</label>
          <input class="form-input" style="width:100%" type="text" id="modal-name" maxlength="120">
        </div>
        <div class="s-field">
          <label>Notes</label>
          <input class="form-input" style="width:100%" type="text" id="modal-notes" maxlength="255">
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field" style="grid-column:1/-1">
          <label>RSVP Name</label>
          <input class="form-input" style="width:100%" type="text" id="modal-rsvp-name" maxlength="120" placeholder="As submitted by guest">
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field">
          <label>Side</label>
          <select class="form-input" style="width:100%" id="modal-side">
            <option value="groom">{{ $wedding['groom'] }}'s side</option>
            <option value="bride">{{ $wedding['bride'] }}'s side</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="s-field">
          <label>Attending</label>
          <select class="form-input" style="width:100%" id="modal-attending">
            <option value="">Pending</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field">
          <label>+Guests</label>
          <select class="form-input" style="width:100%" id="modal-plus">
            @for($n = 0; $n <= 4; $n++)
              <option value="{{ $n }}">{{ $n }}</option>
            @endfor
          </select>
        </div>
        <div class="s-field">
          <label>Attends Ceremony</label>
          <div style="display:flex;align-items:center;gap:10px;padding-top:6px">
            <label class="toggle-switch" title="Toggle ceremony attendance">
              <input type="checkbox" id="modal-ceremony">
              <span class="toggle-slider"></span>
            </label>
            <span id="modal-ceremony-label" style="font-size:12px;color:var(--muted)">No</span>
          </div>
        </div>
      </div>
      <div class="s-field" id="modal-session-field" style="margin-top:4px">
        <label>Reception Session</label>
        <select class="form-input" style="width:100%;max-width:200px" id="modal-session">
          <option value="">— Unassigned —</option>
          <option value="1">Session 1 ({{ $wedding['session1_start'] }} – {{ $wedding['session1_end'] }})</option>
          <option value="2">Session 2 ({{ $wedding['session2_start'] }} – {{ $wedding['session2_end'] }})</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="form-btn" id="modal-save">Save Changes</button>
      <p class="modal-msg" id="modal-msg"></p>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // ── Table filter ─────────────────────────────────
  const search  = document.getElementById('guest-search');
  const statusSel = document.getElementById('guest-status');
  const sideSel   = document.getElementById('guest-side');
  const tbody   = document.getElementById('guest-tbody');
  const counter = document.getElementById('guest-count');
  const total   = tbody ? tbody.querySelectorAll('tr[data-status]').length : 0;

  function filter() {
    const q    = search.value.trim().toLowerCase();
    const s    = statusSel.value;
    const sd   = sideSel.value;
    const rows = tbody.querySelectorAll('tr[data-status]');
    let visible = 0;
    rows.forEach(row => {
      const matchQ  = !q  || row.textContent.toLowerCase().includes(q);
      const matchS  = !s  || row.dataset.status === s;
      const matchSd = !sd || row.dataset.side   === sd;
      const show    = matchQ && matchS && matchSd;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    counter.textContent = visible === total ? total + ' guests' : visible + ' of ' + total;
  }

  if (search) {
    search.addEventListener('input', filter);
    statusSel.addEventListener('change', filter);
    sideSel.addEventListener('change', filter);
    filter();
  }

  // ── Edit modal ────────────────────────────────────
  const modal      = document.getElementById('edit-modal');
  const modalClose = document.getElementById('modal-close');
  const modalSave  = document.getElementById('modal-save');
  const modalMsg   = document.getElementById('modal-msg');
  let activeRow = null, activeUrl = null;

  function openModal(btn) {
    activeRow = btn.closest('tr');
    activeUrl = btn.dataset.url;
    const ceremonyChecked = btn.dataset.ceremony === '1';
    document.getElementById('modal-mobile').textContent   = btn.dataset.mobile;
    document.getElementById('modal-name').value           = btn.dataset.name      || '';
    document.getElementById('modal-notes').value          = btn.dataset.notes     || '';
    document.getElementById('modal-rsvp-name').value      = btn.dataset.rsvpName  || '';
    document.getElementById('modal-side').value           = btn.dataset.side      || 'other';
    document.getElementById('modal-attending').value      = btn.dataset.attending || '';
    document.getElementById('modal-plus').value           = btn.dataset.plus      || '0';
    document.getElementById('modal-ceremony').checked     = ceremonyChecked;
    document.getElementById('modal-ceremony-label').textContent = ceremonyChecked ? 'Yes' : 'No';
    document.getElementById('modal-session').value        = btn.dataset.session   || '';
    document.getElementById('modal-session-field').style.display = ceremonyChecked ? 'none' : 'block';
    modalMsg.textContent = '';
    modal.style.display = 'flex';
    document.getElementById('modal-name').focus();
  }

  function closeModal() {
    modal.style.display = 'none';
    activeRow = null; activeUrl = null;
  }

  // ── Live stats polling ────────────────────────────
  const statsUrl = '{{ route("admin.stats") }}';
  function refreshStats() {
    fetch(statsUrl)
      .then(r => r.json())
      .then(d => {
        document.getElementById('stat-total').textContent    = d.total;
        document.getElementById('stat-attending').textContent = d.attending;
        document.getElementById('stat-heads').textContent    = d.heads;
        document.getElementById('stat-pending').textContent  = d.pending;
        document.getElementById('stat-groom').textContent   = d.groom;
        document.getElementById('stat-bride').textContent   = d.bride;
      })
      .catch(() => {});
  }
  setInterval(refreshStats, 15000);

  // ── Edit modal ────────────────────────────────────
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn));
  });

  document.getElementById('modal-ceremony').addEventListener('change', function () {
    document.getElementById('modal-ceremony-label').textContent = this.checked ? 'Yes' : 'No';
    document.getElementById('modal-session-field').style.display = this.checked ? 'none' : 'block';
    if (this.checked) document.getElementById('modal-session').value = '';
  });

  modalClose.addEventListener('click', closeModal);
  modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  modalSave.addEventListener('click', function () {
    const attending  = document.getElementById('modal-attending').value;
    const plusOnes   = document.getElementById('modal-plus').value;
    const name       = document.getElementById('modal-name').value;
    const notes      = document.getElementById('modal-notes').value;
    const side       = document.getElementById('modal-side').value;
    const rsvpName   = document.getElementById('modal-rsvp-name').value;
    const ceremony   = document.getElementById('modal-ceremony').checked ? '1' : '0';
    const session    = document.getElementById('modal-session').value;

    const body = new URLSearchParams({ _token: csrfToken, name, notes, side, attending, plus_ones: plusOnes, full_name: rsvpName, attends_ceremony: ceremony, session });
    modalSave.disabled = true;
    modalMsg.textContent = '';

    fetch(activeUrl, { method: 'POST', body })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { modalMsg.textContent = 'Something went wrong.'; return; }

        // Update row data
        const statusVal = attending === 'yes' ? 'yes' : attending === 'no' ? 'no' : 'pending';
        activeRow.dataset.status = statusVal;

        activeRow.dataset.side = side;
        activeRow.querySelector('.guest-name').textContent = name || '—';

        const sideLabels = { groom: '{{ $wedding['groom'] }}', bride: '{{ $wedding['bride'] }}', other: 'Other' };
        const sideBadge = activeRow.querySelector('.guest-side');
        sideBadge.className = 'badge side-' + side + ' guest-side';
        sideBadge.textContent = sideLabels[side] || 'Other';

        const badge = activeRow.querySelector('.attending-badge');
        badge.className = 'badge attending-badge badge-' + statusVal;
        badge.textContent = statusVal === 'yes' ? 'Yes' : statusVal === 'no' ? 'No' : 'Pending';

        activeRow.querySelector('.plus-ones').textContent = attending ? plusOnes : '—';

        // Update RSVP name cell (4th td — index 4)
        activeRow.querySelectorAll('td')[4].textContent = rsvpName || '—';

        // Update ceremony button in table
        const ceremonyBtn = activeRow.querySelector('.ceremony-btn');
        if (ceremonyBtn) {
          const on = ceremony === '1';
          ceremonyBtn.className = 'ceremony-btn ' + (on ? 'ceremony-yes' : 'ceremony-no');
          ceremonyBtn.textContent = on ? 'Yes' : 'No';
        }

        // Sync edit button data for next open
        const btn = activeRow.querySelector('.edit-btn');
        btn.dataset.name      = name;
        btn.dataset.notes     = notes;
        btn.dataset.side      = side;
        btn.dataset.attending = attending;
        btn.dataset.plus      = plusOnes;
        btn.dataset.rsvpName  = rsvpName;
        btn.dataset.ceremony  = ceremony;
        btn.dataset.session   = session;

        // Update session badge in table
        const sessionTd = activeRow.querySelectorAll('td')[8]; // Session column
        if (sessionTd) {
          sessionTd.innerHTML = (ceremony !== '1' && session)
            ? `<span class="badge" style="background:#EAF1FB;color:#2A4E96;border-color:#C3D4F5">S${session}</span>`
            : '<span class="td-dim">—</span>';
        }

        refreshStats();
        filter();
        closeModal();
      })
      .catch(() => { modalMsg.textContent = 'Request failed.'; })
      .finally(() => { modalSave.disabled = false; });
  });
})();
</script>
@endpush

@endsection
