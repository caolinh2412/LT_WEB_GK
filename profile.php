<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT username, full_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Xử lý đổi mật khẩu
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = md5($_POST['current_password']); // Mã hóa mật khẩu hiện tại
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu hiện tại
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    if ($current_password === $user_data['password']) {
        if ($new_password === $confirm_password) {
            $hashed_password = md5($new_password); // Mã hóa mật khẩu mới
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            $message = '<div class="alert alert-success">Mật khẩu đã được cập nhật thành công!</div>';
        } else {
            $message = '<div class="alert alert-danger">Mật khẩu mới không khớp!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Mật khẩu hiện tại không đúng!</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thông tin cá nhân</title>
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

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--primary-blue);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: var(--hover-blue);
            color: white;
            transform: translateX(-3px);
        }

        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .profile-avatar i {
            font-size: 50px;
            color: var(--primary-blue);
        }

        .profile-info {
            background: var(--light-blue);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }

        .info-item i {
            width: 40px;
            color: var(--primary-blue);
            font-size: 1.2rem;
        }

        .password-form {
            background: var(--light-blue);
            padding: 25px;
            border-radius: 10px;
        }

        .form-control {
            border: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: var(--primary-blue);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--hover-blue);
            transform: translateY(-2px);
        }

        .section-title {
            color: var(--primary-blue);
            margin-bottom: 20px;
            font-weight: 600;
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
                <a class="nav-link active" href="profile.php">
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
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="section-title">Thông tin cá nhân</h2>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <i class="fas fa-user-circle"></i>
                    <div class="ms-3">
                        <small class="text-muted">Tên đăng nhập</small>
                        <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <div class="ms-3">
                        <small class="text-muted">Họ và tên</small>
                        <div class="fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    </div>
                </div>
            </div>

            <div class="password-form">
                <h3 class="section-title">Đổi mật khẩu</h3>
                <?php echo $message; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="password" class="form-control" name="current_password" placeholder="Mật khẩu hiện tại" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="new_password" placeholder="Mật khẩu mới" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-key me-2"></i>Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 