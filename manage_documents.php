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
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>

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

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>