<?php

require_once BASE_PATH . '/app/config/db.php';

class Defect {

    public function getAll($machineType = null, $locationId = null) {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($machineType) {
            $where .= ' AND d.machine_type = ?';
            $params[] = $machineType;
        }
        if ($locationId) {
            $where .= ' AND d.location_id = ?';
            $params[] = $locationId;
        }
        $sql = "SELECT d.*, l.code AS location_code, l.name AS location_name
                FROM defects d
                LEFT JOIN locations l ON l.id = d.location_id
                WHERE $where
                ORDER BY d.machine_type, d.defect_code";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT d.*, l.code AS location_code, l.name AS location_name
             FROM defects d
             LEFT JOIN locations l ON l.id = d.location_id
             WHERE d.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO defects (machine_type, location_id, defect_code, defect_name) VALUES (?,?,?,?)"
        );
        $stmt->execute([
            $data['machine_type'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
            $data['defect_code'],
            $data['defect_name'],
        ]);
        return $db->lastInsertId();
    }

    public function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE defects SET machine_type=?, location_id=?, defect_code=?, defect_name=? WHERE id=?"
        );
        $stmt->execute([
            $data['machine_type'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
            $data['defect_code'],
            $data['defect_name'],
            $id,
        ]);
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM defects WHERE id = ?");
        $stmt->execute([$id]);
    }
}
