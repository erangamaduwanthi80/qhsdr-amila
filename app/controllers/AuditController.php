<?php

class AuditController {

    public function index() {
        Auth::require('audit', 'view');
        $filters = array_filter([
            'user_id'   => (int)($_GET['user_id'] ?? 0),
            'action'    => trim($_GET['action'] ?? ''),
            'entity'    => trim($_GET['entity'] ?? ''),
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to'   => trim($_GET['date_to'] ?? ''),
        ]);
        $perPage   = 50;
        $totalRows = AuditLog::count($filters);
        $paginator = Paginator::fromRequest($totalRows, $perPage);
        $entries   = AuditLog::search($filters, $perPage, $paginator->offset);
        $users     = AuditLog::distinctUsers();
        $entities  = AuditLog::distinctEntities();
        $activePage = 'audit';
        require_once BASE_PATH . '/app/views/audit/index.php';
    }

    public function purge() {
        Auth::require('audit', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $days = (int)($_POST['days'] ?? 0);
        if ($days < 1) {
            $_SESSION['flash_error'] = 'Specify number of days (minimum 1) to keep.';
            header('Location: ' . BASE_URL . 'audit/index');
            exit;
        }
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM audit_log WHERE created_at < (NOW() - INTERVAL ? DAY)");
        $stmt->execute([$days]);
        $count = $stmt->rowCount();
        AuditLog::log('purge', 'audit_log', null, ['kept_days' => $days, 'deleted_count' => $count]);
        $_SESSION['flash_success'] = "Purged {$count} audit log entries older than {$days} days.";
        header('Location: ' . BASE_URL . 'audit/index');
        exit;
    }
}
