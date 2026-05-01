<?php

require_once BASE_PATH . '/app/models/Location.php';

class LocationController {

    public function index() {
        Auth::require('locations', 'view');
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $locations = (new Location())->search($search, $status);
        $activePage = 'locations';
        require_once BASE_PATH . '/app/views/locations/index.php';
    }

    public function create() {
        Auth::require('locations', 'add');
        $error = '';
        $location = null;
        $activePage = 'locations';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'code'   => trim($_POST['code'] ?? ''),
                'name'   => trim($_POST['name'] ?? ''),
                'status' => $_POST['status'] ?? 'active',
                'remark' => trim($_POST['remark'] ?? ''),
            ];

            if (empty($data['code']) || empty($data['name'])) {
                $error = 'Location code and name are required.';
            } elseif (!in_array($data['status'], ['active', 'inactive'])) {
                $error = 'Invalid status.';
            } else {
                $existing = (new Location())->findByCode($data['code']);
                if ($existing) {
                    $error = 'A location with that code already exists.';
                } else {
                    $newId = (new Location())->create($data);
                    AuditLog::log('create', 'locations', $newId, $data);
                    Flash::success("Location {$data['code']} created.");
                    header('Location: ' . BASE_URL . 'locations/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/locations/form.php';
    }

    public function edit() {
        Auth::require('locations', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $locationModel = new Location();
        $location = $locationModel->find($id);
        if (!$location) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $activePage = 'locations';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'code'   => trim($_POST['code'] ?? ''),
                'name'   => trim($_POST['name'] ?? ''),
                'status' => $_POST['status'] ?? 'active',
                'remark' => trim($_POST['remark'] ?? ''),
            ];

            if (empty($data['code']) || empty($data['name'])) {
                $error = 'Location code and name are required.';
            } elseif (!in_array($data['status'], ['active', 'inactive'])) {
                $error = 'Invalid status.';
            } else {
                $existing = $locationModel->findByCode($data['code']);
                if ($existing && (int)$existing['id'] !== $id) {
                    $error = 'A different location already uses that code.';
                } else {
                    $locationModel->update($id, $data);
                    AuditLog::log('update', 'locations', $id, $data);
                    Flash::success("Location {$data['code']} updated.");
                    header('Location: ' . BASE_URL . 'locations/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/locations/form.php';
    }

    public function delete() {
        Auth::require('locations', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $locationModel = new Location();
        $deleted = $locationModel->find($id);
        $locationModel->delete($id);
        AuditLog::log('delete', 'locations', $id, $deleted ? [
            'code' => $deleted['code'], 'name' => $deleted['name']
        ] : null);
        Flash::success($deleted ? "Location {$deleted['code']} deleted." : 'Location deleted.');
        header('Location: ' . BASE_URL . 'locations/index');
        exit;
    }
}
