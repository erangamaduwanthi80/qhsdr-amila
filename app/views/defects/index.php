<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Defect List</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Filter bar -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Filter by Machine Type</label>
                    <select name="machine_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Machine Types</option>
                        <?php foreach ($machineTypes as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>"
                                <?= ($filterType === $t) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Filter by Location</label>
                    <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Locations</option>
                        <?php $curLoc = (int)($_GET['location_id'] ?? 0); ?>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= $curLoc === (int)$loc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['code'] . ' — ' . $loc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <?php if ($filterType || $curLoc): ?>
                        <a href="<?= BASE_URL ?>defects/index" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 text-end">
                    <a href="<?= BASE_URL ?>defects/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Defect
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Group defects by machine type visually -->
    <?php
    $grouped = [];
    foreach ($defects as $d) {
        $grouped[$d['machine_type']][] = $d;
    }
    ?>

    <?php if (empty($defects)): ?>
        <div class="card"><div class="card-body text-center text-muted py-4">No defects found.</div></div>
    <?php else: ?>
        <?php foreach ($grouped as $type => $items): ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-gear me-1"></i><?= htmlspecialchars($type) ?></span>
                <small class="text-muted"><?= count($items) ?> defect(s)</small>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:13px">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Defect Code</th>
                            <th>Defect Name</th>
                            <th>Location</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($d['defect_code']) ?></code></td>
                            <td><?= htmlspecialchars($d['defect_name']) ?></td>
                            <td>
                                <?php if (!empty($d['location_code'])): ?>
                                    <span class="badge-role" style="background-color:#fff4e0;color:#d97706;">
                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($d['location_code']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>defects/edit?id=<?= $d['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="<?= BASE_URL ?>defects/delete" style="display:inline"
                                      onsubmit="return confirm('Delete this defect?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
