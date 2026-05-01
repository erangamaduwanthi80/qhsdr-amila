<?php

require_once BASE_PATH . '/app/models/Machine.php';
require_once BASE_PATH . '/app/models/Location.php';

class MachineController {

    public function index() {
        Auth::require('machines', 'view');
        $search     = trim($_GET['search'] ?? '');
        $type       = trim($_GET['type'] ?? '');
        if ($type === '' || !in_array($type, Machine::TYPES, true)) {
            $type = Machine::TYPES[0];
        }
        $locationId = (int)($_GET['location_id'] ?? 0);
        list($sort, $dir) = Sort::pick('machine_type', ['machine_type','machine_no','machine_name','location']);
        $machines   = (new Machine())->search($search, $type, $locationId, $sort, $dir);
        $locations  = (new Location())->getAll(true);
        $activePage = 'machines';
        require_once BASE_PATH . '/app/views/machines/index.php';
    }

    public function create() {
        Auth::require('machines', 'add');
        $error = '';
        $machine = null;
        $locations = (new Location())->getAll(true);
        $activePage = 'machines';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'machine_type' => trim($_POST['machine_type'] ?? ''),
                'location_id'  => (int)($_POST['location_id'] ?? 0),
                'machine_no'   => trim($_POST['machine_no'] ?? ''),
                'machine_name' => trim($_POST['machine_name'] ?? ''),
                'remark'       => trim($_POST['remark'] ?? ''),
                'machine_photo' => null,
            ];

            if (empty($data['machine_type']) || empty($data['machine_no']) || empty($data['machine_name'])) {
                $error = 'Machine Type, No, and Name are required.';
            } else {
                if (!empty($_FILES['machine_photo']['name'])) {
                    $uploadResult = $this->handleUpload($_FILES['machine_photo']);
                    if ($uploadResult['error']) {
                        $error = $uploadResult['error'];
                    } else {
                        $data['machine_photo'] = $uploadResult['filename'];
                    }
                }

                if (!$error) {
                    $newId = (new Machine())->create($data);
                    AuditLog::log('create', 'machines', $newId, [
                        'machine_type' => $data['machine_type'],
                        'machine_no'   => $data['machine_no'],
                        'machine_name' => $data['machine_name'],
                        'location_id'  => $data['location_id'] ?? null,
                    ]);
                    Flash::success("Machine {$data['machine_no']} created.");
                    header('Location: ' . BASE_URL . 'machines/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/machines/form.php';
    }

    public function edit() {
        Auth::require('machines', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $machineModel = new Machine();
        $machine = $machineModel->find($id);
        if (!$machine) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $locations = (new Location())->getAll(true);
        $activePage = 'machines';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'machine_type'  => trim($_POST['machine_type'] ?? ''),
                'location_id'   => (int)($_POST['location_id'] ?? 0),
                'machine_no'    => trim($_POST['machine_no'] ?? ''),
                'machine_name'  => trim($_POST['machine_name'] ?? ''),
                'remark'        => trim($_POST['remark'] ?? ''),
                'machine_photo' => $machine['machine_photo'],
            ];

            if (empty($data['machine_type']) || empty($data['machine_no']) || empty($data['machine_name'])) {
                $error = 'Machine Type, No, and Name are required.';
            } else {
                if (!empty($_FILES['machine_photo']['name'])) {
                    $uploadResult = $this->handleUpload($_FILES['machine_photo']);
                    if ($uploadResult['error']) {
                        $error = $uploadResult['error'];
                    } else {
                        $data['machine_photo'] = $uploadResult['filename'];
                    }
                }

                if (!$error) {
                    $machineModel->update($id, $data);
                    AuditLog::log('update', 'machines', $id, [
                        'machine_type' => $data['machine_type'],
                        'machine_no'   => $data['machine_no'],
                        'machine_name' => $data['machine_name'],
                        'location_id'  => $data['location_id'] ?? null,
                    ]);
                    Flash::success("Machine {$data['machine_no']} updated.");
                    header('Location: ' . BASE_URL . 'machines/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/machines/form.php';
    }

    public function delete() {
        Auth::require('machines', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $machineModel = new Machine();
        $deleted = $machineModel->find($id);
        $machineModel->delete($id);
        AuditLog::log('delete', 'machines', $id, $deleted ? [
            'machine_no' => $deleted['machine_no'], 'machine_name' => $deleted['machine_name']
        ] : null);
        Flash::success($deleted ? "Machine {$deleted['machine_no']} deleted." : 'Machine deleted.');
        header('Location: ' . BASE_URL . 'machines/index');
        exit;
    }

    private function handleUpload($file) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $allowedMimes)) {
            return ['error' => 'Only JPG, PNG, GIF, WEBP images are allowed.', 'filename' => null];
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('machine_') . '.' . strtolower($ext);
        $dest = BASE_PATH . '/uploads/machines/' . $filename;

        if (!is_dir(BASE_PATH . '/uploads/machines/')) {
            mkdir(BASE_PATH . '/uploads/machines/', 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $dest);
        return ['error' => null, 'filename' => $filename];
    }
}
