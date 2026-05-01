<?php

require_once BASE_PATH . '/app/models/DataFeed.php';
require_once BASE_PATH . '/app/models/Location.php';

class DashboardController {

    public function index() {
        Auth::require('dashboard', 'view');
        $dateFrom   = $_GET['date_from']   ?? date('Y-m-01');
        $dateTo     = $_GET['date_to']     ?? date('Y-m-d');
        $locationId = (int)($_GET['location_id'] ?? 0);

        $chartData = (new DataFeed())->getChartData($dateFrom, $dateTo, $locationId ?: null);
        $locations = (new Location())->getAll(true);
        $activePage = 'dashboard';
        require_once BASE_PATH . '/app/views/dashboard/index.php';
    }
}
