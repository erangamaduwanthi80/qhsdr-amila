<?php

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Role.php';
require_once BASE_PATH . '/app/models/Location.php';

class UserController {

    public function index() {
        Auth::require('users', 'view');
        $search     = trim($_GET['search'] ?? '');
        $role       = trim($_GET['role'] ?? '');
        $locationId = (int)($_GET['location_id'] ?? 0);
        list($sort, $dir) = Sort::pick('username', ['username','name','role','location','created_at']);
        $userModel = new User();
        $users     = $userModel->getAll($search, $role, $sort, $dir, $locationId);
        $roles     = (new Role())->getAll();
        $locations = (new Location())->getAll(true);
        $activePage = 'users';
        require_once BASE_PATH . '/app/views/users/index.php';
    }

    public function create() {
        Auth::require('users', 'add');
        $error = '';
        $user  = null;
        $roles = (new Role())->getAll();
        $locations = (new Location())->getAll(true);
        $activePage = 'users';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'username'         => trim($_POST['username'] ?? ''),
                'name'             => trim($_POST['name'] ?? ''),
                'role'             => trim($_POST['role'] ?? ''),
                'location_id'      => (int)($_POST['location_id'] ?? 0),
                'password'         => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
            ];

            $error = $this->validate($data, true);
            if (!$error) {
                $userModel = new User();
                if ($userModel->findByUsername($data['username'])) {
                    $error = 'A user with that username already exists.';
                } else {
                    $newId = $userModel->create($data);
                    AuditLog::log('create', 'users', $newId, [
                        'username'    => $data['username'],
                        'name'        => $data['name'],
                        'role'        => $data['role'],
                        'location_id' => $data['location_id'] ?: null,
                    ]);
                    Flash::success("User {$data['username']} created.");
                    header('Location: ' . BASE_URL . 'users/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/users/form.php';
    }

    public function edit() {
        Auth::require('users', 'edit');
        $id = (int)($_GET['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->find($id);
        if (!$user) { http_response_code(404); echo "Not found."; return; }

        $error = '';
        $roles = (new Role())->getAll();
        $locations = (new Location())->getAll(true);
        $activePage = 'users';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $data = [
                'username'         => trim($_POST['username'] ?? ''),
                'name'             => trim($_POST['name'] ?? ''),
                'role'             => trim($_POST['role'] ?? ''),
                'location_id'      => (int)($_POST['location_id'] ?? 0),
                'password'         => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
            ];

            $error = $this->validate($data, false);
            if (!$error) {
                $existing = $userModel->findByUsername($data['username']);
                if ($existing && (int)$existing['id'] !== $id) {
                    $error = 'A different user already uses that username.';
                } else {
                    $userModel->update($id, $data);
                    AuditLog::log('update', 'users', $id, [
                        'username'        => $data['username'],
                        'name'            => $data['name'],
                        'role'            => $data['role'],
                        'location_id'     => $data['location_id'] ?: null,
                        'password_changed'=> !empty($data['password']),
                    ]);
                    if ((int)$_SESSION['user']['id'] === $id) {
                        $_SESSION['user']['username'] = $data['username'];
                        $_SESSION['user']['name']     = $data['name'];
                        $_SESSION['user']['role']     = $data['role'];
                    }
                    Flash::success("User {$data['username']} updated.");
                    header('Location: ' . BASE_URL . 'users/index');
                    exit;
                }
            }
        }

        require_once BASE_PATH . '/app/views/users/form.php';
    }

    public function delete() {
        Auth::require('users', 'delete');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method not allowed."; exit; }
        Csrf::validate();
        $id = (int)($_POST['id'] ?? 0);
        if ((int)$_SESSION['user']['id'] === $id) {
            Flash::error('You cannot delete your own account.');
            header('Location: ' . BASE_URL . 'users/index');
            exit;
        }
        $userModel = new User();
        $deleted = $userModel->find($id);
        $userModel->delete($id);
        AuditLog::log('delete', 'users', $id, $deleted ? [
            'username' => $deleted['username'],
            'name'     => $deleted['name'],
        ] : null);
        Flash::success($deleted ? "User {$deleted['username']} deleted." : 'User deleted.');
        header('Location: ' . BASE_URL . 'users/index');
        exit;
    }

    private function validate($data, $passwordRequired) {
        if (empty($data['username']) || empty($data['name']) || empty($data['role'])) {
            return 'Username, full name, and role are required.';
        }
        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $data['username'])) {
            return 'Username must be 3–50 characters, letters/digits/underscore/dot/dash only.';
        }
        if ($passwordRequired || !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return 'Password must be at least 6 characters.';
            }
            if ($data['password'] !== $data['password_confirm']) {
                return 'Password and confirmation do not match.';
            }
        }
        return '';
    }
}
