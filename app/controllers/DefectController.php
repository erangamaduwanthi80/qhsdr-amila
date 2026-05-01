<?php

require_once BASE_PATH . '/app/models/Defect.php';
require_once BASE_PATH . '/app/models/Machine.php';
require_once BASE_PATH . '/app/models/Location.php';

class DefectController {

    public function index() {
        Auth::require('defects', 'view');
        $filterType = $_GET['machine_type'] ?? '';
        $filterLoc  = (int)($_GET['location_id'] ?? 0);
        $defects = (new Defect())->getAll($filterType ?: null, $filterLoc ?: null);
        $machineTypes = Machine::TYPES;
        $locations = (new Location())->getAll(true);
        $activePage = 'defects';
        require_once BASE_PATH . '/app/views/defects/index.php';
    }

    public function create() {
        Auth::require('defects', 'add');
        $error = '';
        $defect = null;
        $machineTypes = Machine::TYPES;
        $locations = (new Location())->getAll(true);
        $activePage = 'defects';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'machine_type' => trim($_POST['machine_type'] ?? ''),
                'location_id'  => (int)($_POST['location_id'] ?? 0),
                'defect_code'  => trim($_POST['defect_code'] ?? ''),
                'defect_name'  => trim($_POST['defect_name'] ?? ''),
            ];

            if (empty($data['machine_type']) || empty($data['defect_code']) || empty($data['defect_name'])) {
                $error = 'Machine Type, Defect Code, and Defect Name are required.';
            } else {
                $newId = (new Defect())->create($data);
                AuditLog::log('create', 'defects', $newId, $data);
                Flash::success("Defect {$data['defect_code']} created.");
                header('Location: ' . BASE_URL . 'defects/index');
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/defects/form.php';
    }

    public function edit() {
        Auth::require('defects', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $defectModel = new Defect();
        $defect = $defectModel->find($id);
        if (!$defect) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $machineTypes = Machine::TYPES;
        $locations = (new Location())->getAll(true);
        $activePage = 'defects';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'machine_type' => trim($_POST['machine_type'] ?? ''),
                'location_id'  => (int)($_POST['location_id'] ?? 0),
                'defect_code'  => trim($_POST['defect_code'] ?? ''),
                'defect_name'  => trim($_POST['defect_name'] ?? ''),
            ];

            if (empty($data['machine_type']) || empty($data['defect_code']) || empty($data['defect_name'])) {
                $error = 'Machine Type, Defect Code, and Defect Name are required.';
            } else {
                $defectModel->update($id, $data);
                AuditLog::log('update', 'defects', $id, $data);
                Flash::success("Defect {$data['defect_code']} updated.");
                header('Location: ' . BASE_URL . 'defects/index');
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/defects/form.php';
    }

    public function delete() {
        Auth::require('defects', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $defectModel = new Defect();
        $deleted = $defectModel->find($id);
        $defectModel->delete($id);
        AuditLog::log('delete', 'defects', $id, $deleted ? [
            'defect_code' => $deleted['defect_code'], 'defect_name' => $deleted['defect_name']
        ] : null);
        Flash::success($deleted ? "Defect {$deleted['defect_code']} deleted." : 'Defect deleted.');
        header('Location: ' . BASE_URL . 'defects/index');
        exit;
    }
}
