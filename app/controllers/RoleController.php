<?php

require_once BASE_PATH . '/app/models/Role.php';

class RoleController {

    public function index() {
        Auth::require('roles', 'view');
        $roleModel = new Role();
        $roles = $roleModel->getAll();
        $activePage = 'roles';
        require_once BASE_PATH . '/app/views/roles/index.php';
    }

    public function create() {
        Auth::require('roles', 'add');
        $error = '';
        $role = null;
        $activePage = 'roles';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $error = 'Role name is required.';
            } else {
                $newId = (new Role())->create($name);
                AuditLog::log('create', 'roles', $newId, ['name' => $name]);
                Flash::success("Role \"{$name}\" created.");
                header('Location: ' . BASE_URL . 'roles/index');
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/roles/form.php';
    }

    public function edit() {
        Auth::require('roles', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $roleModel = new Role();
        $role = $roleModel->find($id);
        if (!$role) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $activePage = 'roles';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $error = 'Role name is required.';
            } else {
                $roleModel->update($id, $name);
                AuditLog::log('update', 'roles', $id, ['name' => $name]);
                Flash::success("Role updated to \"{$name}\".");
                header('Location: ' . BASE_URL . 'roles/index');
                exit;
            }
        }

        require_once BASE_PATH . '/app/views/roles/form.php';
    }

    public function delete() {
        Auth::require('roles', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        $roleModel = new Role();
        $deleted = $roleModel->find($id);
        $roleModel->delete($id);
        AuditLog::log('delete', 'roles', $id, $deleted ? ['name' => $deleted['name']] : null);
        Flash::success($deleted ? "Role \"{$deleted['name']}\" deleted." : 'Role deleted.');
        header('Location: ' . BASE_URL . 'roles/index');
        exit;
    }

    public function permissions() {
        Auth::require('roles', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $roleModel = new Role();
        $role = $roleModel->find($id);
        if (!$role) { http_response_code(404); echo "Not found."; return; }

        $modules = ['users', 'roles', 'audit', 'locations', 'machines', 'shifts', 'defects', 'datafeed', 'dashboard'];
        $activePage = 'roles';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $actions = $_POST['actions'] ?? [];
            $roleModel->savePermissions($id, $modules, $actions);
            AuditLog::log('update', 'role_permissions', $id, [
                'role_name' => $role['name'],
                'actions'   => $actions,
            ]);
            Flash::success("Permissions for \"{$role['name']}\" saved.");
            header('Location: ' . BASE_URL . 'roles/index');
            exit;
        }

        $permissions = $roleModel->getPermissions($id);
        require_once BASE_PATH . '/app/views/roles/permissions.php';
    }
}
