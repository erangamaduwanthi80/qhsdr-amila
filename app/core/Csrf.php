<?php

class Csrf {

    public static function token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field() {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function check() {
        $submitted = $_POST['csrf_token'] ?? '';
        $expected  = $_SESSION['csrf_token'] ?? '';
        return !empty($expected) && hash_equals($expected, $submitted);
    }

    public static function validate() {
        if (!self::check()) {
            http_response_code(419);
            $deniedReason = 'csrf';
            require_once BASE_PATH . '/app/views/errors/csrf.php';
            exit;
        }
    }
}
