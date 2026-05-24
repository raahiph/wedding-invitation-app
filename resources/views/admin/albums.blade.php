@extends('admin.layout')

@section('title', 'Albums')
@section('page-title', 'Albums')
@section('page-subtitle', 'Create shareable photo galleries to send to guests')

@section('content')

  {{-- Create new album --}}
  <div class="card" style="margin-bottom:20px;">
    <p class="card-title">New Album</p>
    <form id="create-form" onsubmit="createAlbum(event)">
      @csrf
      <div class="form-row">
        <input type="text" name="name" class="form-input" placeholder="e.g. Wedding Day, Pre-Wedding Shoot…" maxlength="120" required>
        <button type="submit" class="form-btn" id="create-btn">Create</button>
      </div>
    </form>
  </div>

  {{-- Albums list --}}
  @if($albums->isEmpty())
    <div style="text-align:center;padding:60px 24px;color:#8FA3B8;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;">
      No albums yet. Create one above.
    </div>
  @else
    <div class="tbl-wrap">
      <table id="albums-table">
        <thead>
          <tr>
            <th>Album Name</th>
            <th style="width:80px;text-align:center;">Photos</th>
            <th style="width:130px;">Created</th>
            <th style="width:240px;">Share Link</th>
            <th style="width:140px;"></th>
          </tr>
        </thead>
        <tbody id="album-list">
          @foreach($albums as $album)
            <tr id="row-{{ $album->id }}">
              <td class="td-hi">{{ $album->name }}</td>
              <td style="text-align:center;color:#6B7E96;">{{ $album->photos_count }}</td>
              <td class="td-dim">{{ $album->created_at->format('d M Y') }}</td>
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <input type="text" value="{{ $album->shareUrl() }}"
                         style="flex:1;font-size:10px;color:#6B7E96;border:1px solid #DDE2E8;border-radius:4px;padding:4px 8px;font-family:monospace;background:#F7F9FB;min-width:0;"
                         readonly onclick="this.select()">
                  <button onclick="copyLink('{{ $album->shareUrl() }}', this)"
                          style="flex-shrink:0;font-size:10px;padding:4px 10px;border:1px solid #C8D3DE;border-radius:4px;background:#fff;cursor:pointer;color:#3A4F65;font-family:inherit;white-space:nowrap;">
                    Copy
                  </button>
                </div>
              </td>
              <td style="text-align:right;">
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                  <a href="{{ route('admin.albums.manage', $album) }}"
                     style="font-size:11px;padding:5px 14px;border:1px solid #C8D3DE;border-radius:6px;color:#3A4F65;background:#fff;text-decoration:none;font-weight:500;">
                    Manage
                  </a>
                  <button onclick="deleteAlbum({{ $album->id }}, '{{ addslashes($album->name) }}')"
                          style="font-size:11px;padding:5px 12px;border:1px solid #F5C6CB;border-radius:6px;color:#C0392B;background:#FDF2F2;cursor:pointer;font-family:inherit;">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("admin/albums") }}';

function createAlbum(e) {
  e.preventDefault();
  const form = e.target;
  const btn  = document.getElementById('create-btn');
  const name = form.name.value.trim();
  if (!name) return;
  btn.disabled = true;
  btn.textContent = 'Creating…';

  fetch(BASE, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({ name }),
  }).then(r => r.json()).then(res => {
    if (res.ok && res.redirect) window.location.href = res.redirect;
    else { btn.disabled = false; btn.textContent = 'Create'; }
  }).catch(() => { btn.disabled = false; btn.textContent = 'Create'; });
}

function copyLink(url, btn) {
  navigator.clipboard.writeText(url).then(() => {
    const orig = btn.textContent;
    btn.textContent = 'Copied!';
    btn.style.color = '#5B9E6E';
    btn.style.borderColor = '#5B9E6E';
    setTimeout(() => { btn.textContent = orig; btn.style.color = ''; btn.style.borderColor = ''; }, 2000);
  });
}

function deleteAlbum(id, name) {
  if (!confirm('Delete album "' + name + '" and all its photos?')) return;
  fetch(BASE + '/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(res => { if (res.ok) document.getElementById('row-' + id)?.remove(); });
}
</script>
@endpush

@endsection
