(function () {
  const CFG       = window.GUEST_CFG;
  const csrf      = CFG.csrfToken;
  const HDRS      = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

  // ── Helpers ───────────────────────────────────────
  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function showFlash(msg, err) {
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

  // ── Stats ─────────────────────────────────────────
  function refreshStats() {
    if (CFG.statsMode === 'server') {
      fetch(CFG.statsUrl, { headers: HDRS, cache: 'no-store' })
        .then(r => r.json())
        .then(d => {
          document.getElementById('stat-total').textContent     = d.total;
          document.getElementById('stat-attending').textContent = d.attending;
          document.getElementById('stat-heads').textContent     = d.heads;
          document.getElementById('stat-pending').textContent   = d.pending;
          if (document.getElementById('stat-groom')) document.getElementById('stat-groom').textContent = d.groom;
          if (document.getElementById('stat-bride')) document.getElementById('stat-bride').textContent = d.bride;
        }).catch(() => {});
    } else {
      const rows     = [...tbody.querySelectorAll('tr[data-status]')];
      const attending = rows.filter(r => r.dataset.status === 'yes').length;
      const pending   = rows.filter(r => r.dataset.status === 'pending').length;
      let heads = 0;
      rows.filter(r => r.dataset.status === 'yes').forEach(r => {
        heads += 1 + (parseInt(r.querySelector('.plus-ones')?.textContent) || 0);
      });
      document.getElementById('stat-total').textContent     = rows.length;
      document.getElementById('stat-attending').textContent = attending;
      document.getElementById('stat-heads').textContent     = heads;
      document.getElementById('stat-pending').textContent   = pending;
    }
  }

  if (CFG.statsMode === 'server') setInterval(refreshStats, 15000);

  // ── Table filter ──────────────────────────────────
  const search    = document.getElementById('guest-search');
  const statusSel = document.getElementById('guest-status');
  const sideSel   = document.getElementById('guest-side'); // null on guests page
  const tbody     = document.getElementById('guest-tbody');
  const counter   = document.getElementById('guest-count');

  function filter() {
    const q  = search.value.trim().toLowerCase();
    const s  = statusSel.value;
    const sd = sideSel ? sideSel.value : '';
    const rows = tbody.querySelectorAll('tr[data-status]');
    let visible = 0;
    rows.forEach(row => {
      const show = (!q  || row.textContent.toLowerCase().includes(q))
                && (!s  || row.dataset.status === s)
                && (!sd || row.dataset.side   === sd);
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    const total = rows.length;
    counter.textContent = visible === total ? total + ' guests' : visible + ' of ' + total;
  }

  search.addEventListener('input', filter);
  statusSel.addEventListener('change', filter);
  if (sideSel) sideSel.addEventListener('change', filter);
  filter();

  // ── Bulk selection ────────────────────────────────
  const selectAll     = document.getElementById('select-all');
  const bulkBar       = document.getElementById('bulk-bar');
  const bulkCount     = document.getElementById('bulk-count');
  const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
  const bulkClearBtn  = document.getElementById('bulk-clear-btn');

  function getChecked() {
    return [...tbody.querySelectorAll('.row-cb:checked')];
  }

  function updateBulkBar() {
    const checked = getChecked();
    const n = checked.length;
    bulkBar.style.display = n > 0 ? 'flex' : 'none';
    bulkCount.textContent = n + ' row' + (n === 1 ? '' : 's') + ' selected';
    selectAll.indeterminate = n > 0 && n < tbody.querySelectorAll('.row-cb').length;
    selectAll.checked = n > 0 && n === tbody.querySelectorAll('tr[data-id] .row-cb').length;
  }

  tbody.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-cb')) updateBulkBar();
  });

  selectAll.addEventListener('change', function () {
    tbody.querySelectorAll('tr[data-id] .row-cb').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
  });

  bulkClearBtn.addEventListener('click', function () {
    tbody.querySelectorAll('.row-cb').forEach(cb => { cb.checked = false; });
    selectAll.checked = false;
    selectAll.indeterminate = false;
    bulkBar.style.display = 'none';
  });

  bulkDeleteBtn.addEventListener('click', function () {
    const checked = getChecked();
    if (!checked.length) return;
    const n = checked.length;
    if (!confirm('Remove ' + n + ' guest' + (n === 1 ? '' : 's') + '?')) return;
    const ids = checked.map(cb => cb.value);
    bulkDeleteBtn.disabled = true;
    const body = new URLSearchParams({ _token: csrf });
    ids.forEach(id => body.append('ids[]', id));
    fetch('/admin/guests/bulk-destroy', { method: 'POST', headers: HDRS, body })
      .then(r => r.json())
      .then(d => {
        if (!d.ok) return;
        checked.forEach(cb => cb.closest('tr').remove());
        renumber();
        refreshStats();
        filter();
        selectAll.checked = false;
        selectAll.indeterminate = false;
        bulkBar.style.display = 'none';
      })
      .catch(() => {})
      .finally(() => { bulkDeleteBtn.disabled = false; });
  });

  // ── Build new guest row ───────────────────────────
  function buildRow(g) {
    const sLabel  = CFG.sideLabels[g.side] || 'Other';
    const sideCol = CFG.hasSideCol
      ? `<td><span class="badge side-${esc(g.side)} guest-side">${esc(sLabel)}</span></td>`
      : '';
    return `<tr data-status="pending" data-side="${esc(g.side)}" data-id="${esc(g.id)}">
      <td style="padding-right:0"><input type="checkbox" class="row-cb" value="${esc(g.id)}"></td>
      <td class="td-dim"></td>
      <td class="td-hi">${esc(g.mobile)}</td>
      <td class="guest-name">${esc(g.name || '—')}</td>
      ${sideCol}
      <td>—</td>
      <td><span class="badge attending-badge badge-pending">Pending</span></td>
      <td class="plus-ones">${esc(g.plus_ones ?? 0)}</td>
      <td><form method="POST" action="${esc(g.ceremony_url)}">
        <input type="hidden" name="_token" value="${esc(csrf)}">
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
          <input type="hidden" name="_token" value="${esc(csrf)}">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="del-btn" style="margin-left:8px">Remove</button>
        </form>
      </td>
    </tr>`;
  }

  // ── Add guest ─────────────────────────────────────
  document.getElementById('add-guest-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('.form-btn');
    btn.disabled = true;
    fetch(this.action, { method: 'POST', headers: HDRS, body: new URLSearchParams(new FormData(this)) })
      .then(r => r.json())
      .then(d => {
        if (!d.ok || !d.created) { showFlash(d.message, true); return; }
        if (!CFG.currentSide || d.guest.side === CFG.currentSide) {
          tbody.insertAdjacentHTML('afterbegin', buildRow(d.guest));
          tbody.querySelector('tr:first-child .edit-btn').addEventListener('click', function () { openModal(this); });
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

  // ── Ceremony toggle & Delete (delegated) ──────────
  document.addEventListener('submit', function (e) {
    const form = e.target;

    if (form.querySelector('.ceremony-btn')) {
      e.preventDefault();
      const btn = form.querySelector('.ceremony-btn');
      fetch(form.action, { method: 'POST', headers: HDRS, body: new URLSearchParams(new FormData(form)) })
        .then(r => r.json())
        .then(d => {
          if (!d.ok) return;
          const on = d.attends_ceremony;
          btn.className   = 'ceremony-btn ' + (on ? 'ceremony-yes' : 'ceremony-no');
          btn.textContent = on ? 'Yes' : 'No';
          const row     = btn.closest('tr');
          const editBtn = row.querySelector('.edit-btn');
          if (editBtn) { editBtn.dataset.ceremony = on ? '1' : '0'; if (on) editBtn.dataset.session = ''; }
          const sessionTd = row.querySelectorAll('td')[CFG.sessionCol];
          if (sessionTd && on) sessionTd.innerHTML = '<span class="td-dim">—</span>';
        });
      return;
    }

    if (form.querySelector('.del-btn')) {
      e.preventDefault();
      const row    = form.closest('tr');
      const mobile = row.querySelector('.td-hi').textContent;
      if (!confirm('Remove ' + mobile + '?')) return;
      fetch(form.action, { method: 'POST', headers: HDRS, body: new URLSearchParams(new FormData(form)) })
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
    const on = btn.dataset.ceremony === '1';
    document.getElementById('modal-mobile').textContent        = btn.dataset.mobile;
    document.getElementById('modal-mobile-input').value       = btn.dataset.mobile;
    document.getElementById('modal-name').value                = btn.dataset.name      || '';
    document.getElementById('modal-notes').value               = btn.dataset.notes     || '';
    document.getElementById('modal-rsvp-name').value           = btn.dataset.rsvpName  || '';
    document.getElementById('modal-side').value                = btn.dataset.side      || 'other';
    document.getElementById('modal-attending').value           = btn.dataset.attending || '';
    document.getElementById('modal-plus').value                = btn.dataset.plus      || '0';
    document.getElementById('modal-ceremony').checked          = on;
    document.getElementById('modal-ceremony-label').textContent = on ? 'Yes' : 'No';
    document.getElementById('modal-session').value             = btn.dataset.session   || '';
    document.getElementById('modal-session-field').style.display = on ? 'none' : 'block';
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
      method: 'POST', headers: HDRS,
      body: new URLSearchParams({ _token: csrf, mobile, name, notes, side, attending, plus_ones: plusOnes, full_name: rsvpName, attends_ceremony: ceremony, session })
    })
    .then(r => r.json())
    .then(data => {
      if (!data.ok) { modalMsg.textContent = data.message || 'Something went wrong.'; return; }

      const statusVal = attending === 'yes' ? 'yes' : attending === 'no' ? 'no' : 'pending';
      activeRow.dataset.status = statusVal;
      activeRow.dataset.side   = side;
      activeRow.querySelector('.td-hi').textContent      = data.mobile;
      activeRow.querySelector('.guest-name').textContent = name || '—';

      if (CFG.hasSideCol) {
        const sideBadge = activeRow.querySelector('.guest-side');
        if (sideBadge) {
          sideBadge.className   = 'badge side-' + side + ' guest-side';
          sideBadge.textContent = CFG.sideLabels[side] || 'Other';
        }
      }

      const badge = activeRow.querySelector('.attending-badge');
      badge.className   = 'badge attending-badge badge-' + statusVal;
      badge.textContent = statusVal === 'yes' ? 'Yes' : statusVal === 'no' ? 'No' : 'Pending';

      activeRow.querySelector('.plus-ones').textContent = plusOnes;
      activeRow.querySelectorAll('td')[CFG.rsvpNameCol].textContent = rsvpName || '—';

      const ceremonyBtn = activeRow.querySelector('.ceremony-btn');
      if (ceremonyBtn) {
        const on = ceremony === '1';
        ceremonyBtn.className   = 'ceremony-btn ' + (on ? 'ceremony-yes' : 'ceremony-no');
        ceremonyBtn.textContent = on ? 'Yes' : 'No';
      }

      Object.assign(activeRow.querySelector('.edit-btn').dataset,
        { mobile: data.mobile, name, notes, side, attending, plus: plusOnes, rsvpName, ceremony, session });
      document.getElementById('modal-mobile').textContent = data.mobile;

      const sessionTd = activeRow.querySelectorAll('td')[CFG.sessionCol];
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
  // ── CSV Import ────────────────────────────────────
  const csvFile      = document.getElementById('csv-file');
  const csvImportBtn = document.getElementById('csv-import-btn');
  const csvFlash     = document.getElementById('csv-flash');
  const csvFileLabel = document.getElementById('csv-file-label');

  function showCsvFlash(msg, err) {
    csvFlash.textContent = msg;
    csvFlash.className = 'flash' + (err ? ' err' : '');
    csvFlash.style.display = '';
    clearTimeout(csvFlash._t);
    csvFlash._t = setTimeout(() => { csvFlash.style.display = 'none'; }, 6000);
  }

  if (csvFile) {
    csvFile.addEventListener('change', function () {
      csvFileLabel.textContent = this.files.length ? this.files[0].name : 'Choose CSV file…';
    });
  }

  if (csvImportBtn) {
    csvImportBtn.addEventListener('click', function () {
      if (!csvFile.files.length) { showCsvFlash('Please select a CSV file.', true); return; }
      const fd = new FormData();
      fd.append('csv', csvFile.files[0]);
      fd.append('_token', csrf);
      csvImportBtn.disabled = true;
      fetch('/admin/guests/import', { method: 'POST', headers: HDRS, body: fd })
        .then(r => r.json())
        .then(d => {
          let msg = `Imported: ${d.imported}, Skipped (duplicates): ${d.skipped}`;
          if (d.errors.length) msg += `. Errors: ${d.errors.join('; ')}`;
          showCsvFlash(msg, d.errors.length > 0);
          if (d.imported > 0) { refreshStats(); setTimeout(() => location.reload(), 1500); }
          csvFile.value = '';
          csvFileLabel.textContent = 'Choose CSV file…';
        })
        .catch(() => showCsvFlash('Request failed.', true))
        .finally(() => { csvImportBtn.disabled = false; });
    });
  }
})();
