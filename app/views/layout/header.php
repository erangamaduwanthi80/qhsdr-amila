<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QHS Defect Rate Dashboard</title>

    <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: #1e2a38;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            padding-top: 20px;
        }

        .sidebar .brand {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            padding: 10px 20px 20px;
            border-bottom: 1px solid #2e3f52;
            display: block;
        }

        .sidebar .brand span {
            color: #4ea8de;
        }

        .sidebar .nav-link {
            color: #a9b8c8;
            padding: 10px 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #2e3f52;
            border-left: 3px solid #4ea8de;
        }

        .sidebar .nav-section {
            color: #5a6a78;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 20px 6px;
        }

        .main-content {
            margin-left: 240px;
            padding: 30px;
        }

        .topbar {
            background: #fff;
            padding: 12px 24px;
            margin-bottom: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .page-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e2a38;
            margin: 0;
        }

        .topbar .user-info {
            font-size: 14px;
            color: #5a6a78;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            font-size: 15px;
            color: #1e2a38;
            padding: 14px 20px;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-primary {
            background-color: #4ea8de;
            border-color: #4ea8de;
        }

        .btn-primary:hover {
            background-color: #3a95cb;
            border-color: #3a95cb;
        }

        .table thead th {
            background-color: #f0f4f8;
            color: #1e2a38;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-role {
            background-color: #e8f4fd;
            color: #4ea8de;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
