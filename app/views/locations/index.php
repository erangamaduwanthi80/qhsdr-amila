<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Location List</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Code or Name…"
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active"   <?= ($_GET['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <a href="<?= BASE_URL ?>locations/index" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
                <div class="col-md-2 text-end">
                    <a href="<?= BASE_URL ?>locations/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Location
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Locations</span>
            <small class="text-muted"><?= count($locations) ?> record(s)</small>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Remark</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($locations as $i => $loc): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><code><?= htmlspecialchars($loc['code']) ?></code></td>
                        <td><?= htmlspecialchars($loc['name']) ?></td>
                        <td>
                            <?php if ($loc['status'] === 'active'): ?>
                                <span class="badge-role" style="background-color:#e6f8ee;color:#27ae60;">Active</span>
                            <?php else: ?>
                                <span class="badge-role" style="background-color:#fdecea;color:#c0392b;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($loc['remark'] ?? '—') ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>locations/edit?id=<?= $loc['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="<?= BASE_URL ?>locations/delete" style="display:inline"
                                  onsubmit="return confirm('Delete this location? Machines and records linked to it will lose their location reference.')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= $loc['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($locations)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No locations found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
