<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Roles & Permissions</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="<?= BASE_URL ?>roles/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Role
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Roles</span>
            <small class="text-muted"><?= count($roles) ?> role(s)</small>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Created At</th>
                        <th class="text-center">Permissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($roles as $i => $role): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <span class="badge-role">
                                <i class="bi bi-shield-lock me-1"></i><?= htmlspecialchars($role['name']) ?>
                            </span>
                        </td>
                        <td style="font-size:13px;color:#8a9bb0"><?= htmlspecialchars($role['created_at']) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>roles/permissions?id=<?= $role['id'] ?>"
                               class="btn btn-sm btn-outline-secondary" style="font-size:12px">
                                <i class="bi bi-sliders"></i> Manage
                            </a>
                        </td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>roles/edit?id=<?= $role['id'] ?>"
                               class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="<?= BASE_URL ?>roles/delete" style="display:inline"
                                  onsubmit="return confirm('Delete this role?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= $role['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($roles)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No roles found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
