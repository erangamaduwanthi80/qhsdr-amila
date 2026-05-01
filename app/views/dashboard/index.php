<?php require_once BASE_PATH . '/app/views/layout/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layout/sidebar.php'; ?>

<?php
$kpi = $chartData['kpi'];

$mtLabels = array_column($chartData['byMachineType'], 'machine_type');
$mtData   = array_column($chartData['byMachineType'], 'total');

$locLabels = array_column($chartData['byLocation'], 'location_name');
$locData   = array_column($chartData['byLocation'], 'total');

$dLabels  = array_column($chartData['byDefect'], 'defect_name');
$dData    = array_column($chartData['byDefect'], 'total');

$dateLabels = array_column($chartData['byDate'], 'feed_date');
$dateData   = array_column($chartData['byDate'], 'total');

// Shift grouped bar
$shiftRows   = $chartData['byShift'];
$shiftNames  = array_unique(array_column($shiftRows, 'shift_name'));
$typeNames   = array_unique(array_column($shiftRows, 'machine_type'));
$shiftMatrix = [];
foreach ($shiftRows as $r) {
    $shiftMatrix[$r['shift_name']][$r['machine_type']] = (int)$r['total'];
}
$shiftColors = ['#4ea8de','#e67e22','#2ecc71','#9b59b6','#e74c3c'];
$shiftDatasets = [];
foreach ($shiftNames as $idx => $sn) {
    $shiftDatasets[] = [
        'label' => $sn,
        'data'  => array_map(fn($t) => $shiftMatrix[$sn][$t] ?? 0, $typeNames),
        'backgroundColor' => $shiftColors[$idx % count($shiftColors)],
    ];
}
?>

<div class="main-content">
    <div class="topbar">
        <h1 class="page-title">Dashboard</h1>
        <div class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></div>
    </div>

    <!-- Filters -->
    <form class="d-flex gap-2 mb-4 align-items-end flex-wrap" method="GET">
        <div>
            <label class="form-label fw-semibold mb-1" style="font-size:13px">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>
        <div>
            <label class="form-label fw-semibold mb-1" style="font-size:13px">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($dateTo) ?>">
        </div>
        <div>
            <label class="form-label fw-semibold mb-1" style="font-size:13px">Location</label>
            <select name="location_id" class="form-select form-select-sm">
                <option value="">All Locations</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>" <?= $locationId === (int)$loc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($loc['code'] . ' — ' . $loc['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
            <a href="<?= BASE_URL ?>dashboard/index" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>

    <!-- KPI cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div style="font-size:13px;color:#8a9bb0">Total Defects</div>
                <div style="font-size:32px;font-weight:700;color:#1e2a38"><?= number_format((int)($kpi['total_defects'] ?? 0)) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div style="font-size:13px;color:#8a9bb0">Top Defective Machine Type</div>
                <div style="font-size:18px;font-weight:600;color:#1e2a38"><?= htmlspecialchars($kpi['top_machine'] ?? '—') ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div style="font-size:13px;color:#8a9bb0">Top Defect Type</div>
                <div style="font-size:18px;font-weight:600;color:#1e2a38"><?= htmlspecialchars($kpi['top_defect'] ?? '—') ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div style="font-size:13px;color:#8a9bb0">Top Defective Location</div>
                <div style="font-size:18px;font-weight:600;color:#1e2a38"><?= htmlspecialchars($kpi['top_location'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <!-- Charts row 1 -->
    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Defects by Machine Type</div>
                <div class="card-body"><canvas id="chartMachineType" height="220"></canvas></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Defect Distribution by Type</div>
                <div class="card-body"><canvas id="chartDefectPie" height="220"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Charts row 2 -->
    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Defect Trend Over Time</div>
                <div class="card-body"><canvas id="chartTrend" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Shift-wise Defect Comparison</div>
                <div class="card-body"><canvas id="chartShift" height="200"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Charts row 3 -->
    <div class="row g-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Defects by Location</div>
                <div class="card-body"><canvas id="chartLocation" height="100"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
const colors = ['#4ea8de','#e67e22','#2ecc71','#9b59b6','#e74c3c','#1abc9c','#f39c12'];

new Chart(document.getElementById('chartMachineType'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($mtLabels) ?>,
        datasets: [{
            label: 'Total Defects',
            data: <?= json_encode(array_map('intval', $mtData)) ?>,
            backgroundColor: colors,
            borderRadius: 4,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartDefectPie'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($dLabels) ?>,
        datasets: [{ data: <?= json_encode(array_map('intval', $dData)) ?>, backgroundColor: colors }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: {
        labels: <?= json_encode($dateLabels) ?>,
        datasets: [{
            label: 'Defects',
            data: <?= json_encode(array_map('intval', $dateData)) ?>,
            borderColor: '#4ea8de',
            backgroundColor: 'rgba(78,168,222,0.1)',
            tension: 0.3,
            fill: true,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartShift'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_values($typeNames)) ?>,
        datasets: <?= json_encode($shiftDatasets) ?>,
    },
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartLocation'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($locLabels) ?>,
        datasets: [{
            label: 'Total Defects',
            data: <?= json_encode(array_map('intval', $locData)) ?>,
            backgroundColor: colors,
            borderRadius: 4,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
</script>

<?php require_once BASE_PATH . '/app/views/layout/footer.php'; ?>
