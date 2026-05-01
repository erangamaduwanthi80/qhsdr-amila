<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php if (isset($_SESSION['user'])): ?>
    <?php $activePage = ''; ?>
    <?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>
<?php endif; ?>

<div class="<?= isset($_SESSION['user']) ? 'main-content' : 'd-flex align-items-center justify-content-center' ?>"
     <?= isset($_SESSION['user']) ? '' : 'style="min-height:100vh"' ?>>
    <div class="card text-center" style="max-width:520px;margin:60px auto;">
        <div class="card-body py-5">
            <div style="font-size:64px;color:#e67e22">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <h2 style="color:#1e2a38;font-weight:700;margin-top:8px">Access Denied</h2>
            <p class="text-muted mb-4" style="font-size:14px">
                You do not have permission to <strong><?= htmlspecialchars($deniedAction ?: 'access') ?></strong>
                <?php if ($deniedModule): ?>
                    on the <strong><?= htmlspecialchars(ucfirst($deniedModule)) ?></strong> module.
                <?php else: ?>
                    this resource.
                <?php endif; ?>
            </p>
            <p class="text-muted" style="font-size:13px">
                Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?></strong>
                <?php if (!empty($_SESSION['user']['role'])): ?>
                    (Role: <span class="badge-role"><?= htmlspecialchars($_SESSION['user']['role']) ?></span>)
                <?php endif; ?>
            </p>
            <div class="mt-4 d-flex gap-2 justify-content-center">
                <a href="<?= BASE_URL ?>dashboard/index" class="btn btn-primary btn-sm">
                    <i class="bi bi-house"></i> Go to Dashboard
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
