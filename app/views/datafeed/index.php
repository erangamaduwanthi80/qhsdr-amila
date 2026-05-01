<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Data Entry</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:680px">
        <div class="card-header">New Defect Record</div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success py-2">
                    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>


            <form method="POST" id="entryForm">
                <?= Csrf::field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="feed_date" class="form-control"
                               value="<?= htmlspecialchars($_POST['feed_date'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Shift</label>
                        <select name="shift_id" id="shiftSelect" class="form-select" required>
                            <option value="">— Select Shift —</option>
                            <?php foreach ($shifts as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                        data-hours="<?= $s['hours'] ?>"
                                    <?= (int)($_POST['shift_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['shift_name']) ?> (<?= $s['hours'] ?> hrs)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hour</label>
                        <select name="hour_no" id="hourSelect" class="form-select" required>
                            <option value="">— Select Shift first —</option>
                        </select>
                        <div class="form-text" id="hourHint" style="color:#4ea8de"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location</label>
                        <?php $userLocId = (int)($_SESSION['user']['location_id'] ?? 0); ?>
                        <?php if ($userLocId && !empty($locations)): ?>
                            <?php $myLoc = $locations[0]; ?>
                            <input type="hidden" name="location_id" id="locationSelect" value="<?= (int)$myLoc['id'] ?>">
                            <input type="text" class="form-control" disabled
                                   value="<?= htmlspecialchars($myLoc['code'] . ' — ' . $myLoc['name']) ?>">
                            <div class="form-text" style="color:#4ea8de;font-size:12px">
                                <i class="bi bi-lock me-1"></i>Auto-set from your account.
                            </div>
                        <?php else: ?>
                            <select name="location_id" id="locationSelect" class="form-select" required>
                                <option value="">— Select Location —</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"
                                        <?= (int)($_POST['location_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc['code'] . ' — ' . $loc['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($locations)): ?>
                                <div class="form-text" style="color:#e67e22">
                                    No active locations defined.
                                    <a href="<?= BASE_URL ?>locations/create">Add one →</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Machine Type</label>
                        <select name="machine_type" id="machineType" class="form-select" required>
                            <option value="">— Select Type —</option>
                            <?php foreach ($machineTypes as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>"
                                    <?= (($_POST['machine_type'] ?? '') === $t) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Machine No</label>
                        <select name="machine_id" id="machineSelect" class="form-select" required>
                            <option value="">— Select Type / Location first —</option>
                            <?php foreach ($machines as $m): ?>
                                <option value="<?= $m['id'] ?>"
                                        data-type="<?= htmlspecialchars($m['machine_type']) ?>"
                                        data-location="<?= (int)($m['location_id'] ?? 0) ?>"
                                    <?= (int)($_POST['machine_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['machine_no'] . ' — ' . $m['machine_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Defect Type</label>
                        <select name="defect_id" id="defectSelect" class="form-select" required>
                            <option value="">— Select Machine Type first —</option>
                            <?php foreach ($defects as $d): ?>
                                <option value="<?= $d['id'] ?>"
                                        data-type="<?= htmlspecialchars($d['machine_type']) ?>"
                                    <?= (int)($_POST['defect_id'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['defect_code'] . ' — ' . $d['defect_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Defect Quantity</label>
                        <input type="number" name="defect_qty" class="form-control" min="1"
                               value="<?= htmlspecialchars($_POST['defect_qty'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Record</button>
                    <a href="<?= BASE_URL ?>datafeed/list" class="btn btn-outline-secondary">View Data List</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// All shift hour slots embedded from PHP — no AJAX needed
var shiftSlots   = <?= json_encode($shiftHourLabels) ?>;
var savedHourNo  = <?= (int)($_POST['hour_no'] ?? 0) ?>;

var allMachineOpts = Array.from(document.querySelectorAll('#machineSelect option'));
var allDefectOpts  = Array.from(document.querySelectorAll('#defectSelect option'));

// ── Shift → populate Hour dropdown ──────────────────────────────────────────
function buildHourDropdown(shiftId) {
    var sel  = document.getElementById('hourSelect');
    var hint = document.getElementById('hourHint');
    sel.innerHTML = '';
    hint.textContent = '';

    if (!shiftId) {
        sel.innerHTML = '<option value="">— Select Shift first —</option>';
        return;
    }

    var slots = shiftSlots[shiftId] || {};   // { "1": "06:00 – 06:59", "2": ... }
    var keys  = Object.keys(slots);

    if (!keys.length) {
        sel.innerHTML = '<option value="">— No hours defined —</option>';
        hint.style.color = '#e67e22';
        hint.textContent = 'Hour slots not set. Go to Shift Breakdown → Define Hours.';
        return;
    }

    var ph = document.createElement('option');
    ph.value = '';
    ph.textContent = '— Select Hour —';
    sel.appendChild(ph);

    keys.forEach(function(hourNo) {
        var opt = document.createElement('option');
        opt.value = hourNo;
        opt.textContent = slots[hourNo];          // shows "06:00 – 06:59"
        if (savedHourNo && savedHourNo == hourNo) opt.selected = true;
        sel.appendChild(opt);
    });
}

document.getElementById('shiftSelect').addEventListener('change', function() {
    buildHourDropdown(this.value);
});

// ── Machine Type + Location → filter Machines ───────────────────────────────
function filterMachines() {
    var type = document.getElementById('machineType').value;
    var loc  = document.getElementById('locationSelect').value;
    var sel  = document.getElementById('machineSelect');
    sel.innerHTML = '';
    var ph = document.createElement('option');
    ph.value = '';
    ph.textContent = (!type && !loc) ? '— Select Type / Location first —' : '— Select Machine —';
    sel.appendChild(ph);
    allMachineOpts
        .filter(function(o) {
            if (!o.dataset.type) return false; // skip placeholder
            if (type && o.dataset.type     !== type) return false;
            if (loc  && o.dataset.location !== loc)  return false;
            return true;
        })
        .forEach(function(o) { sel.appendChild(o.cloneNode(true)); });
}

// ── Machine Type → filter Defects (defects are typed only) ──────────────────
function filterDefects(type) {
    var sel = document.getElementById('defectSelect');
    sel.innerHTML = '';
    var ph = document.createElement('option');
    ph.value = ''; ph.textContent = '— Select —';
    sel.appendChild(ph);
    allDefectOpts
        .filter(function(o) { return !o.dataset.type || o.dataset.type === type; })
        .forEach(function(o) { sel.appendChild(o.cloneNode(true)); });
}

document.getElementById('machineType').addEventListener('change', function() {
    filterMachines();
    filterDefects(this.value);
});

document.getElementById('locationSelect').addEventListener('change', function() {
    filterMachines();
});

// ── Restore on page load (POST error bounce) ─────────────────────────────────
(function() {
    var shiftSel = document.getElementById('shiftSelect');
    if (shiftSel.value) buildHourDropdown(shiftSel.value);

    var typeSel = document.getElementById('machineType');
    if (typeSel.value) typeSel.dispatchEvent(new Event('change'));
})();
</script>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
