<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<?php
    $activeType = $type;
    $tabIcons = [
        'Pressing Machine'  => 'bi-hammer',
        'Wrapping Machine'  => 'bi-box-seam',
        'Plug Hole Machine' => 'bi-circle',
    ];
    $buildTabUrl = function($t) {
        $qs = $_GET;
        $qs['type'] = $t;
        unset($qs['url']);
        return BASE_URL . 'machines/index?' . http_build_query($qs);
    };
?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Machine List</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Machine Type Tabs -->
    <ul class="nav nav-tabs mb-3">
        <?php foreach (Machine::TYPES as $t): ?>
            <li class="nav-item">
                <a class="nav-link <?= $activeType === $t ? 'active' : '' ?>"
                   href="<?= htmlspecialchars($buildTabUrl($t)) ?>">
                    <i class="bi <?= $tabIcons[$t] ?? 'bi-gear' ?> me-1"></i>
                    <?= htmlspecialchars($t) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Search & Filter (within active tab) -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="type" value="<?= htmlspecialchars($activeType) ?>">
                <div class="col-md-4">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Machine No or Name…"
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Location</label>
                    <select name="location_id" class="form-select form-select-sm">
                        <option value="">All Locations</option>
                        <?php $curLoc = (int)($_GET['location_id'] ?? 0); ?>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= $curLoc === (int)$loc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    <a href="<?= BASE_URL ?>machines/index?type=<?= urlencode($activeType) ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
                <div class="col-md-3 text-end">
                    <a href="<?= BASE_URL ?>machines/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Machine
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><?= htmlspecialchars($activeType) ?>s</span>
            <small class="text-muted"><?= count($machines) ?> record(s)</small>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <?php $extraQs = array_intersect_key($_GET, array_flip(['url','search','type','location_id'])); ?>
                    <tr>
                        <th>#</th>
                        <th><?= Sort::header('Location',     'location',     $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Machine No',   'machine_no',   $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Machine Name', 'machine_name', $sort, $dir, $extraQs) ?></th>
                        <th>Photo</th>
                        <th>Remark</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($machines as $i => $m): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if (!empty($m['location_code'])): ?>
                                <span class="badge-role" style="background-color:#fff4e0;color:#d97706;">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($m['location_code']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m['machine_no']) ?></td>
                        <td><?= htmlspecialchars($m['machine_name']) ?></td>
                        <td>
                            <?php if ($m['machine_photo']): ?>
                                <img src="<?= BASE_URL ?>../uploads/machines/<?= htmlspecialchars($m['machine_photo']) ?>"
                                     style="height:36px;border-radius:4px;" alt="photo">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m['remark'] ?? '—') ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>machines/edit?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="<?= BASE_URL ?>machines/delete" style="display:inline"
                                  onsubmit="return confirm('Delete this machine?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($machines)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No machines found in this category.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
