    </div> <!-- end main-content -->

    <!-- Toast notifications -->
    <?php $flashMessages = Flash::consume(); ?>
    <?php if (!empty($flashMessages)): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <?php foreach ($flashMessages as $i => $f): ?>
            <?php
            $palette = [
                'success' => ['bg' => '#27ae60', 'icon' => 'check-circle'],
                'error'   => ['bg' => '#c0392b', 'icon' => 'exclamation-triangle'],
                'info'    => ['bg' => '#4ea8de', 'icon' => 'info-circle'],
            ];
            $p = $palette[$f['type']] ?? $palette['info'];
            ?>
            <div class="toast align-items-center text-white border-0 mb-2 toast-flash"
                 role="alert" aria-live="assertive" aria-atomic="true"
                 style="background-color: <?= $p['bg'] ?>; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-<?= $p['icon'] ?> me-2"></i>
                        <?= htmlspecialchars($f['message']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/chart.umd.min.js"></script>
    <script>
        // Auto-show + auto-dismiss flash toasts
        document.querySelectorAll('.toast-flash').forEach(function(el) {
            var t = new bootstrap.Toast(el, { delay: 4000, autohide: true });
            t.show();
        });
    </script>

</body>
</html>
