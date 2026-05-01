<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Bulk Data Entry</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success py-2">
            <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="bulkForm">
        <?= Csrf::field() ?>

        <!-- Header card -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-event me-1"></i> Header — applies to all rows below
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="feed_date" class="form-control"
                               value="<?= htmlspecialchars($_POST['feed_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Shift</label>
                        <select name="shift_id" id="shiftSelect" class="form-select" required>
                            <option value="">— Select Shift —</option>
                            <?php foreach ($shifts as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                    <?= (int)($_POST['shift_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['shift_name']) ?> (<?= $s['hours'] ?> hrs)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Hour</label>
                        <select name="hour_no" id="hourSelect" class="form-select" required>
                            <option value="">— Select Shift first —</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Location</label>
                        <select name="location_id" class="form-select" required>
                            <option value="">— Select Location —</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>"
                                    <?= (int)($_POST['location_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc['code'] . ' — ' . $loc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail rows card -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check me-1"></i> Defect Rows</span>
                <button type="button" id="addRow" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Add Row
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle" id="rowsTable" style="font-size:13px">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Machine Type</th>
                            <th>Machine No</th>
                            <th>Defect Type</th>
                            <th style="width:100px">Quantity</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="rowsBody">
                        <!-- rows injected by JS; one empty row added on load -->
                    </tbody>
                </table>
                <div class="card-body py-2 text-muted" style="font-size:12px;background:#f8f9fa">
                    <i class="bi bi-info-circle me-1"></i>
                    Empty rows are skipped. Each saved row creates one Data Feed record.
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save All Rows
            </button>
            <button type="reset" class="btn btn-outline-secondary" onclick="setTimeout(resetRows, 0)">Clear</button>
            <a href="<?= BASE_URL ?>datafeed/list" class="btn btn-outline-secondary ms-auto">View Data List</a>
        </div>
    </form>
</div>

<script>
// ── Reference data embedded from PHP ────────────────────────────────────────
var shiftSlots   = <?= json_encode($shiftHourLabels) ?>;
var savedHourNo  = <?= (int)($_POST['hour_no'] ?? 0) ?>;
var machineTypes = <?= json_encode($machineTypes) ?>;
var allMachines  = <?= json_encode(array_map(function($m) {
    return [
        'id'           => (int)$m['id'],
        'machine_no'   => $m['machine_no'],
        'machine_name' => $m['machine_name'],
        'machine_type' => $m['machine_type'],
        'location_id'  => (int)($m['location_id'] ?? 0),
    ];
}, $machines)) ?>;
var allDefects   = <?= json_encode(array_map(function($d) {
    return [
        'id'           => (int)$d['id'],
        'defect_code'  => $d['defect_code'],
        'defect_name'  => $d['defect_name'],
        'machine_type' => $d['machine_type'],
    ];
}, $defects)) ?>;

// ── Header: Shift → Hour cascade ────────────────────────────────────────────
function buildHourDropdown(shiftId) {
    var sel = document.getElementById('hourSelect');
    sel.innerHTML = '';
    if (!shiftId) {
        sel.innerHTML = '<option value="">— Select Shift first —</option>';
        return;
    }
    var slots = shiftSlots[shiftId] || {};
    var keys = Object.keys(slots);
    if (!keys.length) {
        sel.innerHTML = '<option value="">— No hours defined —</option>';
        return;
    }
    var ph = document.createElement('option');
    ph.value = ''; ph.textContent = '— Select Hour —';
    sel.appendChild(ph);
    keys.forEach(function(no) {
        var opt = document.createElement('option');
        opt.value = no; opt.textContent = slots[no];
        if (savedHourNo == no) opt.selected = true;
        sel.appendChild(opt);
    });
}
document.getElementById('shiftSelect').addEventListener('change', function() {
    buildHourDropdown(this.value);
});

// ── Detail rows: dynamic add/remove ─────────────────────────────────────────
var rowIndex = 0;
function makeRow() {
    var idx = rowIndex++;
    var tr = document.createElement('tr');
    tr.dataset.idx = idx;
    tr.innerHTML =
        '<td class="text-muted row-num"></td>' +
        '<td>' +
            '<select name="rows[' + idx + '][machine_type]" class="form-select form-select-sm row-type">' +
                '<option value="">— Type —</option>' +
                machineTypes.map(function(t){return "<option>"+escapeHtml(t)+"</option>";}).join("") +
            '</select>' +
        '</td>' +
        '<td>' +
            '<select name="rows[' + idx + '][machine_id]" class="form-select form-select-sm row-machine">' +
                '<option value="">— Type/Location first —</option>' +
            '</select>' +
        '</td>' +
        '<td>' +
            '<select name="rows[' + idx + '][defect_id]" class="form-select form-select-sm row-defect">' +
                '<option value="">— Type first —</option>' +
            '</select>' +
        '</td>' +
        '<td>' +
            '<input type="number" name="rows[' + idx + '][defect_qty]" class="form-control form-control-sm" min="1" placeholder="0">' +
        '</td>' +
        '<td class="text-end">' +
            '<button type="button" class="btn btn-sm btn-outline-danger row-del" title="Remove row">' +
                '<i class="bi bi-x-lg"></i>' +
            '</button>' +
        '</td>';
    document.getElementById('rowsBody').appendChild(tr);

    // Wire events
    tr.querySelector('.row-type').addEventListener('change', function() {
        filterRowMachine(tr);
        filterRowDefect(tr);
    });
    tr.querySelector('.row-del').addEventListener('click', function() {
        if (document.querySelectorAll('#rowsBody tr').length > 1) {
            tr.remove();
            renumberRows();
        } else {
            // never remove last row; just clear it
            tr.querySelectorAll('select, input').forEach(function(el){ el.value = ''; });
            filterRowMachine(tr);
            filterRowDefect(tr);
        }
    });
    renumberRows();
    filterRowMachine(tr);
    return tr;
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function(c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

function filterRowMachine(tr) {
    var type = tr.querySelector('.row-type').value;
    var locSel = document.querySelector('select[name="location_id"]');
    var loc = locSel ? parseInt(locSel.value, 10) || 0 : 0;
    var sel = tr.querySelector('.row-machine');
    sel.innerHTML = '<option value="">— Select Machine —</option>';
    allMachines.forEach(function(m) {
        if (type && m.machine_type !== type) return;
        if (loc  && m.location_id  !== loc)  return;
        var opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.machine_no + ' — ' + m.machine_name;
        sel.appendChild(opt);
    });
}

function filterRowDefect(tr) {
    var type = tr.querySelector('.row-type').value;
    var sel = tr.querySelector('.row-defect');
    sel.innerHTML = '<option value="">— Select Defect —</option>';
    allDefects.forEach(function(d) {
        if (type && d.machine_type !== type) return;
        var opt = document.createElement('option');
        opt.value = d.id;
        opt.textContent = d.defect_code + ' — ' + d.defect_name;
        sel.appendChild(opt);
    });
}

function renumberRows() {
    var rows = document.querySelectorAll('#rowsBody tr');
    rows.forEach(function(tr, i) {
        tr.querySelector('.row-num').textContent = i + 1;
    });
}

function refreshAllRowsForLocation() {
    document.querySelectorAll('#rowsBody tr').forEach(filterRowMachine);
}

function resetRows() {
    document.getElementById('rowsBody').innerHTML = '';
    rowIndex = 0;
    makeRow();
    document.getElementById('hourSelect').innerHTML = '<option value="">— Select Shift first —</option>';
}

// Wire Add Row button
document.getElementById('addRow').addEventListener('click', makeRow);

// When header location changes, re-filter all rows' machine dropdowns
document.querySelector('select[name="location_id"]').addEventListener('change', refreshAllRowsForLocation);

// Init: one empty row on load
makeRow();

// Restore hour dropdown on load if shift was already selected (POST error bounce)
(function() {
    var shiftSel = document.getElementById('shiftSelect');
    if (shiftSel.value) buildHourDropdown(shiftSel.value);
})();
</script>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
