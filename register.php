<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);

    // Kiểm tra username đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $message = '<div class="alert alert-danger">Tên đăng nhập đã tồn tại!</div>';
    } 
    // Kiểm tra mật khẩu
    elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Mật khẩu xác nhận không khớp!</div>';
    }
    else {
        // Thêm user mới
        $hashed_password = md5($password); // Mã hóa mật khẩu bằng MD5
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, 'student', ?)");
        if ($stmt->execute([$username, $hashed_password, $full_name])) {
            $message = '<div class="alert alert-success">Đăng ký thành công! Vui lòng đợi admin duyệt tài khoản.</div>';
        } else {
            $message = '<div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký tài khoản</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header i {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .form-control {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .btn-register {
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background: var(--hover-blue);
            color: white;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h2>Đăng ký tài khoản</h2>
            <p class="text-muted">Vui lòng điền thông tin để đăng ký</p>
        </div>

        <?php echo $message; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="full_name" placeholder="Họ và tên" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus me-2"></i>Đăng ký
            </button>
        </form>

        <div class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 