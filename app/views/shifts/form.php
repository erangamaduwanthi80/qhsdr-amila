<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $shift ? 'Edit Shift' : 'Add Shift' ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:500px">
        <div class="card-header"><?= $shift ? 'Edit Shift' : 'New Shift' ?></div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <p class="text-muted" style="font-size:13px">
                Each shift belongs to a location. After saving, you will be taken to define the exact time for each hour slot.
            </p>
            <form method="POST">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location</label>
                    <select name="location_id" class="form-select" required>
                        <option value="">— Select Location —</option>
                        <?php $curLoc = (int)($_POST['location_id'] ?? $shift['location_id'] ?? 0); ?>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= $curLoc === (int)$loc['id'] ? 'selected' : '' ?>>
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
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Shift Name</label>
                    <input type="text" name="shift_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['shift_name'] ?? $shift['shift_name'] ?? '') ?>"
                           placeholder="e.g. Day, Night, Morning" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Total Hours per Shift</label>
                    <select name="hours" class="form-select" required>
                        <option value="">— Select —</option>
                        <?php foreach (Shift::HOUR_OPTIONS as $h): ?>
                            <option value="<?= $h ?>"
                                <?= (int)($_POST['hours'] ?? $shift['hours'] ?? 0) === $h ? 'selected' : '' ?>>
                                <?= $h ?> hours
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Choose between 8 and 12 hours.</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Save & Define Hours <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <a href="<?= BASE_URL ?>shifts/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
