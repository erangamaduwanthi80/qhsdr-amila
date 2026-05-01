<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $location ? 'Edit Location' : 'Add Location' ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:600px">
        <div class="card-header"><?= $location ? 'Edit Location' : 'New Location' ?></div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location Code</label>
                    <input type="text" name="code" class="form-control"
                           value="<?= htmlspecialchars($_POST['code'] ?? $location['code'] ?? '') ?>"
                           placeholder="e.g. LOC-A1, FLR-2, LINE-3" required>
                    <div class="form-text">Short unique code used in dropdowns and reports.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($_POST['name'] ?? $location['name'] ?? '') ?>"
                           placeholder="e.g. Floor 2 — Pressing Area" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <?php $cur = $_POST['status'] ?? $location['status'] ?? 'active'; ?>
                        <option value="active"   <?= $cur === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $cur === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Remark</label>
                    <textarea name="remark" class="form-control" rows="2"><?= htmlspecialchars($_POST['remark'] ?? $location['remark'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= BASE_URL ?>locations/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
