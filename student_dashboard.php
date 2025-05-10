<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
// Lấy username từ bảng users nếu chưa có trong session
if (empty($_SESSION['username'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    $_SESSION['username'] = $row ? $row['username'] : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trang chính - Sinh viên</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #090e66;
            --light-blue: #e8e9ff;
            --hover-blue: #070b4d;
            --font-primary: 'Montserrat', sans-serif;
            --font-heading: 'Playfair Display', serif;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: var(--font-primary);
        }

        .navbar {
            background-color: var(--primary-blue);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.6rem 1rem;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 0.4rem 0;
            font-family: var(--font-heading);
        }

        .navbar-collapse {
            justify-content: center;
        }

        .navbar-nav {
            margin: 0 auto;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
            font-weight: 500;
            border-radius: 12px;
        }

        .nav-link:hover {
            color: white !important;
            background-color: var(--hover-blue);
            border-radius: 5px;
        }

        .nav-link.active {
            background-color: var(--hover-blue);
            border-radius: 5px;
        }

        .search-form {
            position: relative;
            width: 250px;
            margin: 0 1.2rem;
        }

        .search-input {
            padding: 0.4rem 2.2rem 0.4rem 0.8rem;
            border-radius: 30px;
            border: none;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.9rem;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input:focus {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: none;
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            padding: 0.5rem;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-dropdown .dropdown-toggle {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .user-dropdown .dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 20px;
            padding: 0.5rem;
        }

        .user-dropdown .dropdown-item {
            padding: 0.7rem 1rem;
            border-radius: 15px;
            margin: 0.2rem 0;
        }

        .user-dropdown .dropdown-item:hover {
            background-color: var(--light-blue);
        }

        .main-content {
            padding: 20px;
            margin-top: 20px;
        }

        .student-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .announcement-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-blue);
        }

        .section-title {
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-family: var(--font-heading);
        }

        /* Carousel Styles */
        #mainCarousel {
            width: 100%;
            height: 400px;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        .carousel-inner {
            width: 100%;
            height: 100%;
        }

        .carousel-item {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
            z-index: 1;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.9);
            transition: transform 0.5s ease;
        }

        .carousel-item:hover img {
            transform: scale(1.05);
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            text-align: left;
            z-index: 2;
        }

        .carousel-caption h3 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: none;
            letter-spacing: 0.5px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            line-height: 1.3;
        }

        .carousel-caption p {
            font-family: var(--font-primary);
            font-size: 1.1rem;
            font-weight: 300;
            line-height: 1.8;
            color: rgba(255,255,255,0.95);
            max-width: 600px;
            margin-bottom: 0;
            letter-spacing: 0.3px;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #mainCarousel:hover .carousel-control-prev,
        #mainCarousel:hover .carousel-control-next {
            opacity: 0.8;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(9, 14, 102, 0.5);
            border-radius: 50%;
            background-size: 50%;
        }

        .carousel-indicators {
            margin-bottom: 1.5rem;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 6px;
            background-color: rgba(255,255,255,0.5);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .carousel-indicators button.active {
            background-color: var(--primary-blue);
            transform: scale(1.2);
        }

        .btn {
            border-radius: 12px;
        }

        .btn-primary {
            border-radius: 12px;
            padding: 0.5rem 1.2rem;
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background-color: var(--hover-blue);
            border-color: var(--hover-blue);
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .modal-header {
            border-radius: 20px 20px 0 0;
            background-color: var(--primary-blue);
            padding: 1.5rem;
            border: none;
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal .btn-close {
            color: white;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .modal .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .profile-info {
            background: var(--light-blue);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .profile-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: white;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .profile-info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .profile-info-item:last-child {
            margin-bottom: 0;
        }

        .profile-info-item i {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-blue);
            color: white;
            border-radius: 10px;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .profile-info-item .info-label {
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.2rem;
        }

        .profile-info-item .info-value {
            color: #666;
            font-size: 0.95rem;
        }

        .settings-form .form-group {
            margin-bottom: 1.5rem;
        }

        .settings-form label {
            font-weight: 500;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }

        .settings-form .form-control {
            border: 2px solid #eee;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .settings-form .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(9, 14, 102, 0.1);
        }

        .settings-form .btn-primary {
            width: 100%;
            padding: 0.8rem;
            font-weight: 500;
            margin-top: 1rem;
        }

        .settings-form .form-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Hệ thống học tập
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-home" href="#" onclick="showTab('home', event)">
                            <i class="fas fa-home me-2"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-subjects" href="#" onclick="showTab('subjects', event)">
                            <i class="fas fa-book me-2"></i>Môn học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-announcements" href="#" onclick="showTab('announcements', event)">
                            <i class="fas fa-bullhorn me-2"></i>Thông báo
                        </a>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="search-form">
                    <input type="text" class="form-control search-input" placeholder="Tìm kiếm...">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo $_SESSION['full_name']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="fas fa-user me-2"></i>Thông tin cá nhân
                        </a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog me-2"></i>Cài đặt
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Carousel Slider -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./img/Anh1.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h3>Kho Tài Liệu Học Tập Phong Phú</h3>
                    <p>Khám phá bộ sưu tập tài liệu đa dạng, được biên soạn bởi đội ngũ giảng viên giàu kinh nghiệm</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./img/Anh2.jpg" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h3>Tài Liệu Được Cập Nhật Thường Xuyên</h3>
                    <p>Luôn cập nhật những kiến thức mới nhất, phù hợp với chương trình học hiện đại</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./img/Anh3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h3>Học Tập Mọi Lúc, Mọi Nơi</h3>
                    <p>Truy cập tài liệu học tập dễ dàng, thuận tiện trên mọi thiết bị</p>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Main Content -->
    <div class="container main-content">
        <div id="home-section">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="section-title"><i class="fas fa-book me-2"></i>Danh sách môn học</h2>
                    <div class="row">
                        <?php
                        $subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();
                        foreach ($subjects as $subject) {
                            echo '<div class="col-md-4 mb-4">
                                <div class="card student-card">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-graduation-cap me-2"></i>' . $subject['subject_name'] . '</h5>';
                            if (!empty($subject['description'])) {
                                echo '<p class="text-muted">' . htmlspecialchars($subject['description']) . '</p>';
                            }
                            echo '<a href="documents.php?subject_id=' . $subject['id'] . '" class="btn btn-primary">
                                            <i class="fas fa-folder-open me-1"></i>Xem tài liệu
                                        </a>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-12">
                    <h2 class="section-title"><i class="fas fa-bullhorn me-2"></i>Thông báo</h2>
                    <?php
                    $announcements = $pdo->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.posted_by = u.id ORDER BY a.posted_at DESC")->fetchAll();
                    foreach ($announcements as $ann) {
                        echo '<div class="card announcement-card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-newspaper me-2"></i>' . $ann['title'] . '</h5>
                                <p class="card-text">' . $ann['content'] . '</p>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-user me-1"></i>Đăng bởi: ' . $ann['full_name'] . ' | 
                                        <i class="fas fa-clock me-1"></i>' . $ann['posted_at'] . '
                                    </small>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div id="subjects-section" style="display:none;">
            <h2 class="section-title"><i class="fas fa-book me-2"></i>Danh sách môn học</h2>
            <div class="row">
                <?php
                $subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();
                foreach ($subjects as $subject) {
                    echo '<div class="col-md-4 mb-4">
                        <div class="card student-card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-graduation-cap me-2"></i>' . $subject['subject_name'] . '</h5>';
                    if (!empty($subject['description'])) {
                        echo '<p class="text-muted">' . htmlspecialchars($subject['description']) . '</p>';
                    }
                    echo '<a href="documents.php?subject_id=' . $subject['id'] . '" class="btn btn-primary">
                                        <i class="fas fa-folder-open me-1"></i>Xem tài liệu
                                    </a>
                                </div>
                            </div>
                        </div>';
                }
                ?>
            </div>
        </div>
        <div id="announcements-section" style="display:none;">
            <h2 class="section-title"><i class="fas fa-bullhorn me-2"></i>Thông báo</h2>
            <?php
            $announcements = $pdo->query("SELECT a.*, u.full_name FROM announcements a JOIN users u ON a.posted_by = u.id ORDER BY a.posted_at DESC")->fetchAll();
            foreach ($announcements as $ann) {
                echo '<div class="card announcement-card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-newspaper me-2"></i>' . $ann['title'] . '</h5>
                        <p class="card-text">' . $ann['content'] . '</p>
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-user me-1"></i>Đăng bởi: ' . $ann['full_name'] . ' | 
                                <i class="fas fa-clock me-1"></i>' . $ann['posted_at'] . '
                            </small>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showTab(tab, e) {
        e.preventDefault();
        document.getElementById('home-section').style.display = (tab === 'home') ? '' : 'none';
        document.getElementById('subjects-section').style.display = (tab === 'subjects') ? '' : 'none';
        document.getElementById('announcements-section').style.display = (tab === 'announcements') ? '' : 'none';
        document.getElementById('tab-home').classList.toggle('active', tab === 'home');
        document.getElementById('tab-subjects').classList.toggle('active', tab === 'subjects');
        document.getElementById('tab-announcements').classList.toggle('active', tab === 'announcements');
    }
    // Mặc định hiển thị tab trang chủ
    showTab('home', {preventDefault: function(){}});
    </script>

    <!-- Modal: Thông tin cá nhân -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="profileModalLabel">
              <i class="fas fa-user me-2"></i>Thông tin cá nhân
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="profile-info">
              <div class="profile-info-item">
                <i class="fas fa-user"></i>
                <div>
                  <div class="info-label">Họ tên</div>
                  <div class="info-value"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                </div>
              </div>
              <div class="profile-info-item">
                <i class="fas fa-id-card"></i>
                <div>
                  <div class="info-label">Tên đăng nhập</div>
                  <div class="info-value"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
                </div>
              </div>
              <div class="profile-info-item">
                <i class="fas fa-user-tag"></i>
                <div>
                  <div class="info-label">Vai trò</div>
                  <div class="info-value">Sinh viên</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Cài đặt -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="settingsModalLabel">
              <i class="fas fa-cog me-2"></i>Cài đặt tài khoản
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="change_password.php" class="settings-form">
              <div class="form-group">
                <label for="current_password">
                  <i class="fas fa-lock me-2"></i>Mật khẩu hiện tại
                </label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="new_password">
                  <i class="fas fa-key me-2"></i>Mật khẩu mới
                </label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="confirm_password">
                  <i class="fas fa-check-circle me-2"></i>Nhập lại mật khẩu mới
                </label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Đổi mật khẩu
              </button>
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>Đổi mật khẩu để bảo vệ tài khoản của bạn.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</body>
</html> 