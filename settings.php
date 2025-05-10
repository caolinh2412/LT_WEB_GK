<?php
session_start();
require 'config.php';
require_once 'modules/settings_module.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$settingsModule = new SettingsModule($pdo);
$settingsModule->handleSettings();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cài đặt</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .settings-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .layout-option {
            background: var(--light-blue);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .layout-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .layout-option.selected {
            border-color: var(--primary-blue);
            background: white;
        }

        .layout-preview {
            width: 100%;
            height: 200px;
            background: white;
            border-radius: 8px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .layout-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .layout-description {
            color: #666;
            font-size: 0.9rem;
        }

        .btn-save {
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: var(--hover-blue);
            transform: translateY(-2px);
        }

        /* Layout Preview Styles */
        .preview-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            width: 30%;
            height: 100%;
            background: var(--primary-blue);
        }

        .preview-navbar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 20%;
            background: var(--primary-blue);
        }

        .preview-content {
            position: absolute;
            top: 20%;
            left: 30%;
            width: 70%;
            height: 80%;
            background: #f8f9fa;
        }

        .preview-content-full {
            position: absolute;
            top: 20%;
            left: 0;
            width: 100%;
            height: 80%;
            background: #f8f9fa;
        }

        .preview-icon-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            width: 10%;
            height: 100%;
            background: var(--primary-blue);
        }

        .preview-icon-content {
            position: absolute;
            top: 0;
            left: 10%;
            width: 90%;
            height: 100%;
            background: #f8f9fa;
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['layout']) ? $_SESSION['layout'] : 'default'; ?>-layout">
    <!-- Top Navbar (for navbar layout) -->
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="text-white mb-0"><i class="fas fa-graduation-cap me-2"></i>Quản lý</h3>
            <div class="nav">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="manage_users.php">
                    <i class="fas fa-users"></i> Quản lý tài khoản
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
                <a class="nav-link" href="admin_dashboard.php">
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
                <a class="nav-link active" href="settings.php">
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
        <?php $settingsModule->renderSettings(); ?>
    </div>

    <script>
        function selectLayout(layout) {
            // Bỏ chọn tất cả các options
            document.querySelectorAll('.layout-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Chọn option được click
            const selectedOption = document.querySelector(`.layout-option[onclick="selectLayout('${layout}')"]`);
            selectedOption.classList.add('selected');
            
            // Check radio button tương ứng
            document.querySelector(`input[value="${layout}"]`).checked = true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 