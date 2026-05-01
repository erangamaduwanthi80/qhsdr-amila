<?php

require_once BASE_PATH . '/app/config/db.php';

class RateLimit {

    const MAX_ATTEMPTS_PER_USERNAME = 5;
    const MAX_ATTEMPTS_PER_IP       = 15;
    const WINDOW_MINUTES            = 15;
    const LOCKOUT_MINUTES           = 15;

    public static function recordLogin($username, $success) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO login_attempts (username, ip_address, success) VALUES (?,?,?)"
        );
        $stmt->execute([
            $username,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $success ? 1 : 0,
        ]);
    }

    public static function isLockedOut($username) {
        $db = getDB();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // Failed attempts on this username in window
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE username = ? AND success = 0
               AND attempted_at > (NOW() - INTERVAL ? MINUTE)"
        );
        $stmt->execute([$username, self::WINDOW_MINUTES]);
        if ((int)$stmt->fetchColumn() >= self::MAX_ATTEMPTS_PER_USERNAME) {
            return true;
        }

        // Failed attempts from this IP in window (across all usernames)
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE ip_address = ? AND success = 0
               AND attempted_at > (NOW() - INTERVAL ? MINUTE)"
        );
        $stmt->execute([$ip, self::WINDOW_MINUTES]);
        if ((int)$stmt->fetchColumn() >= self::MAX_ATTEMPTS_PER_IP) {
            return true;
        }

        return false;
    }

    public static function clearOnSuccess($username) {
        // After successful login, optionally clear failed history for this user
        // We keep records for audit but won't lock the user out anymore on past failures
        // since success counts as fresh.
        $db = getDB();
        $stmt = $db->prepare(
            "DELETE FROM login_attempts
             WHERE username = ? AND success = 0
               AND attempted_at > (NOW() - INTERVAL ? MINUTE)"
        );
        $stmt->execute([$username, self::WINDOW_MINUTES]);
    }

    public static function minutesRemaining($username) {
        $db = getDB();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = $db->prepare(
            "SELECT TIMESTAMPDIFF(MINUTE, NOW(), MAX(attempted_at) + INTERVAL ? MINUTE)
             FROM login_attempts
             WHERE (username = ? OR ip_address = ?) AND success = 0
               AND attempted_at > (NOW() - INTERVAL ? MINUTE)"
        );
        $stmt->execute([self::WINDOW_MINUTES, $username, $ip, self::WINDOW_MINUTES]);
        $mins = (int)$stmt->fetchColumn();
        return max(1, $mins);
    }
}
