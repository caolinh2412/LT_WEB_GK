<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Xử lý các hành động với tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET is_approved = TRUE WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif ($action === 'disable') {
        $stmt = $pdo->prepare("UPDATE users SET is_approved = FALSE WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }
}

// Lấy danh sách tài khoản theo trạng thái
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC");
$stmt->execute();
$all_users = $stmt->fetchAll();

// Phân loại tài khoản
$pending_users = array_filter($all_users, function($user) {
    return !$user['is_approved'];
});

$approved_users = array_filter($all_users, function($user) {
    return $user['is_approved'];
});

// Lấy layout từ session
$layout = isset($_SESSION['layout']) ? $_SESSION['layout'] : 'default';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tài khoản</title>
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

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        .user-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .user-avatar i {
            font-size: 1.5rem;
            color: var(--primary-blue);
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-meta {
            color: #666;
            font-size: 0.9rem;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            margin-left: 20px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-approve:hover, .btn-reject:hover {
            opacity: 0.9;
            color: white;
        }

        .section-title {
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .no-users {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            color: #666;
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

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #666;
            padding: 10px 20px;
            font-weight: 500;
            position: relative;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-blue);
            background: none;
            border: none;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-blue);
        }

        .user-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: #ffeeba;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-disabled {
            background: #ffc107;
            color: #856404;
        }

        .tab-content {
            padding-top: 20px;
        }

        .btn-disable {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-disable:hover, .btn-delete:hover {
            opacity: 0.9;
            color: inherit;
        }

        .modal-confirm {
            color: #636363;
        }

        .modal-confirm .modal-content {
            padding: 20px;
            border-radius: 5px;
            border: none;
        }

        .modal-confirm .modal-header {
            border-bottom: none;
            position: relative;
        }

        .modal-confirm h4 {
            text-align: center;
            font-size: 26px;
            margin: 30px 0 -10px;
        }

        .modal-confirm .close {
            position: absolute;
            top: -5px;
            right: -2px;
        }

        .modal-confirm .modal-body {
            color: #999;
        }

        .modal-confirm .modal-footer {
            border: none;
            text-align: center;
            border-radius: 5px;
            font-size: 13px;
            padding: 10px 0 25px;
        }

        .modal-confirm .modal-footer a {
            color: #999;
        }

        .modal-confirm .icon-box {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            z-index: 9;
            text-align: center;
            border: 3px solid #f15e5e;
        }

        .modal-confirm .icon-box i {
            color: #f15e5e;
            font-size: 46px;
            display: inline-block;
            margin-top: 13px;
        }

        .modal-confirm .btn {
            color: #fff;
            border-radius: 4px;
            background: #60c7c1;
            text-decoration: none;
            transition: all 0.4s;
            line-height: normal;
            min-width: 120px;
            border: none;
            min-height: 40px;
            border-radius: 3px;
            margin: 0 5px;
        }

        .modal-confirm .btn-danger {
            background: #f15e5e;
        }

        .modal-confirm .btn-danger:hover {
            background: #ee3535;
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
                <a class="nav-link active" href="manage_users.php">
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
                <a class="nav-link active" href="manage_users.php">
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

    <div class="main-content">
        <h2 class="section-title">
            <i class="fas fa-users me-2"></i>Quản lý tài khoản
        </h2>

        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    Chờ duyệt <span class="badge bg-warning ms-1"><?php echo count($pending_users); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                    Đã duyệt <span class="badge bg-success ms-1"><?php echo count($approved_users); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled" type="button" role="tab">
                    Đã vô hiệu hóa <span class="badge bg-warning ms-1"><?php echo count($pending_users); ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="userTabsContent">
            <!-- Tab Chờ duyệt -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <?php if (empty($pending_users)): ?>
                    <div class="no-users">
                        <i class="fas fa-check-circle mb-3" style="font-size: 3rem; color: #28a745;"></i>
                        <h4>Không có tài khoản nào đang chờ duyệt</h4>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_users as $user): ?>
                        <div class="user-card">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                        <span class="user-status status-pending ms-2">Chờ duyệt</span>
                                    </div>
                                    <div class="user-meta">
                                        <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($user['username']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="user-actions">
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-approve">
                                        <i class="fas fa-check me-1"></i>Duyệt
                                    </button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-reject" onclick="return confirm('Bạn có chắc chắn muốn từ chối tài khoản này?')">
                                        <i class="fas fa-times me-1"></i>Từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab Đã duyệt -->
            <div class="tab-pane fade" id="approved" role="tabpanel">
                <?php if (empty($approved_users)): ?>
                    <div class="no-users">
                        <i class="fas fa-users mb-3" style="font-size: 3rem; color: #666;"></i>
                        <h4>Chưa có tài khoản nào được duyệt</h4>
                    </div>
                <?php else: ?>
                    <?php foreach ($approved_users as $user): ?>
                        <div class="user-card">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                        <span class="user-status status-approved ms-2">Đã duyệt</span>
                                    </div>
                                    <div class="user-meta">
                                        <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($user['username']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="user-actions">
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="disable">
                                    <button type="submit" class="btn-disable" onclick="return confirm('Bạn có chắc chắn muốn vô hiệu hóa tài khoản này?')">
                                        <i class="fas fa-ban me-1"></i>Vô hiệu hóa
                                    </button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        <i class="fas fa-trash me-1"></i>Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab Đã vô hiệu hóa -->
            <div class="tab-pane fade" id="disabled" role="tabpanel">
                <?php if (empty($pending_users)): ?>
                    <div class="no-users">
                        <i class="fas fa-ban mb-3" style="font-size: 3rem; color: #666;"></i>
                        <h4>Chưa có tài khoản nào bị vô hiệu hóa</h4>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_users as $user): ?>
                        <div class="user-card">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($user['full_name']); ?>
                                        <span class="user-status status-disabled ms-2">Đã vô hiệu hóa</span>
                                    </div>
                                    <div class="user-meta">
                                        <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($user['username']); ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="user-actions">
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-approve">
                                        <i class="fas fa-check me-1"></i>Kích hoạt lại
                                    </button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        <i class="fas fa-trash me-1"></i>Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 