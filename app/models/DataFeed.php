<?php

require_once BASE_PATH . '/app/config/db.php';

class DataFeed {

    private function buildWhere($filters) {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['date_from']))   { $where[] = 'df.feed_date >= ?';  $params[] = $filters['date_from']; }
        if (!empty($filters['date_to']))     { $where[] = 'df.feed_date <= ?';  $params[] = $filters['date_to']; }
        if (!empty($filters['shift_id']))    { $where[] = 'df.shift_id = ?';    $params[] = $filters['shift_id']; }
        if (!empty($filters['location_id'])) { $where[] = 'df.location_id = ?'; $params[] = $filters['location_id']; }
        if (!empty($filters['machine_id']))  { $where[] = 'df.machine_id = ?';  $params[] = $filters['machine_id']; }
        if (!empty($filters['defect_id']))   { $where[] = 'df.defect_id = ?';   $params[] = $filters['defect_id']; }
        return [implode(' AND ', $where), $params];
    }

    public function countList($filters = []) {
        $db = getDB();
        list($whereSql, $params) = $this->buildWhere($filters);
        $sql = "SELECT COUNT(*) FROM data_feed df WHERE $whereSql";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getList($filters = [], $sort = 'feed_date', $dir = 'desc', $limit = null, $offset = 0) {
        $db = getDB();
        list($whereSql, $params) = $this->buildWhere($filters);

        $sortMap = [
            'feed_date'    => 'df.feed_date',
            'shift'        => 's.shift_name',
            'hour_no'      => 'df.hour_no',
            'location'     => 'l.code',
            'machine_type' => 'm.machine_type',
            'machine_no'   => 'm.machine_no',
            'defect_code'  => 'd.defect_code',
            'defect_name'  => 'd.defect_name',
            'qty'          => 'df.defect_qty',
        ];
        $orderBy = $sortMap[$sort] ?? 'df.feed_date';
        $dirSql  = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';

        $sql = "SELECT df.*, s.shift_name, m.machine_no, m.machine_name, m.machine_type,
                       d.defect_code, d.defect_name,
                       l.code AS location_code, l.name AS location_name
                FROM data_feed df
                JOIN shifts s ON s.id = df.shift_id
                JOIN machines m ON m.id = df.machine_id
                JOIN defects d ON d.id = df.defect_id
                LEFT JOIN locations l ON l.id = df.location_id
                WHERE $whereSql
                ORDER BY $orderBy $dirSql, df.id DESC";

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO data_feed (feed_date, shift_id, hour_no, location_id, machine_id, defect_id, defect_qty)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $data['feed_date'],
            $data['shift_id'],
            $data['hour_no'],
            !empty($data['location_id']) ? (int)$data['location_id'] : null,
            $data['machine_id'],
            $data['defect_id'],
            $data['defect_qty'],
        ]);
        return $db->lastInsertId();
    }

    public function createBatch($header, $rows) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO data_feed (feed_date, shift_id, hour_no, location_id, machine_id, defect_id, defect_qty)
             VALUES (?,?,?,?,?,?,?)"
        );
        $db->beginTransaction();
        try {
            $count = 0;
            foreach ($rows as $r) {
                $stmt->execute([
                    $header['feed_date'],
                    $header['shift_id'],
                    $header['hour_no'],
                    !empty($header['location_id']) ? (int)$header['location_id'] : null,
                    (int)$r['machine_id'],
                    (int)$r['defect_id'],
                    (int)$r['defect_qty'],
                ]);
                $count++;
            }
            $db->commit();
            return $count;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM data_feed WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getChartData($dateFrom = null, $dateTo = null, $locationId = null) {
        $db = getDB();
        $cond = '1=1';
        $params = [];
        if ($dateFrom)   { $cond .= ' AND df.feed_date >= ?'; $params[] = $dateFrom; }
        if ($dateTo)     { $cond .= ' AND df.feed_date <= ?'; $params[] = $dateTo; }
        if ($locationId) { $cond .= ' AND df.location_id = ?'; $params[] = $locationId; }

        $byMachineType = $db->prepare(
            "SELECT m.machine_type, SUM(df.defect_qty) AS total
             FROM data_feed df JOIN machines m ON m.id = df.machine_id
             WHERE $cond GROUP BY m.machine_type ORDER BY total DESC"
        );
        $byMachineType->execute($params);

        $byLocation = $db->prepare(
            "SELECT COALESCE(l.name, '— Unassigned —') AS location_name,
                    SUM(df.defect_qty) AS total
             FROM data_feed df
             LEFT JOIN locations l ON l.id = df.location_id
             WHERE $cond
             GROUP BY df.location_id, l.name
             ORDER BY total DESC"
        );
        $byLocation->execute($params);

        $byDefect = $db->prepare(
            "SELECT d.defect_name, SUM(df.defect_qty) AS total
             FROM data_feed df JOIN defects d ON d.id = df.defect_id
             WHERE $cond GROUP BY d.defect_name ORDER BY total DESC LIMIT 10"
        );
        $byDefect->execute($params);

        $byDate = $db->prepare(
            "SELECT df.feed_date, SUM(df.defect_qty) AS total
             FROM data_feed df WHERE $cond
             GROUP BY df.feed_date ORDER BY df.feed_date ASC"
        );
        $byDate->execute($params);

        $byShift = $db->prepare(
            "SELECT s.shift_name, m.machine_type, SUM(df.defect_qty) AS total
             FROM data_feed df
             JOIN shifts s ON s.id = df.shift_id
             JOIN machines m ON m.id = df.machine_id
             WHERE $cond
             GROUP BY s.shift_name, m.machine_type
             ORDER BY s.shift_name, m.machine_type"
        );
        $byShift->execute($params);

        $stmtTotal = $db->prepare("SELECT SUM(df.defect_qty) AS total FROM data_feed df WHERE $cond");
        $stmtTotal->execute($params);
        $totalDefects = (int)$stmtTotal->fetchColumn();

        $stmtMachine = $db->prepare(
            "SELECT m.machine_type FROM data_feed df JOIN machines m ON m.id = df.machine_id
             WHERE $cond GROUP BY m.machine_type ORDER BY SUM(df.defect_qty) DESC LIMIT 1"
        );
        $stmtMachine->execute($params);
        $topMachine = $stmtMachine->fetchColumn();

        $stmtDefect = $db->prepare(
            "SELECT d.defect_name FROM data_feed df JOIN defects d ON d.id = df.defect_id
             WHERE $cond GROUP BY d.defect_name ORDER BY SUM(df.defect_qty) DESC LIMIT 1"
        );
        $stmtDefect->execute($params);
        $topDefect = $stmtDefect->fetchColumn();

        $stmtLocation = $db->prepare(
            "SELECT COALESCE(l.name, '— Unassigned —') AS location_name
             FROM data_feed df
             LEFT JOIN locations l ON l.id = df.location_id
             WHERE $cond
             GROUP BY df.location_id, l.name
             ORDER BY SUM(df.defect_qty) DESC LIMIT 1"
        );
        $stmtLocation->execute($params);
        $topLocation = $stmtLocation->fetchColumn();

        return [
            'byMachineType' => $byMachineType->fetchAll(),
            'byLocation'    => $byLocation->fetchAll(),
            'byDefect'      => $byDefect->fetchAll(),
            'byDate'        => $byDate->fetchAll(),
            'byShift'       => $byShift->fetchAll(),
            'kpi'           => [
                'total_defects' => $totalDefects,
                'top_machine'   => $topMachine  ?: '—',
                'top_defect'    => $topDefect   ?: '—',
                'top_location'  => $topLocation ?: '—',
            ],
        ];
    }
}
