<div class="sidebar">
    <a href="<?= BASE_URL ?>" class="brand">QHS <span>DR</span> Dashboard</a>

    <?php if (Auth::can('dashboard', 'view')): ?>
    <div class="nav-section">Main</div>
    <a href="<?= BASE_URL ?>dashboard/index" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <?php endif; ?>

    <?php
    $cfgLinks = Auth::can('users', 'view')
              || Auth::can('roles', 'view')
              || Auth::can('locations', 'view')
              || Auth::can('machines', 'view')
              || Auth::can('shifts', 'view')
              || Auth::can('defects', 'view');
    ?>
    <?php if ($cfgLinks): ?>
    <div class="nav-section">Configuration</div>
    <?php endif; ?>

    <?php if (Auth::can('users', 'view')): ?>
    <a href="<?= BASE_URL ?>users/index" class="nav-link <?= $activePage === 'users' ? 'active' : '' ?>">
        <i class="bi bi-people"></i> User Accounts
    </a>
    <?php endif; ?>
    <?php if (Auth::can('roles', 'view')): ?>
    <a href="<?= BASE_URL ?>roles/index" class="nav-link <?= $activePage === 'roles' ? 'active' : '' ?>">
        <i class="bi bi-shield-lock"></i> Roles & Permissions
    </a>
    <?php endif; ?>
    <?php if (Auth::can('audit', 'view')): ?>
    <a href="<?= BASE_URL ?>audit/index" class="nav-link <?= $activePage === 'audit' ? 'active' : '' ?>">
        <i class="bi bi-journal-text"></i> Audit Log
    </a>
    <?php endif; ?>
    <?php if (Auth::can('locations', 'view')): ?>
    <a href="<?= BASE_URL ?>locations/index" class="nav-link <?= $activePage === 'locations' ? 'active' : '' ?>">
        <i class="bi bi-geo-alt"></i> Location List
    </a>
    <?php endif; ?>
    <?php if (Auth::can('machines', 'view')): ?>
    <a href="<?= BASE_URL ?>machines/index" class="nav-link <?= $activePage === 'machines' ? 'active' : '' ?>">
        <i class="bi bi-gear"></i> Machine List
    </a>
    <?php endif; ?>
    <?php if (Auth::can('shifts', 'view')): ?>
    <a href="<?= BASE_URL ?>shifts/index" class="nav-link <?= $activePage === 'shifts' ? 'active' : '' ?>">
        <i class="bi bi-clock"></i> Shift Breakdown
    </a>
    <?php endif; ?>
    <?php if (Auth::can('defects', 'view')): ?>
    <a href="<?= BASE_URL ?>defects/index" class="nav-link <?= $activePage === 'defects' ? 'active' : '' ?>">
        <i class="bi bi-exclamation-triangle"></i> Defect List
    </a>
    <?php endif; ?>

    <?php if (Auth::can('datafeed', 'add') || Auth::can('datafeed', 'view')): ?>
    <div class="nav-section">Operations</div>
    <?php endif; ?>
    <?php if (Auth::can('datafeed', 'add')): ?>
    <a href="<?= BASE_URL ?>datafeed/index" class="nav-link <?= $activePage === 'datafeed' ? 'active' : '' ?>">
        <i class="bi bi-pencil-square"></i> Data Entry
    </a>
    <a href="<?= BASE_URL ?>datafeed/bulk" class="nav-link <?= $activePage === 'datafeedbulk' ? 'active' : '' ?>">
        <i class="bi bi-grid-3x3-gap"></i> Bulk Entry
    </a>
    <?php endif; ?>
    <?php if (Auth::can('datafeed', 'view')): ?>
    <a href="<?= BASE_URL ?>datafeed/list" class="nav-link <?= $activePage === 'datafeedlist' ? 'active' : '' ?>">
        <i class="bi bi-table"></i> Data List
    </a>
    <?php endif; ?>

    <div class="nav-section">Account</div>
    <a href="<?= BASE_URL ?>auth/logout" class="nav-link">
        <i class="bi bi-box-arrow-left"></i> Logout
    </a>
</div>
