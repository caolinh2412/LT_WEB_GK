<?php
class SettingsModule {
    private $pdo;
    private $message;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function handleSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $layout = $_POST['layout'];
            $_SESSION['layout'] = $layout;
            $this->message = '<div class="alert alert-success">Cài đặt đã được lưu thành công!</div>';
        }
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCurrentLayout() {
        return isset($_SESSION['layout']) ? $_SESSION['layout'] : 'default';
    }

    public function renderSettings() {
        $currentLayout = $this->getCurrentLayout();
        ?>
        <div class="settings-container">
            <div class="settings-header d-flex align-items-center mb-4">
                <a href="admin_dashboard.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="section-title mb-0 ms-3">Cài đặt giao diện</h2>
            </div>
            <?php if ($this->message) echo $this->message; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="layout-option <?php echo ($currentLayout === 'default') ? 'selected' : ''; ?>" onclick="selectLayout('default')">
                            <div class="layout-preview">
                                <div class="preview-sidebar"></div>
                                <div class="preview-content"></div>
                            </div>
                            <h3 class="layout-title">Mặc định</h3>
                            <p class="layout-description">Sidebar cố định bên trái với đầy đủ menu</p>
                            <input type="radio" name="layout" value="default" <?php echo ($currentLayout === 'default') ? 'checked' : ''; ?> style="display: none;">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="layout-option <?php echo ($currentLayout === 'navbar') ? 'selected' : ''; ?>" onclick="selectLayout('navbar')">
                            <div class="layout-preview">
                                <div class="preview-navbar"></div>
                                <div class="preview-content-full"></div>
                            </div>
                            <h3 class="layout-title">Navbar</h3>
                            <p class="layout-description">Menu chuyển thành navbar phía trên</p>
                            <input type="radio" name="layout" value="navbar" <?php echo ($currentLayout === 'navbar') ? 'checked' : ''; ?> style="display: none;">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="layout-option <?php echo ($currentLayout === 'icon') ? 'selected' : ''; ?>" onclick="selectLayout('icon')">
                            <div class="layout-preview">
                                <div class="preview-icon-sidebar"></div>
                                <div class="preview-icon-content"></div>
                            </div>
                            <h3 class="layout-title">Icon Only</h3>
                            <p class="layout-description">Sidebar chỉ hiển thị icon</p>
                            <input type="radio" name="layout" value="icon" <?php echo ($currentLayout === 'icon') ? 'checked' : ''; ?> style="display: none;">
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-2"></i>Lưu cài đặt
                    </button>
                </div>
            </form>
        </div>

        <style>
            :root {
                --primary-blue: #090e66;
                --light-blue: #e8e9ff;
                --hover-blue: #070b4d;
            }
            
            .settings-container {
                max-width: 800px;
                margin: 50px auto;
                padding: 30px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }

            .settings-header {
                position: relative;
            }

            .btn-back {
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
                border: none;
                cursor: pointer;
            }

            .btn-back:hover {
                background: var(--hover-blue);
                color: white;
                transform: translateY(-2px);
            }

            .btn-back {
                transform: translateX(-3px);
            }

            .layout-option {
                background: var(--light-blue);
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                cursor: pointer;
                transition: all 0.3s;
                border: 2px solid transparent;
            }

            .layout-option:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }

            .layout-option.selected {
                border-color: var(--primary-blue);
                background: white;
            }

            .layout-preview {
                width: 100%;
                height: 200px;
                background: white;
                border-radius: 8px;
                margin-bottom: 15px;
                position: relative;
                overflow: hidden;
            }

            .layout-title {
                color: var(--primary-blue);
                font-weight: 600;
                margin-bottom: 10px;
            }

            .layout-description {
                color: #666;
                font-size: 0.9rem;
            }

            .btn-save {
                background: var(--primary-blue);
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 8px;
                transition: all 0.3s;
            }

            .btn-save:hover {
                background: var(--hover-blue);
                transform: translateY(-2px);
            }

            /* Layout Preview Styles */
            .preview-sidebar {
                position: absolute;
                left: 0;
                top: 0;
                width: 30%;
                height: 100%;
                background: var(--primary-blue);
            }

            .preview-navbar {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 20%;
                background: var(--primary-blue);
            }

            .preview-content {
                position: absolute;
                top: 20%;
                left: 30%;
                width: 70%;
                height: 80%;
                background: #f8f9fa;
            }

            .preview-content-full {
                position: absolute;
                top: 20%;
                left: 0;
                width: 100%;
                height: 80%;
                background: #f8f9fa;
            }

            .preview-icon-sidebar {
                position: absolute;
                left: 0;
                top: 0;
                width: 10%;
                height: 100%;
                background: var(--primary-blue);
            }

            .preview-icon-content {
                position: absolute;
                top: 0;
                left: 10%;
                width: 90%;
                height: 100%;
                background: #f8f9fa;
            }
        </style>

        <script>
            function selectLayout(layout) {
                document.querySelectorAll('.layout-option').forEach(option => {
                    option.classList.remove('selected');
                });
                
                const selectedOption = document.querySelector(`.layout-option[onclick="selectLayout('${layout}')"]`);
                selectedOption.classList.add('selected');
                
                document.querySelector(`input[value="${layout}"]`).checked = true;
            }
        </script>
        <?php
    }
}
?> 