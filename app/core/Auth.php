<?php

require_once BASE_PATH . '/app/config/db.php';

class Auth {

    private static $cache = null;

    public static function check() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public static function user() {
        return $_SESSION['user'] ?? null;
    }

    public static function role() {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function permissions() {
        if (self::$cache !== null) return self::$cache;

        $role = self::role();
        if (!$role) { self::$cache = []; return self::$cache; }

        $db = getDB();
        $stmt = $db->prepare(
            "SELECT p.module, p.can_add, p.can_view, p.can_edit, p.can_delete, p.can_approve
             FROM permissions p
             JOIN roles r ON r.id = p.role_id
             WHERE r.name = ?"
        );
        $stmt->execute([$role]);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['module']] = [
                'add'     => (bool)$row['can_add'],
                'view'    => (bool)$row['can_view'],
                'edit'    => (bool)$row['can_edit'],
                'delete'  => (bool)$row['can_delete'],
                'approve' => (bool)$row['can_approve'],
            ];
        }
        self::$cache = $map;
        return $map;
    }

    public static function can($module, $action) {
        $perms = self::permissions();
        return !empty($perms[$module][$action]);
    }

    public static function require($module, $action) {
        self::check();
        if (!self::can($module, $action)) {
            self::denied($module, $action);
        }
    }

    public static function denied($module = '', $action = '') {
        http_response_code(403);
        $deniedModule = $module;
        $deniedAction = $action;
        require_once BASE_PATH . '/app/views/errors/403.php';
        exit;
    }
}
