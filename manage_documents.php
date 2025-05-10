<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $subject_id = $_POST['subject_id'];
        $title = $_POST['title'];
        $file = $_FILES['file'];
        $file_path = 'uploads/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $file_path);

        $pdo->prepare("INSERT INTO documents (subject_id, title, file_path, uploaded_by) VALUES (?, ?, ?, ?)")->execute([$subject_id, $title, $file_path, $_SESSION['user_id']]);
    } elseif (isset($_POST['delete'])) {
        $doc_id = $_POST['doc_id'];
        $pdo->prepare("DELETE FROM documents WHERE id = ?")->execute([$doc_id]);
    } elseif (isset($_POST['edit'])) {
        $doc_id = $_POST['doc_id'];
        $title = $_POST['title'];
        $pdo->prepare("UPDATE documents SET title = ? WHERE id = ?")->execute([$title, $doc_id]);
    } elseif (isset($_POST['add_subject'])) {
        $new_subject = trim($_POST['new_subject']);
        $description = trim($_POST['description']);
        if ($new_subject !== '') {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
            $stmt->execute([$new_subject, $description]);
            header("Location: manage_documents.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tài liệu</title>
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

        .btn-primary {
            background: var(--primary-blue);
            border: none;
        }

        .btn-primary:hover {
            background: var(--hover-blue);
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(9, 14, 102, 0.25);
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

        .action-buttons .btn {
            margin: 0 5px;
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

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .custom-file-label {
            display: inline-block;
            padding: 8px 16px;
            background: var(--light-blue);
            border-radius: 5px;
            cursor: pointer;
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
                <a class="nav-link active" href="manage_documents.php">
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
        <div class="container">
            <div class="page-header">
                <h2><i class="fas fa-file-alt me-2"></i>Quản lý tài liệu</h2>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm môn học mới</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Tên môn học</label>
                                    <input type="text" name="new_subject" class="form-control" placeholder="Nhập tên môn học" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control" placeholder="Nhập mô tả môn học" rows="2"></textarea>
                                </div>
                                <button type="submit" name="add_subject" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Thêm môn học
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm tài liệu mới</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Môn học</label>
                                    <select name="subject_id" class="form-select" required>
                                        <?php
                                        $subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();
                                        foreach ($subjects as $subject) {
                                            echo "<option value='{$subject['id']}'>{$subject['subject_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tiêu đề</label>
                                    <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">File tài liệu</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" name="file" class="form-control" required>
                                        <span class="custom-file-label">Chọn file</span>
                                    </div>
                                </div>
                                <button type="submit" name="add" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-2"></i>Thêm tài liệu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách tài liệu</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề</th>
                                            <th>Môn học</th>
                                            <th>File</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $documents = $pdo->query("SELECT d.*, s.subject_name FROM documents d JOIN subjects s ON d.subject_id = s.id")->fetchAll();
                                        foreach ($documents as $doc) {
                                            echo "<tr>
                                                <td>{$doc['title']}</td>
                                                <td>{$doc['subject_name']}</td>
                                                <td>
                                                    <a href='{$doc['file_path']}' download class='btn btn-sm btn-outline-primary'>
                                                        <i class='fas fa-download me-1'></i>Tải xuống
                                                    </a>
                                                </td>
                                                <td class='action-buttons'>
                                                    <button type='button' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editModal{$doc['id']}'>
                                                        <i class='fas fa-edit'></i>
                                                    </button>
                                                    <form method='POST' style='display:inline'>
                                                        <input type='hidden' name='doc_id' value='{$doc['id']}'>
                                                        <button type='submit' name='delete' class='btn btn-sm btn-danger' onclick='return confirm(\"Bạn có chắc muốn xóa tài liệu này?\")'>
                                                            <i class='fas fa-trash'></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>";

                                            // Edit Modal
                                            echo "<div class='modal fade' id='editModal{$doc['id']}' tabindex='-1'>
                                                <div class='modal-dialog'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <h5 class='modal-title'>Sửa tài liệu</h5>
                                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                        </div>
                                                        <div class='modal-body'>
                                                            <form method='POST'>
                                                                <input type='hidden' name='doc_id' value='{$doc['id']}'>
                                                                <div class='mb-3'>
                                                                    <label class='form-label'>Tiêu đề</label>
                                                                    <input type='text' name='title' class='form-control' value='{$doc['title']}' required>
                                                                </div>
                                                                <button type='submit' name='edit' class='btn btn-primary'>Lưu thay đổi</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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