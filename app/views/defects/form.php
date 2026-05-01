<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $defect ? 'Edit Defect' : 'Add Defect' ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:500px">
        <div class="card-header"><?= $defect ? 'Edit Defect' : 'New Defect' ?></div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Machine Type</label>
                    <select name="machine_type" class="form-select" required>
                        <option value="">— Select Type —</option>
                        <?php foreach ($machineTypes as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>"
                                <?= (($_POST['machine_type'] ?? $defect['machine_type'] ?? '') === $t) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location <span class="text-muted fw-normal">(optional)</span></label>
                    <select name="location_id" class="form-select">
                        <option value="">— No Location —</option>
                        <?php $curLoc = (int)($_POST['location_id'] ?? $defect['location_id'] ?? 0); ?>
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
                    <label class="form-label fw-semibold">Defect Code</label>
                    <input type="text" name="defect_code" class="form-control"
                           value="<?= htmlspecialchars($_POST['defect_code'] ?? $defect['defect_code'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Defect Name</label>
                    <input type="text" name="defect_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['defect_name'] ?? $defect['defect_name'] ?? '') ?>" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= BASE_URL ?>defects/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
