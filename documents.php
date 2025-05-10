<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$subject_id = $_GET['subject_id'];

// Lấy thông tin môn học
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$subject_id]);
$subject = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$subject) {
    echo "Môn học không tồn tại.";
    exit;
}

// Lấy danh sách tài liệu
$stmt = $pdo->prepare("SELECT d.*, u.full_name FROM documents d JOIN users u ON d.uploaded_by = u.id WHERE d.subject_id = ?");
$stmt->execute([$subject_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tài liệu - <?php echo htmlspecialchars($subject['subject_name']); ?></title>
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
        .back-link {
            color: var(--primary-blue);
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        .back-link:hover {
            color: var(--hover-blue);
        }
        .doc-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            padding: 20px;
            border-left: 4px solid var(--primary-blue);
            transition: transform 0.3s;
        }
        .doc-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(9,14,102,0.08);
        }
        .doc-title {
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 1.2rem;
        }
        .doc-meta {
            color: #888;
            font-size: 0.95rem;
        }
        .download-btn {
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            transition: background 0.2s;
            font-size: 1rem;
        }
        .download-btn:hover {
            background: var(--hover-blue);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="student_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <div class="page-header mb-4">
            <h2><i class="fas fa-book me-2"></i>Tài liệu môn <?php echo htmlspecialchars($subject['subject_name']); ?></h2>
        </div>
        <div class="row">
            <?php if (count($documents) === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info">Chưa có tài liệu nào cho môn học này.</div>
                </div>
            <?php endif; ?>
            <?php foreach ($documents as $doc): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="doc-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="doc-title mb-2">
                                <i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($doc['title']); ?>
                            </div>
                            <div class="doc-meta mb-3">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($doc['full_name']); ?>
                                <span class="mx-2">|</span>
                                <i class="fas fa-clock me-1"></i><?php echo $doc['uploaded_at']; ?>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" download class="download-btn mt-2">
                            <i class="fas fa-download me-1"></i>Tải xuống
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
