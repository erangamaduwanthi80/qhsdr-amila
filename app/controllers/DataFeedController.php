<?php

require_once BASE_PATH . '/app/models/DataFeed.php';
require_once BASE_PATH . '/app/models/Shift.php';
require_once BASE_PATH . '/app/models/Machine.php';
require_once BASE_PATH . '/app/models/Defect.php';
require_once BASE_PATH . '/app/models/Location.php';

class DataFeedController {

    private function userLocationId() {
        return !empty($_SESSION['user']['location_id']) ? (int)$_SESSION['user']['location_id'] : 0;
    }

    private function userLocations($userLoc) {
        if ($userLoc) {
            $loc = (new Location())->find($userLoc);
            return $loc ? [$loc] : [];
        }
        return (new Location())->getAll(true);
    }

    public function index() {
        Auth::require('datafeed', 'add');
        $userLoc      = $this->userLocationId();
        $shiftModel   = new Shift();
        $shifts       = $shiftModel->getAll($userLoc ?: null);
        // Build hour slot labels per shift: [shift_id => [hour_no => 'HH:MM – HH:MM']]
        $shiftHourLabels = [];
        foreach ($shifts as $s) {
            $shiftHourLabels[$s['id']] = $shiftModel->getHourSlotMap($s['id']);
        }
        $machines     = (new Machine())->getAll(null, $userLoc ?: null);
        $defects      = (new Defect())->getAll(null, $userLoc ?: null);
        $locations    = $this->userLocations($userLoc);
        $machineTypes = Machine::TYPES;
        $hourOptions  = Shift::HOUR_OPTIONS;
        $activePage   = 'datafeed';
        $error        = '';
        $success      = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'feed_date'   => trim($_POST['feed_date'] ?? ''),
                'shift_id'    => (int)($_POST['shift_id'] ?? 0),
                'hour_no'     => (int)($_POST['hour_no'] ?? 0),
                'location_id' => $userLoc ?: (int)($_POST['location_id'] ?? 0),
                'machine_id'  => (int)($_POST['machine_id'] ?? 0),
                'defect_id'   => (int)($_POST['defect_id'] ?? 0),
                'defect_qty'  => (int)($_POST['defect_qty'] ?? 0),
            ];

            if (empty($data['feed_date']) || !$data['shift_id'] || !$data['hour_no']
                || !$data['location_id'] || !$data['machine_id'] || !$data['defect_id']
                || $data['defect_qty'] <= 0) {
                $error = 'All fields are required and defect quantity must be greater than 0.';
            } else {
                $newId = (new DataFeed())->create($data);
                AuditLog::log('create', 'data_feed', $newId, [
                    'feed_date' => $data['feed_date'],
                    'shift_id'  => $data['shift_id'],
                    'hour_no'   => $data['hour_no'],
                    'machine_id'=> $data['machine_id'],
                    'defect_id' => $data['defect_id'],
                    'qty'       => $data['defect_qty'],
                ]);
                $success = 'Record saved successfully.';
            }
        }

        require_once BASE_PATH . '/app/views/datafeed/index.php';
    }

    public function bulk() {
        Auth::require('datafeed', 'add');
        $userLoc      = $this->userLocationId();
        $shiftModel   = new Shift();
        $shifts       = $shiftModel->getAll($userLoc ?: null);
        $shiftHourLabels = [];
        foreach ($shifts as $s) {
            $shiftHourLabels[$s['id']] = $shiftModel->getHourSlotMap($s['id']);
        }
        $machines     = (new Machine())->getAll(null, $userLoc ?: null);
        $defects      = (new Defect())->getAll(null, $userLoc ?: null);
        $locations    = $this->userLocations($userLoc);
        $machineTypes = Machine::TYPES;
        $activePage   = 'datafeedbulk';
        $error        = '';
        $success      = '';
        $savedCount   = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $header = [
                'feed_date'   => trim($_POST['feed_date'] ?? ''),
                'shift_id'    => (int)($_POST['shift_id'] ?? 0),
                'hour_no'     => (int)($_POST['hour_no'] ?? 0),
                'location_id' => $userLoc ?: (int)($_POST['location_id'] ?? 0),
            ];
            $rawRows = $_POST['rows'] ?? [];
            $rows = [];
            foreach ($rawRows as $r) {
                if (empty($r['machine_id']) || empty($r['defect_id']) || (int)($r['defect_qty'] ?? 0) <= 0) {
                    continue;
                }
                $rows[] = [
                    'machine_id' => (int)$r['machine_id'],
                    'defect_id'  => (int)$r['defect_id'],
                    'defect_qty' => (int)$r['defect_qty'],
                ];
            }

            if (empty($header['feed_date']) || !$header['shift_id'] || !$header['hour_no'] || !$header['location_id']) {
                $error = 'Date, Shift, Hour, and Location are required.';
            } elseif (empty($rows)) {
                $error = 'Add at least one defect row with Machine, Defect Type, and Quantity > 0.';
            } else {
                try {
                    $savedCount = (new DataFeed())->createBatch($header, $rows);
                    AuditLog::log('create_batch', 'data_feed', null, [
                        'feed_date'  => $header['feed_date'],
                        'shift_id'   => $header['shift_id'],
                        'hour_no'    => $header['hour_no'],
                        'location_id'=> $header['location_id'],
                        'rows_saved' => $savedCount,
                    ]);
                    $success = "Saved {$savedCount} defect record(s) successfully.";
                    $_POST = []; // clear form on success
                } catch (Exception $e) {
                    $error = 'Failed to save: ' . $e->getMessage();
                }
            }
        }

        require_once BASE_PATH . '/app/views/datafeed/bulk.php';
    }

    public function list() {
        Auth::require('datafeed', 'view');
        $userLoc = $this->userLocationId();
        $filters = [
            'date_from'   => $_GET['date_from'] ?? '',
            'date_to'     => $_GET['date_to'] ?? '',
            'shift_id'    => (int)($_GET['shift_id'] ?? 0),
            'location_id' => $userLoc ?: (int)($_GET['location_id'] ?? 0),
            'machine_id'  => (int)($_GET['machine_id'] ?? 0),
            'defect_id'   => (int)($_GET['defect_id'] ?? 0),
        ];

        list($sort, $dir) = Sort::pick('feed_date', ['feed_date','shift','hour_no','location','machine_type','machine_no','defect_code','defect_name','qty']);
        $activeFilters = array_filter($filters);
        $dataFeed  = new DataFeed();

        // CSV export bypasses pagination (export the full filtered set)
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $records = $dataFeed->getList($activeFilters, $sort, $dir);
            $this->exportCsv($records);
        }

        $perPage    = 25;
        $totalRows  = $dataFeed->countList($activeFilters);
        $paginator  = Paginator::fromRequest($totalRows, $perPage);
        $records    = $dataFeed->getList($activeFilters, $sort, $dir, $perPage, $paginator->offset);

        $shifts    = (new Shift())->getAll($userLoc ?: null);
        $machines  = (new Machine())->getAll(null, $userLoc ?: null);
        $defects   = (new Defect())->getAll(null, $userLoc ?: null);
        $locations = $this->userLocations($userLoc);
        $activePage = 'datafeedlist';

        require_once BASE_PATH . '/app/views/datafeed/list.php';
    }

    public function delete() {
        Auth::require('datafeed', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        (new DataFeed())->delete($id);
        AuditLog::log('delete', 'data_feed', $id);
        Flash::success("Record #{$id} deleted.");
        header('Location: ' . BASE_URL . 'datafeed/list');
        exit;
    }

    public function getHoursByShift() {
        Auth::check();
        $shiftId = (int)($_GET['shift_id'] ?? 0);
        $shiftModel = new Shift();
        $shift = $shiftModel->find($shiftId);
        if (!$shift) { header('Content-Type: application/json'); echo json_encode([]); exit; }
        $slots = $shiftModel->getHourSlots($shiftId);
        $result = [];
        foreach ($slots as $s) {
            $result[] = [
                'hour_no'    => $s['hour_no'],
                'start_time' => $s['start_time'],
                'end_time'   => $s['end_time'],
                'label'      => $s['start_time'] . ' – ' . $s['end_time'],
            ];
        }
        // If no slots defined, return numbered placeholders up to shift hours
        if (empty($result)) {
            for ($i = 1; $i <= $shift['hours']; $i++) {
                $result[] = ['hour_no' => $i, 'start_time' => '', 'end_time' => '', 'label' => ''];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function getMachinesByType() {
        Auth::check();
        $userLoc    = $this->userLocationId();
        $type       = $_GET['type'] ?? '';
        $locationId = $userLoc ?: (int)($_GET['location_id'] ?? 0);
        $machines   = (new Machine())->getAll($type ?: null, $locationId ?: null);
        header('Content-Type: application/json');
        echo json_encode($machines);
        exit;
    }

    public function getDefectsByType() {
        Auth::check();
        $userLoc = $this->userLocationId();
        $type    = $_GET['type'] ?? '';
        $defects = (new Defect())->getAll($type ?: null, $userLoc ?: null);
        header('Content-Type: application/json');
        echo json_encode($defects);
        exit;
    }

    private function exportCsv($records) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="data_feed_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Date', 'Shift', 'Hour', 'Location Code', 'Location Name', 'Machine Type', 'Machine No', 'Machine Name', 'Defect Code', 'Defect Name', 'Qty']);
        foreach ($records as $row) {
            fputcsv($out, [
                $row['id'], $row['feed_date'], $row['shift_name'], $row['hour_no'],
                $row['location_code'] ?? '', $row['location_name'] ?? '',
                $row['machine_type'], $row['machine_no'], $row['machine_name'],
                $row['defect_code'], $row['defect_name'], $row['defect_qty'],
            ]);
        }
        fclose($out);
        exit;
    }
}
