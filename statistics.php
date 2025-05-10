<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thống kê truy cập</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #090e66;
            --light-blue: #e8e9ff;
            --hover-blue: #070b4d;
        }
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .page-header {
            background: var(--primary-blue);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background: var(--primary-blue);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .back-link {
            color: var(--primary-blue);
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        .back-link:hover {
            color: var(--hover-blue);
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: var(--primary-blue);
            color: white;
            border: none;
        }
        .table td {
            vertical-align: middle;
        }
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-right: 15px;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-blue);
        }
        .stat-label {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <div class="page-header">
            <h2><i class="fas fa-chart-bar me-2"></i>Thống kê truy cập</h2>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-users stat-icon"></i>
                        <div>
                            <div class="stat-label">Tổng số lượt truy cập</div>
                            <div class="stat-value">
                                <?php
                                $total_visits = $pdo->query("SELECT COUNT(*) as count FROM visits")->fetch()['count'];
                                echo $total_visits;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Lượt truy cập theo người dùng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Tên người dùng</th><th>Số lượt truy cập</th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $stats = $pdo->query("SELECT u.full_name, COUNT(v.id) as visit_count FROM visits v JOIN users u ON v.user_id = u.id GROUP BY u.id")->fetchAll();
                        foreach ($stats as $stat) {
                            echo "<tr><td>{$stat['full_name']}</td><td>{$stat['visit_count']}</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>