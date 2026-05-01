<?php

require_once BASE_PATH . '/app/models/Shift.php';
require_once BASE_PATH . '/app/models/Location.php';

class ShiftController {

    public function index() {
        Auth::require('shifts', 'view');
        $shiftModel = new Shift();
        $filterLoc  = (int)($_GET['location_id'] ?? 0);
        $shifts = $shiftModel->getAll($filterLoc ?: null);
        // Attach hour slots to each shift
        foreach ($shifts as &$s) {
            $s['slots'] = $shiftModel->getHourSlots($s['id']);
        }
        unset($s);
        $locations = (new Location())->getAll(true);
        $activePage = 'shifts';
        require_once BASE_PATH . '/app/views/shifts/index.php';
    }

    public function create() {
        Auth::require('shifts', 'add');
        $error = '';
        $shift = null;
        $locations = (new Location())->getAll(true);
        $activePage = 'shifts';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $shiftName  = trim($_POST['shift_name'] ?? '');
            $hours      = (int)($_POST['hours'] ?? 0);
            $locationId = (int)($_POST['location_id'] ?? 0);

            if (empty($shiftName) || !in_array($hours, Shift::HOUR_OPTIONS) || !$locationId) {
                $error = 'Location, shift name, and valid hours (8–12) are required.';
            } else {
                $newId = (new Shift())->create($shiftName, $hours, $locationId);
                AuditLog::log('create', 'shifts', $newId, [
                    'shift_name'  => $shiftName,
                    'hours'       => $hours,
                    'location_id' => $locationId,
                ]);
                Flash::success("Shift \"{$shiftName}\" created. Now define the hour slots.");
                header('Location: ' . BASE_URL . 'shifts/hours?id=' . $newId);
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/shifts/form.php';
    }

    public function edit() {
        Auth::require('shifts', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $shiftModel = new Shift();
        $shift = $shiftModel->find($id);
        if (!$shift) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $locations = (new Location())->getAll(true);
        $activePage = 'shifts';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $shiftName  = trim($_POST['shift_name'] ?? '');
            $hours      = (int)($_POST['hours'] ?? 0);
            $locationId = (int)($_POST['location_id'] ?? 0);

            if (empty($shiftName) || !in_array($hours, Shift::HOUR_OPTIONS) || !$locationId) {
                $error = 'Location, shift name, and valid hours (8–12) are required.';
            } else {
                $shiftModel->update($id, $shiftName, $hours, $locationId);
                AuditLog::log('update', 'shifts', $id, [
                    'shift_name'  => $shiftName,
                    'hours'       => $hours,
                    'location_id' => $locationId,
                ]);
                Flash::success("Shift \"{$shiftName}\" updated.");
                header('Location: ' . BASE_URL . 'shifts/hours?id=' . $id);
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/shifts/form.php';
    }

    public function hours() {
        Auth::require('shifts', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $shiftModel = new Shift();
        $shift = $shiftModel->find($id);
        if (!$shift) { http_response_code(404); echo "Not found."; return; }

        $slots   = $shiftModel->getHourSlots($id);
        $error   = '';
        $success = '';
        $activePage = 'shifts';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $submitted = $_POST['slots'] ?? [];
            $filled = array_filter($submitted, fn($s) => !empty($s['start_time']) && !empty($s['end_time']));

            if (count($filled) < $shift['hours']) {
                $error = 'Please fill in all ' . $shift['hours'] . ' hour slots.';
            } else {
                $shiftModel->saveHourSlots($id, $submitted);
                AuditLog::log('update', 'shift_hours', $id, [
                    'shift_name' => $shift['shift_name'],
                    'slot_count' => count($filled),
                ]);
                $success = 'Hour slots saved successfully.';
                $slots   = $shiftModel->getHourSlots($id);
            }
        }

        require_once BASE_PATH . '/app/views/shifts/hours.php';
    }

    public function delete() {
        Auth::require('shifts', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $shiftModel = new Shift();
        $deleted = $shiftModel->find($id);
        $shiftModel->delete($id);
        AuditLog::log('delete', 'shifts', $id, $deleted ? ['shift_name' => $deleted['shift_name']] : null);
        Flash::success($deleted ? "Shift \"{$deleted['shift_name']}\" deleted." : 'Shift deleted.');
        header('Location: ' . BASE_URL . 'shifts/index');
        exit;
    }
}
