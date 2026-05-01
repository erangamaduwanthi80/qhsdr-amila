<?php

require_once BASE_PATH . '/app/config/db.php';

class Machine {

    const TYPES = ['Pressing Machine', 'Wrapping Machine', 'Plug Hole Machine'];

    public function getAll($type = null, $locationId = null) {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($type) {
            $where .= ' AND m.machine_type = ?';
            $params[] = $type;
        }
        if ($locationId) {
            $where .= ' AND m.location_id = ?';
            $params[] = $locationId;
        }
        $sql = "SELECT m.*, l.code AS location_code, l.name AS location_name
                FROM machines m
                LEFT JOIN locations l ON l.id = m.location_id
                WHERE $where
                ORDER BY m.machine_type, m.machine_no";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function search($keyword = '', $type = '', $locationId = 0, $sort = 'machine_type', $dir = 'asc') {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($keyword !== '') {
            $where .= ' AND (m.machine_no LIKE ? OR m.machine_name LIKE ?)';
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        if ($type !== '') {
            $where .= ' AND m.machine_type = ?';
            $params[] = $type;
        }
        if ($locationId) {
            $where .= ' AND m.location_id = ?';
            $params[] = $locationId;
        }
        $sortMap = [
            'machine_type' => 'm.machine_type',
            'machine_no'   => 'm.machine_no',
            'machine_name' => 'm.machine_name',
            'location'     => 'l.code',
        ];
        $orderBy = $sortMap[$sort] ?? 'm.machine_type';
        $dirSql  = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';
        $sql = "SELECT m.*, l.code AS location_code, l.name AS location_name
                FROM machines m
                LEFT JOIN locations l ON l.id = m.location_id
                WHERE $where
                ORDER BY $orderBy $dirSql, m.machine_no ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT m.*, l.code AS location_code, l.name AS location_name
             FROM machines m
             LEFT JOIN locations l ON l.id = m.location_id
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO machines (machine_type, location_id, machine_no, machine_name, machine_photo, remark)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['machine_type'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
            $data['machine_no'],
            $data['machine_name'],
            $data['machine_photo'] ?? null,
            $data['remark'] ?? null,
        ]);
        return $db->lastInsertId();
    }

    public function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE machines SET machine_type=?, location_id=?, machine_no=?, machine_name=?, machine_photo=?, remark=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['machine_type'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
            $data['machine_no'],
            $data['machine_name'],
            $data['machine_photo'] ?? null,
            $data['remark'] ?? null,
            $id,
        ]);
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM machines WHERE id = ?");
        $stmt->execute([$id]);
    }
}
