<?php

require_once BASE_PATH . '/app/config/db.php';

class Location {

    public function getAll($activeOnly = false) {
        $db = getDB();
        $sql = "SELECT * FROM locations";
        if ($activeOnly) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY name";
        return $db->query($sql)->fetchAll();
    }

    public function search($keyword = '', $status = '') {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($keyword !== '') {
            $where .= ' AND (code LIKE ? OR name LIKE ?)';
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = $status;
        }
        $stmt = $db->prepare("SELECT * FROM locations WHERE $where ORDER BY name");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM locations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCode($code) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM locations WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO locations (code, name, status, remark) VALUES (?,?,?,?)"
        );
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['status'] ?? 'active',
            $data['remark'] ?? null,
        ]);
        return $db->lastInsertId();
    }

    public function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE locations SET code=?, name=?, status=?, remark=? WHERE id=?"
        );
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['status'] ?? 'active',
            $data['remark'] ?? null,
            $id,
        ]);
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM locations WHERE id = ?");
        $stmt->execute([$id]);
    }
}
