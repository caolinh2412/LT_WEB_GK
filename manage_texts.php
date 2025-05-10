<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $pdo->prepare("INSERT INTO texts (title, content, created_by) VALUES (?, ?, ?)")->execute([$title, $content, $_SESSION['user_id']]);
    } elseif (isset($_POST['delete'])) {
        $text_id = $_POST['text_id'];
        $pdo->prepare("DELETE FROM texts WHERE id = ?")->execute([$text_id]);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Soạn văn bản</title>
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

        .back-link {
            color: var(--primary-blue);
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }

        .back-link:hover {
            color: var(--hover-blue);
        }

        .text-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-blue);
            transition: transform 0.3s;
        }

        .text-card:hover {
            transform: translateY(-2px);
        }

        .text-title {
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .text-content {
            color: #666;
            margin-bottom: 15px;
        }

        .delete-btn {
            color: #dc3545;
            background: none;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .delete-btn:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>

        <div class="page-header">
            <h2><i class="fas fa-edit me-2"></i>Soạn văn bản</h2>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm văn bản mới</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề</label>
                                <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề văn bản" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nội dung</label>
                                <textarea name="content" class="form-control" rows="5" placeholder="Nhập nội dung văn bản" required></textarea>
                            </div>
                            <button type="submit" name="add" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Lưu văn bản
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách văn bản</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $texts = $pdo->query("SELECT t.*, u.full_name FROM texts t JOIN users u ON t.created_by = u.id ORDER BY t.created_at DESC")->fetchAll();
                        foreach ($texts as $text) {
                            echo "<div class='text-card'>
                                <div class='d-flex justify-content-between align-items-start'>
                                    <h5 class='text-title'><i class='fas fa-file-alt me-2'></i>{$text['title']}</h5>
                                    <form method='POST' style='display:inline'>
                                        <input type='hidden' name='text_id' value='{$text['id']}'>
                                        <button type='submit' name='delete' class='delete-btn' onclick='return confirm(\"Bạn có chắc muốn xóa văn bản này?\")'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </form>
                                </div>
                                <div class='text-content'>{$text['content']}</div>
                                <div class='announcement-meta'>
                                    <i class='fas fa-user me-1'></i>Người tạo: {$text['full_name']} | 
                                    <i class='fas fa-clock me-1'></i>{$text['created_at']}
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>