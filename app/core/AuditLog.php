<?php

require_once BASE_PATH . '/app/config/db.php';

class AuditLog {

    public static function log($action, $entity, $entityId = null, $snapshot = null) {
        $db = getDB();
        $user = $_SESSION['user'] ?? null;
        $stmt = $db->prepare(
            "INSERT INTO audit_log (user_id, username, action, entity, entity_id, snapshot, ip_address)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $user['id']       ?? null,
            $user['username'] ?? null,
            $action,
            $entity,
            $entityId ? (int)$entityId : null,
            $snapshot ? json_encode($snapshot, JSON_UNESCAPED_UNICODE) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    private static function buildWhere($filters) {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['user_id']))  { $where[] = 'user_id = ?'; $params[] = (int)$filters['user_id']; }
        if (!empty($filters['action']))   { $where[] = 'action = ?';  $params[] = $filters['action']; }
        if (!empty($filters['entity']))   { $where[] = 'entity = ?';  $params[] = $filters['entity']; }
        if (!empty($filters['date_from'])){ $where[] = 'created_at >= ?'; $params[] = $filters['date_from'] . ' 00:00:00'; }
        if (!empty($filters['date_to']))  { $where[] = 'created_at <= ?'; $params[] = $filters['date_to'] . ' 23:59:59'; }
        return [implode(' AND ', $where), $params];
    }

    public static function count($filters = []) {
        $db = getDB();
        list($whereSql, $params) = self::buildWhere($filters);
        $stmt = $db->prepare("SELECT COUNT(*) FROM audit_log WHERE $whereSql");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function search($filters = [], $limit = 50, $offset = 0) {
        $db = getDB();
        list($whereSql, $params) = self::buildWhere($filters);
        $sql = "SELECT * FROM audit_log
                WHERE $whereSql
                ORDER BY id DESC
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function distinctEntities() {
        $db = getDB();
        return $db->query("SELECT DISTINCT entity FROM audit_log ORDER BY entity")->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function distinctUsers() {
        $db = getDB();
        return $db->query(
            "SELECT user_id, username FROM audit_log
             WHERE user_id IS NOT NULL
             GROUP BY user_id, username
             ORDER BY username"
        )->fetchAll();
    }
}
