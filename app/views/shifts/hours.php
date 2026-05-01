<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Define Hour Slots — <?= htmlspecialchars($shift['shift_name']) ?> Shift</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:640px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-clock-history me-1"></i>
                <?= htmlspecialchars($shift['shift_name']) ?> Shift —
                <span class="badge-role"><?= $shift['hours'] ?> hours</span>
            </span>
            <a href="<?= BASE_URL ?>shifts/index" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success py-2">
                    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <p class="text-muted mb-3" style="font-size:13px">
                Define the start and end time for each of the <?= $shift['hours'] ?> hours in this shift.
                Use 24-hour format (e.g. <code>06:00</code>, <code>14:30</code>).
            </p>

            <form method="POST">
                <?= Csrf::field() ?>
                <table class="table" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="width:80px">Hour</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th style="width:120px;color:#8a9bb0">Preview</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Build existing slot map: hour_no => slot
                    $slotMap = [];
                    foreach ($slots as $sl) { $slotMap[$sl['hour_no']] = $sl; }
                    ?>
                    <?php for ($i = 1; $i <= $shift['hours']; $i++): ?>
                        <?php $existing = $slotMap[$i] ?? null; ?>
                        <tr>
                            <td><span class="badge-role">Hr <?= $i ?></span></td>
                            <td>
                                <input type="time" name="slots[<?= $i - 1 ?>][start_time]"
                                       class="form-control form-control-sm slot-start"
                                       data-index="<?= $i - 1 ?>"
                                       value="<?= htmlspecialchars($existing['start_time'] ?? '') ?>"
                                       required>
                            </td>
                            <td>
                                <input type="time" name="slots[<?= $i - 1 ?>][end_time]"
                                       class="form-control form-control-sm slot-end"
                                       data-index="<?= $i - 1 ?>"
                                       value="<?= htmlspecialchars($existing['end_time'] ?? '') ?>"
                                       required>
                            </td>
                            <td>
                                <span id="preview-<?= $i - 1 ?>" style="font-size:12px;color:#4ea8de">
                                    <?php if ($existing): ?>
                                        <?= $existing['start_time'] ?> – <?= $existing['end_time'] ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Save Hour Slots
                    </button>
                    <a href="<?= BASE_URL ?>shifts/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Convert "HH:MM" to total minutes
function toMins(t) {
    var p = t.split(':');
    return parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
}

// Convert total minutes to "HH:MM"
function toTime(mins) {
    mins = ((mins % 1440) + 1440) % 1440; // wrap 24h
    return String(Math.floor(mins / 60)).padStart(2, '0') + ':' + String(mins % 60).padStart(2, '0');
}

function updatePreview(idx) {
    var s = document.querySelector('.slot-start[data-index="' + idx + '"]').value;
    var e = document.querySelector('.slot-end[data-index="' + idx + '"]').value;
    var el = document.getElementById('preview-' + idx);
    if (el) el.textContent = s && e ? s + ' – ' + e : '';
}

// Cascade: from row idx onward, auto-fill end = start+59min, next start = end+1min
function cascadeFrom(idx) {
    var rows = document.querySelectorAll('.slot-start');
    var total = rows.length;

    for (var i = idx; i < total; i++) {
        var startInput = document.querySelector('.slot-start[data-index="' + i + '"]');
        var endInput   = document.querySelector('.slot-end[data-index="' + i + '"]');

        if (!startInput.value) break; // stop if no start time

        // End = start + 59 minutes
        endInput.value = toTime(toMins(startInput.value) + 59);
        updatePreview(i);

        // Fill next row's start = this end + 1 minute
        var nextStart = document.querySelector('.slot-start[data-index="' + (i + 1) + '"]');
        if (nextStart) {
            nextStart.value = toTime(toMins(endInput.value) + 1);
        }
    }
    // Update preview for last row if it got a start
    if (idx < total) updatePreview(total - 1);
}

// When any start time is changed manually → cascade from that row
document.querySelectorAll('.slot-start').forEach(function(input) {
    input.addEventListener('change', function() {
        cascadeFrom(parseInt(this.dataset.index, 10));
    });
});

// Allow manual end time edit → just update preview, don't cascade
document.querySelectorAll('.slot-end').forEach(function(input) {
    input.addEventListener('change', function() {
        updatePreview(this.dataset.index);
    });
});

// On load: if first slot already has a start time (edit mode), cascade all
(function() {
    var first = document.querySelector('.slot-start[data-index="0"]');
    if (first && first.value) cascadeFrom(0);
})();
</script>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
