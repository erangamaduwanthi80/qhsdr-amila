<?php

require_once BASE_PATH . '/app/models/User.php';

class AuthController {

    public function index() {
        $this->login();
    }

    public function login() {
        // Already logged in
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'dashboard/index');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::validate();
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } elseif (RateLimit::isLockedOut($username)) {
                $mins = RateLimit::minutesRemaining($username);
                $error = "Too many failed login attempts. Try again in about {$mins} minute(s).";
                AuditLog::log('login_blocked', 'users', null, ['username' => $username]);
            } else {
                $userModel = new User();
                $user = $userModel->findByUsername($username);

                if ($user && $userModel->verifyPassword($password, $user['password'])) {
                    RateLimit::recordLogin($username, true);
                    RateLimit::clearOnSuccess($username);
                    $_SESSION['user'] = [
                        'id'            => $user['id'],
                        'username'      => $user['username'],
                        'name'          => $user['name'],
                        'role'          => $user['role'],
                        'location_id'   => !empty($user['location_id']) ? (int)$user['location_id'] : null,
                        'location_code' => $user['location_code'] ?? null,
                        'location_name' => $user['location_name'] ?? null,
                    ];
                    AuditLog::log('login', 'users', $user['id']);
                    header('Location: ' . BASE_URL . 'dashboard/index');
                    exit;
                } else {
                    RateLimit::recordLogin($username, false);
                    AuditLog::log('login_failed', 'users', null, ['username' => $username]);
                    $error = 'Invalid username or password.';
                }
            }
        }

        require_once BASE_PATH . '/app/views/auth/login.php';
    }

    public function logout() {
        if (!empty($_SESSION['user'])) {
            AuditLog::log('logout', 'users', $_SESSION['user']['id']);
        }
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}
