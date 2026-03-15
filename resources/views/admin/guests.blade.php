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

{{-- Guest Table --}}
<div class="tbl-filters">
  <input class="form-input tbl-search" type="search" id="guest-search" placeholder="Search mobile, name…" autocomplete="off">
  <select class="form-input tbl-select" id="guest-status">
    <option value="">All statuses</option>
    <option value="yes">Attending</option>
    <option value="no">Not attending</option>
    <option value="pending">Pending RSVP</option>
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
      @endphp
      <tr data-status="{{ $attStatus }}">
        <td class="td-dim">{{ $i + 1 }}</td>
        <td class="td-hi">{{ $guest->mobile }}</td>
        <td class="guest-name">{{ $guest->name ?: '—' }}</td>
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
        <td colspan="9" style="text-align:center;color:#8FA3B8;padding:32px;">No guests on {{ $sideName }}'s side yet.</td>
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
          <label>Mobile</label>
          <input class="form-input" style="width:100%" type="tel" id="modal-mobile-input" maxlength="20">
        </div>
        <div class="s-field">
          <label>Name</label>
          <input class="form-input" style="width:100%" type="text" id="modal-name" maxlength="120">
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field" style="grid-column:1/-1">
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
  const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;
  const AJAX_HDRS  = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
  const currentSide = '{{ $side }}';

  // ── Helpers ───────────────────────────────────────
  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function showFlash(msg, err = false) {
    let el = document.getElementById('add-flash');
    if (!el) {
      el = document.createElement('p');
      el.id = 'add-flash'; el.className = 'flash';
      document.getElementById('add-guest-form').after(el);
    }
    el.className = 'flash' + (err ? ' err' : '');
    el.textContent = msg;
    clearTimeout(el._t);
    el._t = setTimeout(() => { el.textContent = ''; }, 4000);
  }

  function renumber() {
    tbody.querySelectorAll('tr[data-status]').forEach((r, i) => {
      const td = r.querySelector('td.td-dim');
      if (td) td.textContent = i + 1;
    });
  }

  function refreshStats() {
    const rows = [...tbody.querySelectorAll('tr[data-status]')];
    const total = rows.length;
    const attending = rows.filter(r => r.dataset.status === 'yes').length;
    const pending   = rows.filter(r => r.dataset.status === 'pending').length;
    let heads = 0;
    rows.filter(r => r.dataset.status === 'yes').forEach(r => {
      const td = r.querySelector('.plus-ones');
      heads += 1 + (parseInt(td?.textContent) || 0);
    });
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-attending').textContent = attending;
    document.getElementById('stat-heads').textContent     = heads;
    document.getElementById('stat-pending').textContent   = pending;
  }

  // ── Table filter ──────────────────────────────────
  const search    = document.getElementById('guest-search');
  const statusSel = document.getElementById('guest-status');
  const tbody     = document.getElementById('guest-tbody');
  const counter   = document.getElementById('guest-count');

  function filter() {
    const q  = search.value.trim().toLowerCase();
    const s  = statusSel.value;
    const rows = tbody.querySelectorAll('tr[data-status]');
    let visible = 0;
    rows.forEach(row => {
      const show = (!q || row.textContent.toLowerCase().includes(q)) && (!s || row.dataset.status === s);
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    const total = rows.length;
    counter.textContent = visible === total ? total + ' guests' : visible + ' of ' + total;
  }

  search.addEventListener('input', filter);
  statusSel.addEventListener('change', filter);
  filter();

  // ── Build new guest row ───────────────────────────
  function buildRow(g) {
    return `<tr data-status="pending" data-side="${esc(g.side)}">
      <td class="td-dim"></td>
      <td class="td-hi">${esc(g.mobile)}</td>
      <td class="guest-name">${esc(g.name || '—')}</td>
      <td>—</td>
      <td><span class="badge attending-badge badge-pending">Pending</span></td>
      <td class="plus-ones">—</td>
      <td><form method="POST" action="${esc(g.ceremony_url)}">
        <input type="hidden" name="_token" value="${esc(csrfToken)}">
        <button type="submit" class="ceremony-btn ceremony-no">No</button>
      </form></td>
      <td><span class="td-dim">—</span></td>
      <td class="td-dim">—</td>
      <td style="white-space:nowrap">
        <button class="edit-btn"
          data-url="${esc(g.update_url)}" data-mobile="${esc(g.mobile)}"
          data-name="${esc(g.name)}" data-notes="${esc(g.notes)}"
          data-side="${esc(g.side)}" data-attending="" data-plus="0"
          data-rsvp-name="" data-ceremony="0" data-session="">Edit</button>
        <form method="POST" action="${esc(g.destroy_url)}" style="display:inline">
          <input type="hidden" name="_token" value="${esc(csrfToken)}">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="del-btn" style="margin-left:8px">Remove</button>
        </form>
      </td>
    </tr>`;
  }

  // ── Add guest (AJAX) ──────────────────────────────
  document.getElementById('add-guest-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('.form-btn');
    btn.disabled = true;
    fetch(this.action, { method: 'POST', headers: AJAX_HDRS, body: new URLSearchParams(new FormData(this)) })
      .then(r => r.json())
      .then(d => {
        if (!d.ok || !d.created) { showFlash(d.message, true); return; }
        // Only add to table if the guest is on the current side
        if (d.guest.side === currentSide) {
          tbody.insertAdjacentHTML('afterbegin', buildRow(d.guest));
          tbody.querySelector('tr:first-child .edit-btn').addEventListener('click', function() { openModal(this); });
          renumber();
        }
        refreshStats();
        filter();
        this.reset();
        showFlash('Guest added.');
      })
      .catch(() => showFlash('Request failed.', true))
      .finally(() => { btn.disabled = false; });
  });

  // ── Ceremony toggle & Delete (AJAX, delegated) ────
  document.addEventListener('submit', function(e) {
    const form = e.target;

    if (form.querySelector('.ceremony-btn')) {
      e.preventDefault();
      const btn = form.querySelector('.ceremony-btn');
      fetch(form.action, { method: 'POST', headers: AJAX_HDRS, body: new URLSearchParams(new FormData(form)) })
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          const on = d.attends_ceremony;
          btn.className = 'ceremony-btn ' + (on ? 'ceremony-yes' : 'ceremony-no');
          btn.textContent = on ? 'Yes' : 'No';
          const row = btn.closest('tr');
          const editBtn = row.querySelector('.edit-btn');
          if (editBtn) { editBtn.dataset.ceremony = on ? '1' : '0'; if (on) editBtn.dataset.session = ''; }
          const sessionTd = row.querySelectorAll('td')[7];
          if (sessionTd && on) sessionTd.innerHTML = '<span class="td-dim">—</span>';
        });
      return;
    }

    if (form.querySelector('.del-btn')) {
      e.preventDefault();
      const row = form.closest('tr');
      const mobile = row.querySelector('.td-hi').textContent;
      if (!confirm('Remove ' + mobile + '?')) return;
      fetch(form.action, { method: 'POST', headers: AJAX_HDRS, body: new URLSearchParams(new FormData(form)) })
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          row.remove();
          renumber();
          refreshStats();
          filter();
        });
    }
  });

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
    document.getElementById('modal-mobile').textContent        = btn.dataset.mobile;
    document.getElementById('modal-mobile-input').value       = btn.dataset.mobile;
    document.getElementById('modal-name').value                = btn.dataset.name      || '';
    document.getElementById('modal-notes').value               = btn.dataset.notes     || '';
    document.getElementById('modal-rsvp-name').value           = btn.dataset.rsvpName  || '';
    document.getElementById('modal-side').value                = btn.dataset.side      || 'other';
    document.getElementById('modal-attending').value           = btn.dataset.attending || '';
    document.getElementById('modal-plus').value                = btn.dataset.plus      || '0';
    document.getElementById('modal-ceremony').checked          = ceremonyChecked;
    document.getElementById('modal-ceremony-label').textContent = ceremonyChecked ? 'Yes' : 'No';
    document.getElementById('modal-session').value             = btn.dataset.session   || '';
    document.getElementById('modal-session-field').style.display = ceremonyChecked ? 'none' : 'block';
    modalMsg.textContent = '';
    modal.style.display = 'flex';
    document.getElementById('modal-name').focus();
  }

  function closeModal() { modal.style.display = 'none'; activeRow = null; activeUrl = null; }

  document.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', () => openModal(btn)));

  document.getElementById('modal-ceremony').addEventListener('change', function () {
    document.getElementById('modal-ceremony-label').textContent = this.checked ? 'Yes' : 'No';
    document.getElementById('modal-session-field').style.display = this.checked ? 'none' : 'block';
    if (this.checked) document.getElementById('modal-session').value = '';
  });

  modalClose.addEventListener('click', closeModal);
  modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  modalSave.addEventListener('click', function () {
    const mobile    = document.getElementById('modal-mobile-input').value;
    const attending = document.getElementById('modal-attending').value;
    const plusOnes  = document.getElementById('modal-plus').value;
    const name      = document.getElementById('modal-name').value;
    const notes     = document.getElementById('modal-notes').value;
    const side      = document.getElementById('modal-side').value;
    const rsvpName  = document.getElementById('modal-rsvp-name').value;
    const ceremony  = document.getElementById('modal-ceremony').checked ? '1' : '0';
    const session   = document.getElementById('modal-session').value;

    modalSave.disabled = true; modalMsg.textContent = '';

    fetch(activeUrl, {
      method: 'POST', headers: AJAX_HDRS,
      body: new URLSearchParams({ _token: csrfToken, mobile, name, notes, side, attending, plus_ones: plusOnes, full_name: rsvpName, attends_ceremony: ceremony, session })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.ok) { modalMsg.textContent = data.message || 'Something went wrong.'; return; }
      const statusVal = attending === 'yes' ? 'yes' : attending === 'no' ? 'no' : 'pending';
      activeRow.dataset.status = statusVal;
      activeRow.querySelector('.td-hi').textContent      = data.mobile;
      activeRow.querySelector('.guest-name').textContent = name || '—';

      const badge = activeRow.querySelector('.attending-badge');
      badge.className = 'badge attending-badge badge-' + statusVal;
      badge.textContent = statusVal === 'yes' ? 'Yes' : statusVal === 'no' ? 'No' : 'Pending';

      activeRow.querySelector('.plus-ones').textContent = attending ? plusOnes : '—';
      activeRow.querySelectorAll('td')[3].textContent = rsvpName || '—';

      const ceremonyBtn = activeRow.querySelector('.ceremony-btn');
      if (ceremonyBtn) {
        const on = ceremony === '1';
        ceremonyBtn.className = 'ceremony-btn ' + (on ? 'ceremony-yes' : 'ceremony-no');
        ceremonyBtn.textContent = on ? 'Yes' : 'No';
      }

      Object.assign(activeRow.querySelector('.edit-btn').dataset, { mobile: data.mobile, name, notes, side, attending, plus: plusOnes, rsvpName, ceremony, session });
      document.getElementById('modal-mobile').textContent = data.mobile;

      const sessionTd = activeRow.querySelectorAll('td')[7];
      if (sessionTd) {
        sessionTd.innerHTML = (ceremony !== '1' && session)
          ? `<span class="badge" style="background:#EAF1FB;color:#2A4E96;border-color:#C3D4F5">S${session}</span>`
          : '<span class="td-dim">—</span>';
      }

      refreshStats(); filter(); closeModal();
    })
    .catch(() => { modalMsg.textContent = 'Request failed.'; })
    .finally(() => { modalSave.disabled = false; });
  });
})();
</script>
@endpush

@endsection
