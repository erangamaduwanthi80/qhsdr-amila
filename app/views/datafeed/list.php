<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Data List</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Shift</label>
                    <select name="shift_id" class="form-select form-select-sm">
                        <option value="">All Shifts</option>
                        <?php foreach ($shifts as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (int)($_GET['shift_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['shift_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Location</label>
                    <select name="location_id" class="form-select form-select-sm">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= (int)($_GET['location_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Machine</label>
                    <select name="machine_id" class="form-select form-select-sm">
                        <option value="">All Machines</option>
                        <?php foreach ($machines as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= (int)($_GET['machine_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['machine_no']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Defect</label>
                    <select name="defect_id" class="form-select form-select-sm">
                        <option value="">All Defects</option>
                        <?php foreach ($defects as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= (int)($_GET['defect_id'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['defect_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="<?= BASE_URL ?>datafeed/list" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted" style="font-size:13px">
            <?php if ($paginator->total > 0): ?>
                Showing <?= $paginator->rangeStart() ?>–<?= $paginator->rangeEnd() ?> of <?= $paginator->total ?> record(s)
            <?php else: ?>
                No records found
            <?php endif; ?>
        </span>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>datafeed/index" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> New Entry
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>"
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0" style="font-size:13px">
                <thead>
                    <?php $extraQs = array_intersect_key($_GET, array_flip(['url','date_from','date_to','shift_id','location_id','machine_id','defect_id'])); ?>
                    <tr>
                        <th>#</th>
                        <th><?= Sort::header('Date',         'feed_date',    $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Shift',        'shift',        $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Hour',         'hour_no',      $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Location',     'location',     $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Machine Type', 'machine_type', $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Machine No',   'machine_no',   $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Defect Code',  'defect_code',  $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Defect Name',  'defect_name',  $sort, $dir, $extraQs) ?></th>
                        <th class="text-center"><?= Sort::header('Qty', 'qty', $sort, $dir, $extraQs) ?></th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($r['feed_date']) ?></td>
                        <td><?= htmlspecialchars($r['shift_name']) ?></td>
                        <td><?= htmlspecialchars($r['hour_no']) ?></td>
                        <td>
                            <?php if (!empty($r['location_code'])): ?>
                                <span class="badge-role" style="background-color:#fff4e0;color:#d97706;">
                                    <?= htmlspecialchars($r['location_code']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['machine_type']) ?></td>
                        <td><?= htmlspecialchars($r['machine_no']) ?></td>
                        <td><?= htmlspecialchars($r['defect_code']) ?></td>
                        <td><?= htmlspecialchars($r['defect_name']) ?></td>
                        <td class="text-center fw-semibold"><?= htmlspecialchars($r['defect_qty']) ?></td>
                        <td class="text-end">
                            <form method="POST" action="<?= BASE_URL ?>datafeed/delete" style="display:inline"
                                  onsubmit="return confirm('Delete this record?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($records)): ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">No records found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($paginator->totalPages > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center" style="background:#f8f9fa">
            <small class="text-muted">Page <?= $paginator->page ?> of <?= $paginator->totalPages ?></small>
            <?php $pagQs = array_intersect_key($_GET, array_flip(['url','date_from','date_to','shift_id','location_id','machine_id','defect_id','sort','dir'])); ?>
            <?= $paginator->render($pagQs) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
