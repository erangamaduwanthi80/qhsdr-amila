<?php

require_once BASE_PATH . '/app/config/db.php';

class Role {

    public function getAll() {
        $db = getDB();
        return $db->query("SELECT * FROM roles ORDER BY name")->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO roles (name) VALUES (?)");
        $stmt->execute([$name]);
        return $db->lastInsertId();
    }

    public function update($id, $name) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE roles SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getPermissions($roleId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM permissions WHERE role_id = ?");
        $stmt->execute([$roleId]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['module']] = $row;
        }
        return $map;
    }

    public function savePermissions($roleId, $modules, $actions) {
        $db = getDB();
        $db->prepare("DELETE FROM permissions WHERE role_id = ?")->execute([$roleId]);
        $stmt = $db->prepare(
            "INSERT INTO permissions (role_id, module, can_add, can_view, can_edit, can_delete, can_approve)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($modules as $module) {
            $stmt->execute([
                $roleId,
                $module,
                in_array($module . '_add',     $actions) ? 1 : 0,
                in_array($module . '_view',    $actions) ? 1 : 0,
                in_array($module . '_edit',    $actions) ? 1 : 0,
                in_array($module . '_delete',  $actions) ? 1 : 0,
                in_array($module . '_approve', $actions) ? 1 : 0,
            ]);
        }
    }
}
