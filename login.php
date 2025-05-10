<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Mã hóa mật khẩu bằng MD5

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        // Check if the student account is approved
        if (!$user['is_approved'] && $user['role'] === 'student') {
            $message = '<div class="alert alert-warning">Tài khoản của bạn đang chờ được duyệt. Vui lòng thử lại sau!</div>';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit;
        }
    } else {
        $message = '<div class="alert alert-danger">Tên đăng nhập hoặc mật khẩu không đúng!</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(9,14,102,0.08);
            padding: 40px 30px;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: var(--primary-blue);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px 0 10px 0;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-label {
            color: var(--primary-blue);
            font-weight: 500;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(9, 14, 102, 0.15);
        }
        .btn-primary {
            background: var(--primary-blue);
            border: none;
            border-radius: 5px;
            font-weight: 600;
            padding: 10px 0;
        }
        .btn-primary:hover {
            background: var(--hover-blue);
        }
        .login-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .register-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .register-link a:hover {
            color: var(--hover-blue);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2>Đăng nhập</h2>
        </div>
        <?php echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
            </button>
        </form>
        <div class="register-link">
            <p class="mb-0">Chưa có tài khoản? <a href="register.php"><i class="fas fa-user-plus me-1"></i>Đăng ký ngay</a></p>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>