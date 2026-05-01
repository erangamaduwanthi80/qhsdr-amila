<?php

require_once BASE_PATH . '/app/config/db.php';

class User {

    public function getAll($search = '', $role = '', $sort = 'username', $dir = 'asc', $locationId = 0) {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($search !== '') {
            $where .= ' AND (u.username LIKE ? OR u.name LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($role !== '') {
            $where .= ' AND u.role = ?';
            $params[] = $role;
        }
        if ($locationId > 0) {
            $where .= ' AND u.location_id = ?';
            $params[] = (int)$locationId;
        }
        $sortMap = [
            'username'   => 'u.username',
            'name'       => 'u.name',
            'role'       => 'u.role',
            'location'   => 'l.code',
            'created_at' => 'u.created_at',
        ];
        $orderBy = $sortMap[$sort] ?? 'u.username';
        $dirSql  = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';
        $sql = "SELECT u.id, u.username, u.name, u.role, u.location_id, u.created_at,
                       l.code AS location_code, l.name AS location_name
                FROM users u
                LEFT JOIN locations l ON l.id = u.location_id
                WHERE $where
                ORDER BY $orderBy $dirSql";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT u.id, u.username, u.name, u.role, u.location_id, u.created_at,
                    l.code AS location_code, l.name AS location_name
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT u.*, l.code AS location_code, l.name AS location_name
             FROM users u
             LEFT JOIN locations l ON l.id = u.location_id
             WHERE u.username = ? LIMIT 1"
        );
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function verifyPassword($inputPassword, $hashedPassword) {
        return password_verify($inputPassword, $hashedPassword);
    }

    public function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO users (username, password, name, role, location_id) VALUES (?,?,?,?,?)"
        );
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['name'],
            $data['role'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
        ]);
        return $db->lastInsertId();
    }

    public function update($id, $data) {
        $db = getDB();
        $locationId = !empty($data['location_id']) ? (int)$data['location_id'] : null;
        if (!empty($data['password'])) {
            $stmt = $db->prepare(
                "UPDATE users SET username=?, password=?, name=?, role=?, location_id=? WHERE id=?"
            );
            $stmt->execute([
                $data['username'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['name'],
                $data['role'],
                $locationId,
                $id,
            ]);
        } else {
            $stmt = $db->prepare(
                "UPDATE users SET username=?, name=?, role=?, location_id=? WHERE id=?"
            );
            $stmt->execute([
                $data['username'],
                $data['name'],
                $data['role'],
                $locationId,
                $id,
            ]);
        }
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
