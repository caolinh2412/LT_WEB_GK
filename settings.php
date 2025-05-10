<?php
session_start();
require 'config.php';
require_once 'modules/settings_module.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$settingsModule = new SettingsModule($pdo);
$settingsModule->handleSettings();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cài đặt</title>
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

        .settings-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
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
</head>
<body>
    <?php $settingsModule->renderSettings(); ?>

    <script>
        function selectLayout(layout) {
            // Bỏ chọn tất cả các options
            document.querySelectorAll('.layout-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Chọn option được click
            const selectedOption = document.querySelector(`.layout-option[onclick="selectLayout('${layout}')"]`);
            selectedOption.classList.add('selected');
            
            // Check radio button tương ứng
            document.querySelector(`input[value="${layout}"]`).checked = true;
        }
    </script>
</body>
</html> 