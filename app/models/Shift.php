<?php

require_once BASE_PATH . '/app/config/db.php';

class Shift {

    const HOUR_OPTIONS = [8, 9, 10, 11, 12];

    public function getAll($locationId = null) {
        $db = getDB();
        $where = '1=1';
        $params = [];
        if ($locationId !== null && $locationId !== '' && (int)$locationId > 0) {
            $where .= ' AND s.location_id = ?';
            $params[] = (int)$locationId;
        }
        $sql = "SELECT s.*, l.code AS location_code, l.name AS location_name
                FROM shifts s
                LEFT JOIN locations l ON l.id = s.location_id
                WHERE $where
                ORDER BY l.code IS NULL, l.code, s.id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT s.*, l.code AS location_code, l.name AS location_name
             FROM shifts s
             LEFT JOIN locations l ON l.id = s.location_id
             WHERE s.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($shiftName, $hours, $locationId = null) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO shifts (location_id, shift_name, hours) VALUES (?,?,?)");
        $stmt->execute([
            !empty($locationId) ? (int)$locationId : null,
            $shiftName,
            $hours,
        ]);
        return $db->lastInsertId();
    }

    public function update($id, $shiftName, $hours, $locationId = null) {
        $db = getDB();
        // If hours count changed, clear existing hour slots
        $current = $this->find($id);
        if ((int)$current['hours'] !== (int)$hours) {
            $db->prepare("DELETE FROM shift_hours WHERE shift_id = ?")->execute([$id]);
        }
        $stmt = $db->prepare("UPDATE shifts SET location_id=?, shift_name=?, hours=? WHERE id=?");
        $stmt->execute([
            !empty($locationId) ? (int)$locationId : null,
            $shiftName,
            $hours,
            $id,
        ]);
    }

    public function delete($id) {
        $db = getDB();
        $db->prepare("DELETE FROM shift_hours WHERE shift_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM shifts WHERE id = ?")->execute([$id]);
    }

    public function getHourSlots($shiftId) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM shift_hours WHERE shift_id = ? ORDER BY hour_no");
        $stmt->execute([$shiftId]);
        return $stmt->fetchAll();
    }

    public function saveHourSlots($shiftId, $slots) {
        $db = getDB();
        $db->prepare("DELETE FROM shift_hours WHERE shift_id = ?")->execute([$shiftId]);
        $stmt = $db->prepare(
            "INSERT INTO shift_hours (shift_id, hour_no, start_time, end_time) VALUES (?,?,?,?)"
        );
        foreach ($slots as $no => $slot) {
            if (!empty($slot['start_time']) && !empty($slot['end_time'])) {
                $stmt->execute([$shiftId, $no + 1, $slot['start_time'], $slot['end_time']]);
            }
        }
    }

    public function getHourSlotMap($shiftId) {
        $slots = $this->getHourSlots($shiftId);
        $map = [];
        foreach ($slots as $s) {
            $map[$s['hour_no']] = $s['start_time'] . ' – ' . $s['end_time'];
        }
        return $map;
    }
}
