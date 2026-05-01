<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">User Accounts</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Username or full name…"
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:13px">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= htmlspecialchars($r['name']) ?>"
                                <?= ($_GET['role'] ?? '') === $r['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
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
                    <a href="<?= BASE_URL ?>users/index" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
                <div class="col-md-3 text-end">
                    <a href="<?= BASE_URL ?>users/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add User
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Users</span>
            <small class="text-muted"><?= count($users) ?> account(s)</small>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <?php $extraQs = array_intersect_key($_GET, array_flip(['url','search','role','location_id'])); ?>
                    <tr>
                        <th>#</th>
                        <th><?= Sort::header('Username',  'username',   $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Full Name', 'name',       $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Role',      'role',       $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Location',  'location',   $sort, $dir, $extraQs) ?></th>
                        <th><?= Sort::header('Created',   'created_at', $sort, $dir, $extraQs) ?></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $i => $u): ?>
                    <?php $isSelf = (int)$_SESSION['user']['id'] === (int)$u['id']; ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <i class="bi bi-person-circle me-1" style="color:#4ea8de"></i>
                            <strong><?= htmlspecialchars($u['username']) ?></strong>
                            <?php if ($isSelf): ?>
                                <span class="badge-role ms-1" style="background-color:#e6f8ee;color:#27ae60;font-size:10px">YOU</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td>
                            <span class="badge-role">
                                <i class="bi bi-shield-lock me-1"></i><?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($u['location_code'])): ?>
                                <span class="badge-role" style="background-color:#fff4e0;color:#d97706;">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($u['location_code']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:#8a9bb0"><?= htmlspecialchars($u['created_at']) ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>users/edit?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php if (!$isSelf): ?>
                                <form method="POST" action="<?= BASE_URL ?>users/delete" style="display:inline"
                                      onsubmit="return confirm('Delete user <?= htmlspecialchars($u['username']) ?>? This cannot be undone.')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-danger" disabled title="You cannot delete your own account">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
