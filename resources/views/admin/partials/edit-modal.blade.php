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
          <label>Nickname</label>
          <input class="form-input" style="width:100%" type="text" id="modal-nickname" maxlength="120" placeholder="Optional nickname">
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field" style="grid-column:1/-1">
          <label>Notes</label>
          <input class="form-input" style="width:100%" type="text" id="modal-notes" maxlength="255">
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
          <label>Category</label>
          <select class="form-input" style="width:100%" id="modal-category">
            <option value="">— None —</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="s-grid2">
        <div class="s-field">
          <label>Attending</label>
          <select class="form-input" style="width:100%" id="modal-attending">
            <option value="">Pending</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
        <div class="s-field">
          <label>+Guests</label>
          <select class="form-input" style="width:100%" id="modal-plus">
            @for($n = 0; $n <= 4; $n++)
              <option value="{{ $n }}">{{ $n }}</option>
            @endfor
          </select>
        </div>
      </div>
      <div class="s-grid2">
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
