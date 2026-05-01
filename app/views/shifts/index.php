<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Shift Breakdown</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Filter bar -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
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
                <div class="col-md-4 d-flex gap-2 align-items-end">
                    <?php if ($curLoc): ?>
                        <a href="<?= BASE_URL ?>shifts/index" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= BASE_URL ?>shifts/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Shift
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Group shifts by location -->
    <?php
    $grouped = [];
    foreach ($shifts as $s) {
        $key = $s['location_code'] ? ($s['location_code'] . ' — ' . $s['location_name']) : '__none__';
        $grouped[$key][] = $s;
    }
    ?>

    <?php if (empty($shifts)): ?>
        <div class="card"><div class="card-body text-center text-muted py-4">No shifts defined yet.</div></div>
    <?php else: ?>
        <?php foreach ($grouped as $locLabel => $items): ?>
            <h5 class="mt-3 mb-2" style="font-size:15px;color:#4ea8de">
                <i class="bi bi-geo-alt me-1"></i>
                <?php if ($locLabel === '__none__'): ?>
                    <span style="color:#e67e22">Unassigned (no location)</span>
                <?php else: ?>
                    <?= htmlspecialchars($locLabel) ?>
                <?php endif; ?>
            </h5>
            <?php foreach ($items as $s): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-clock me-1"></i>
                        <strong><?= htmlspecialchars($s['shift_name']) ?> Shift</strong>
                        <span class="badge-role ms-2"><?= $s['hours'] ?> hrs</span>
                        <?php if (!empty($s['location_code'])): ?>
                            <span class="badge-role ms-1" style="background-color:#fff4e0;color:#d97706;">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($s['location_code']) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge-role ms-1" style="background-color:#fde8e8;color:#b00020;">
                                no location
                            </span>
                        <?php endif; ?>
                    </span>
                    <div class="d-flex gap-2">
                        <a href="<?= BASE_URL ?>shifts/hours?id=<?= $s['id'] ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-clock-history"></i> Define Hours
                        </a>
                        <a href="<?= BASE_URL ?>shifts/edit?id=<?= $s['id'] ?>"
                           class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="<?= BASE_URL ?>shifts/delete" style="display:inline"
                              onsubmit="return confirm('Delete this shift?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($s['slots'])): ?>
                <div class="card-body p-0">
                    <table class="table mb-0" style="font-size:13px">
                        <thead>
                            <tr>
                                <th style="width:80px">Hour</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($s['slots'] as $slot): ?>
                            <tr>
                                <td><span class="badge-role">Hr <?= $slot['hour_no'] ?></span></td>
                                <td><?= htmlspecialchars($slot['start_time']) ?></td>
                                <td><?= htmlspecialchars($slot['end_time']) ?></td>
                                <td style="color:#8a9bb0">1 hour</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="card-body py-2" style="font-size:13px;color:#e67e22;">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Hour slots not defined yet.
                    <a href="<?= BASE_URL ?>shifts/hours?id=<?= $s['id'] ?>">Define now →</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
