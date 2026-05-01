<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $machine ? 'Edit Machine' : 'Add Machine' ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:600px">
        <div class="card-header"><?= $machine ? 'Edit Machine' : 'New Machine' ?></div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Machine Type</label>
                    <select name="machine_type" class="form-select" required>
                        <option value="">— Select Type —</option>
                        <?php foreach (Machine::TYPES as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"
                                <?= (($_POST['machine_type'] ?? $machine['machine_type'] ?? '') === $type) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location <span class="text-muted fw-normal">(optional)</span></label>
                    <select name="location_id" class="form-select">
                        <option value="">— No Location —</option>
                        <?php $curLoc = (int)($_POST['location_id'] ?? $machine['location_id'] ?? 0); ?>
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
                    <label class="form-label fw-semibold">Machine No</label>
                    <input type="text" name="machine_no" class="form-control"
                           value="<?= htmlspecialchars($_POST['machine_no'] ?? $machine['machine_no'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Machine Name</label>
                    <input type="text" name="machine_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['machine_name'] ?? $machine['machine_name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Machine Photo <span class="text-muted fw-normal">(optional)</span></label>
                    <?php if (!empty($machine['machine_photo'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>../uploads/machines/<?= htmlspecialchars($machine['machine_photo']) ?>"
                                 style="height:60px;border-radius:6px;" alt="current photo">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="machine_photo" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Remark</label>
                    <textarea name="remark" class="form-control" rows="2"><?= htmlspecialchars($_POST['remark'] ?? $machine['remark'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= BASE_URL ?>machines/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
