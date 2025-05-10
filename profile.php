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
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu hiện tại
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
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
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
<body>
    <div class="profile-container">
        <a href="admin_dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 