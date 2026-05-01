<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title"><?= $user ? 'Edit User' : 'Add User' ?></h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <div class="card" style="max-width:640px">
        <div class="card-header"><?= $user ? 'Edit User Account' : 'New User Account' ?></div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <?= Csrf::field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($_POST['username'] ?? $user['username'] ?? '') ?>"
                               placeholder="e.g. jsmith" required>
                        <div class="form-text">Letters, digits, dot, dash, underscore. 3–50 chars.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($_POST['name'] ?? $user['name'] ?? '') ?>"
                               placeholder="e.g. John Smith" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">— Select Role —</option>
                            <?php $cur = $_POST['role'] ?? $user['role'] ?? ''; ?>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= htmlspecialchars($r['name']) ?>"
                                    <?= $cur === $r['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($roles)): ?>
                            <div class="form-text" style="color:#e67e22">
                                No roles defined.
                                <a href="<?= BASE_URL ?>roles/create">Add one →</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location <span class="text-muted fw-normal">(optional)</span></label>
                        <select name="location_id" class="form-select">
                            <option value="">— No Location —</option>
                            <?php $curLoc = (int)($_POST['location_id'] ?? $user['location_id'] ?? 0); ?>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>" <?= $curLoc === (int)$loc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc['code'] . ' — ' . $loc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($locations)): ?>
                            <div class="form-text" style="color:#e67e22">
                                No active locations defined.
                                <a href="<?= BASE_URL ?>locations/create">Add one →</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr class="mt-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Password
                            <?php if ($user): ?>
                                <span class="text-muted fw-normal">(leave blank to keep current)</span>
                            <?php endif; ?>
                        </label>
                        <input type="password" name="password" class="form-control"
                               autocomplete="new-password"
                               placeholder="<?= $user ? 'Leave blank to keep current' : 'Min. 6 characters' ?>"
                               <?= $user ? '' : 'required' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-control"
                               autocomplete="new-password"
                               placeholder="Re-enter password"
                               <?= $user ? '' : 'required' ?>>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="<?= BASE_URL ?>users/index" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
