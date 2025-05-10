<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Thống kê thực tế
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$total_subjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$total_documents = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$total_announcements = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();

// Lấy layout từ session, mặc định là 'default'
$layout = isset($_SESSION['layout']) ? $_SESSION['layout'] : 'default';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trang quản lý</title>
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
            --sidebar-width: 250px;
            --icon-sidebar-width: 70px;
        }
        
        body {
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-blue);
            color: white;
            padding: 20px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.5rem;
            margin: 0;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .sidebar-menu .nav-link i {
            width: 25px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }

        /* Navbar Layout */
        .navbar-layout .sidebar {
            display: none;
        }

        .navbar-layout .main-content {
            margin-left: 0;
            padding-top: 80px;
        }

        .navbar-layout .top-navbar {
            display: block;
        }

        /* Icon Only Layout */
        .icon-layout .sidebar {
            width: var(--icon-sidebar-width);
        }

        .icon-layout .sidebar-header h3,
        .icon-layout .nav-link span {
            display: none;
        }

        .icon-layout .main-content {
            margin-left: var(--icon-sidebar-width);
        }

        .icon-layout .nav-link {
            text-align: center;
            padding: 12px 5px;
        }

        .icon-layout .nav-link i {
            width: auto;
            font-size: 1.2rem;
        }

        /* Top Navbar */
        .top-navbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--primary-blue);
            padding: 15px 20px;
            z-index: 1000;
        }

        .top-navbar .nav-link {
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            margin-right: 5px;
        }

        .top-navbar .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Admin Dashboard Styles */
        .admin-dashboard {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .admin-card {
            background: var(--light-blue);
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .admin-card:hover {
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .admin-stat {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-blue);
        }

        .admin-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .section-title {
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="<?php echo $layout; ?>-layout">
    <!-- Top Navbar (for navbar layout) -->
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="text-white mb-0"><i class="fas fa-graduation-cap me-2"></i>Quản lý</h3>
            <div class="nav">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="manage_documents.php">
                    <i class="fas fa-file-alt"></i> Quản lý tài liệu
                </a>
                <a class="nav-link" href="manage_announcements.php">
                    <i class="fas fa-bullhorn"></i> Quản lý thông báo
                </a>
                <a class="nav-link" href="statistics.php">
                    <i class="fas fa-chart-bar"></i> Thống kê
                </a>
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i> Thông tin cá nhân
                </a>
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-graduation-cap me-2"></i>Quản lý</h3>
        </div>
        <div class="sidebar-menu">
            <nav class="nav flex-column">
                <a class="nav-link active" href="#">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
                <a class="nav-link" href="manage_users.php">
                    <i class="fas fa-users"></i> <span>Quản lý tài khoản</span>
                </a>
                <a class="nav-link" href="manage_documents.php">
                    <i class="fas fa-file-alt"></i> <span>Quản lý tài liệu</span>
                </a>
                <a class="nav-link" href="manage_announcements.php">
                    <i class="fas fa-bullhorn"></i> <span>Quản lý thông báo</span>
                </a>
                <a class="nav-link" href="statistics.php">
                    <i class="fas fa-chart-bar"></i> <span>Thống kê</span>
                </a>
                <hr class="text-white-50">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i> <span>Thông tin cá nhân</span>
                </a>
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i> <span>Cài đặt</span>
                </a>
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="admin-dashboard">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card admin-card text-center p-3">
                        <i class="fas fa-users admin-icon"></i>
                        <div class="admin-stat"><?php echo $total_students; ?></div>
                        <div class="text-muted">Tổng số sinh viên</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card admin-card text-center p-3">
                        <i class="fas fa-book admin-icon"></i>
                        <div class="admin-stat"><?php echo $total_subjects; ?></div>
                        <div class="text-muted">Môn học</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card admin-card text-center p-3">
                        <i class="fas fa-file-alt admin-icon"></i>
                        <div class="admin-stat"><?php echo $total_documents; ?></div>
                        <div class="text-muted">Tài liệu</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card admin-card text-center p-3">
                        <i class="fas fa-bullhorn admin-icon"></i>
                        <div class="admin-stat"><?php echo $total_announcements; ?></div>
                        <div class="text-muted">Thông báo</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title"><i class="fas fa-bolt me-2"></i>Thao tác nhanh</h2>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="manage_documents.php" class="card admin-card text-center p-3 text-decoration-none">
                                <i class="fas fa-file-upload admin-icon"></i>
                                <h5>Thêm tài liệu</h5>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="manage_announcements.php" class="card admin-card text-center p-3 text-decoration-none">
                                <i class="fas fa-plus-circle admin-icon"></i>
                                <h5>Tạo thông báo</h5>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="statistics.php" class="card admin-card text-center p-3 text-decoration-none">
                                <i class="fas fa-chart-line admin-icon"></i>
                                <h5>Xem thống kê</h5>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 