<?php

class Sort {

    public static function pick($default, $allowed) {
        $sort = $_GET['sort'] ?? $default;
        if (!in_array($sort, $allowed, true)) {
            $sort = $default;
        }
        $dir = strtolower($_GET['dir'] ?? 'asc');
        if ($dir !== 'desc') $dir = 'asc';
        return [$sort, $dir];
    }

    public static function header($label, $col, $currentCol, $currentDir, $extraQs = []) {
        $nextDir = ($currentCol === $col && $currentDir === 'asc') ? 'desc' : 'asc';
        $qs = array_merge($extraQs, ['sort' => $col, 'dir' => $nextDir]);
        $url = '?' . http_build_query($qs);
        $arrow = '';
        if ($currentCol === $col) {
            $arrow = $currentDir === 'asc'
                ? ' <i class="bi bi-caret-up-fill" style="font-size:10px"></i>'
                : ' <i class="bi bi-caret-down-fill" style="font-size:10px"></i>';
        } else {
            $arrow = ' <i class="bi bi-arrow-down-up" style="font-size:10px;color:#c1c8d0"></i>';
        }
        return '<a href="' . htmlspecialchars($url) . '" style="color:inherit;text-decoration:none;">'
             . htmlspecialchars($label) . $arrow . '</a>';
    }
}
