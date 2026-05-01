<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Audit Log</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success py-2"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['user_id'] ?>"
                                <?= (int)($_GET['user_id'] ?? 0) === (int)$u['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Action</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        <?php foreach (['create','update','delete','login','logout','purge'] as $a): ?>
                            <option value="<?= $a ?>" <?= ($_GET['action'] ?? '') === $a ? 'selected' : '' ?>>
                                <?= ucfirst($a) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Entity</label>
                    <select name="entity" class="form-select form-select-sm">
                        <option value="">All Entities</option>
                        <?php foreach ($entities as $e): ?>
                            <option value="<?= htmlspecialchars($e) ?>" <?= ($_GET['entity'] ?? '') === $e ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="<?= BASE_URL ?>audit/index" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted" style="font-size:13px">
            <?php if ($paginator->total > 0): ?>
                Showing <?= $paginator->rangeStart() ?>–<?= $paginator->rangeEnd() ?> of <?= $paginator->total ?> entries
            <?php else: ?>
                No entries
            <?php endif; ?>
        </span>
        <?php if (Auth::can('audit', 'delete')): ?>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#purgePanel">
                <i class="bi bi-trash"></i> Purge Old Entries
            </button>
        <?php endif; ?>
    </div>

    <?php if (Auth::can('audit', 'delete')): ?>
    <div class="collapse mb-3" id="purgePanel">
        <div class="card border-danger">
            <div class="card-body py-3">
                <form method="POST" action="<?= BASE_URL ?>audit/purge" class="d-flex gap-2 align-items-end"
                      onsubmit="return confirm('Permanently delete audit entries older than the specified days? This cannot be undone.');">
                    <?= Csrf::field() ?>
                    <div>
                        <label class="form-label fw-semibold mb-1" style="font-size:13px">Keep entries from last N days</label>
                        <input type="number" name="days" class="form-control form-control-sm" min="1" value="90" required style="width:120px">
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm">Purge</button>
                    <small class="text-muted ms-2">Deletes everything older. Default: 90 days.</small>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0" style="font-size:13px">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="width:160px">When</th>
                        <th>User</th>
                        <th style="width:90px">Action</th>
                        <th>Entity</th>
                        <th style="width:60px">ID</th>
                        <th>Snapshot</th>
                        <th style="width:120px">IP</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $i => $e): ?>
                    <?php
                    $actionColors = [
                        'create' => '#27ae60', 'update' => '#4ea8de', 'delete' => '#c0392b',
                        'login'  => '#9b59b6', 'logout' => '#7f8c8d', 'purge'  => '#e67e22',
                    ];
                    $color = $actionColors[$e['action']] ?? '#1e2a38';
                    $snapshot = $e['snapshot'] ? json_decode($e['snapshot'], true) : null;
                    ?>
                    <tr>
                        <td class="text-muted"><?= $e['id'] ?></td>
                        <td style="font-size:12px;color:#5a6a78"><?= htmlspecialchars($e['created_at']) ?></td>
                        <td>
                            <i class="bi bi-person-circle me-1" style="color:#4ea8de"></i>
                            <?= htmlspecialchars($e['username'] ?? '—') ?>
                        </td>
                        <td>
                            <span class="badge-role" style="background-color:<?= $color ?>15;color:<?= $color ?>;">
                                <?= htmlspecialchars(ucfirst($e['action'])) ?>
                            </span>
                        </td>
                        <td><code><?= htmlspecialchars($e['entity']) ?></code></td>
                        <td class="text-muted"><?= htmlspecialchars($e['entity_id'] ?? '—') ?></td>
                        <td>
                            <?php if ($snapshot): ?>
                                <code style="font-size:11px;background:#f0f4f8;padding:2px 6px;border-radius:3px;display:inline-block;max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                                      title="<?= htmlspecialchars(json_encode($snapshot, JSON_PRETTY_PRINT)) ?>">
                                    <?= htmlspecialchars(json_encode($snapshot, JSON_UNESCAPED_UNICODE)) ?>
                                </code>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:11px;color:#8a9bb0"><?= htmlspecialchars($e['ip_address'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($entries)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No audit entries found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($paginator->totalPages > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center" style="background:#f8f9fa">
            <small class="text-muted">Page <?= $paginator->page ?> of <?= $paginator->totalPages ?></small>
            <?php $pagQs = array_intersect_key($_GET, array_flip(['url','user_id','action','entity','date_from','date_to'])); ?>
            <?= $paginator->render($pagQs) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
