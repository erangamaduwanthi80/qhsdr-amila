<?php

class Flash {

    public static function success($message) {
        $_SESSION['flash'][] = ['type' => 'success', 'message' => $message];
    }

    public static function error($message) {
        $_SESSION['flash'][] = ['type' => 'error', 'message' => $message];
    }

    public static function info($message) {
        $_SESSION['flash'][] = ['type' => 'info', 'message' => $message];
    }

    public static function consume() {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        // Backwards-compat: pick up older flash_* keys still in use
        if (!empty($_SESSION['flash_success'])) {
            $messages[] = ['type' => 'success', 'message' => $_SESSION['flash_success']];
            unset($_SESSION['flash_success']);
        }
        if (!empty($_SESSION['flash_error'])) {
            $messages[] = ['type' => 'error', 'message' => $_SESSION['flash_error']];
            unset($_SESSION['flash_error']);
        }
        return $messages;
    }
}
