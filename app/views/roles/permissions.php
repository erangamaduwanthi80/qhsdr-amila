<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Permissions — <?= htmlspecialchars($role['name']) ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card">
        <div class="card-header">Assign Permissions</div>
        <div class="card-body">
            <form method="POST">
                <?= Csrf::field() ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th class="text-center">Add</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center">Approve</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($modules as $mod): ?>
                        <?php $p = $permissions[$mod] ?? []; ?>
                        <tr>
                            <td class="fw-semibold text-capitalize"><?= htmlspecialchars($mod) ?></td>
                            <?php foreach (['add','view','edit','delete','approve'] as $action): ?>
                                <td class="text-center">
                                    <input type="checkbox" name="actions[]"
                                           value="<?= $mod . '_' . $action ?>"
                                           <?= !empty($p['can_' . $action]) ? 'checked' : '' ?>>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                    <a href="<?= BASE_URL ?>roles/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
