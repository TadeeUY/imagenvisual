<?php
require_once __DIR__ . '/config.php';

session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: ./login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/ECC256Crypt.php';

$works = require __DIR__ . '/../data/works.php';
$clients = require __DIR__ . '/../data/clients.php';
$categories = require __DIR__ . '/../data/categories.php';
$users = require __DIR__ . '/../data/users.php';

foreach ($users as &$u) {
    if (!empty($u['google_2fa_secret'])) {
        try {
            $u['google_2fa_secret'] = ECC256Crypt::decrypt($u['google_2fa_secret']);
        } catch (Throwable $e) {
            $u['google_2fa_secret'] = '';
        }
    }
}
unset($u);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Imagen Visual</title>
    <link rel="shortcut icon" href="../imagenes/logos/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #dc3545;
            --primary-hover: #b02a37;
            --bg-dark: #0f1016;
            --sidebar-bg: #161722;
            --card-bg: #1e1f29;
            --input-bg: rgba(255, 255, 255, 0.05);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            background: #22c55e;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-weight: 500;
        }
        .toast.error {
            background: #ef4444;
        }
        .toast.show {
            transform: translateY(0);
        }

        /* HEADER */
        header.admin-header {
            background-color: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header.admin-header .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        header.admin-header .brand img {
            max-height: 40px;
            height: auto;
        }

        header.admin-header .brand h1 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        header.admin-header .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        header.admin-header .user-menu .username {
            font-weight: 500;
            color: var(--text-muted);
        }

        header.admin-header .user-menu .btn-logout {
            padding: 8px 15px;
            background: rgba(220, 53, 69, 0.1);
            color: var(--primary);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        header.admin-header .user-menu .btn-logout:hover {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }

        /* MAIN CONTAINER */
        .admin-main {
            display: flex;
            flex: 1;
            padding: 30px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            flex-direction: column;
            gap: 30px;
        }

        /* NAV TABS */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            gap: 15px;
        }

        .tab-btn {
            padding: 12px 25px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tab-btn:hover {
            color: #fff;
        }

        .tab-btn.active {
            color: var(--primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        /* TAB PANELS */
        .tab-panel {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* CONTROLS BAR (WORKS) */
        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-filter-group {
            display: flex;
            gap: 15px;
            flex: 1;
            max-width: 600px;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-wrapper input {
            width: 100%;
            padding: 10px 15px 10px 45px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            outline: none;
            transition: all 0.3s;
        }

        .search-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
        }

        .filter-select {
            padding: 10px 20px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            outline: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-select:focus {
            border-color: var(--primary);
        }

        /* DISEÑO PREMIUM DE SELECTORES (DROP DOWN) */
        select.filter-select,
        .form-group-modal select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
            background-repeat: no-repeat !important;
            background-position: right 15px center !important;
            background-size: 14px !important;
            padding-right: 40px !important;
            cursor: pointer;
        }

        /* Hover & Focus */
        select.filter-select:hover,
        .form-group-modal select:hover {
            border-color: rgba(255, 255, 255, 0.25);
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
        }

        select.filter-select:focus,
        .form-group-modal select:focus {
            border-color: var(--primary) !important;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23dc3545' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15) !important;
        }

        /* Opciones desplegables */
        select.filter-select option,
        .form-group-modal select option {
            background-color: #1a1b26 !important;
            color: #fff !important;
            padding: 10px !important;
        }

        .btn-primary {
            padding: 10px 20px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.25);
        }

        /* WORKS GRID */
        .works-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .work-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .work-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .work-card .thumbnail-wrapper {
            position: relative;
            padding-top: 56.25%; /* 16:9 ratio */
            background-color: #000;
            overflow: hidden;
        }

        .work-card .thumbnail-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .work-card:hover .thumbnail-wrapper img {
            transform: scale(1.05);
        }

        .work-card .image-count-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        .work-card .category-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--primary);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .work-card .card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .work-card .card-content h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .work-card .card-content p {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .work-card .card-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }

        .btn-action-card {
            flex: 1;
            padding: 8px;
            border: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 6px;
            color: var(--text-color);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }

        .btn-action-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-action-card.delete:hover {
            background: rgba(220, 53, 69, 0.1);
            color: var(--primary);
            border-color: rgba(220, 53, 69, 0.2);
        }

        /* CLIENTS PANEL */
        .clients-container {
            display: flex;
            flex-direction: column;
            gap: 35px;
        }

        .clients-line-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
        }

        .clients-line-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .clients-line-card .line-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* LOGO GRID WITH HORIZONTAL SCROLL */
        .logos-row {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 10px 5px 20px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.15);
            min-height: 150px;
            align-items: center;
            scroll-behavior: smooth;
        }

        .logo-admin-item {
            background: #fff;
            border-radius: 8px;
            padding: 8px;
            min-width: 130px;
            max-width: 130px;
            height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .logo-admin-item .img-container {
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .logo-admin-item img {
            max-width: 110px;
            max-height: 55px;
            object-fit: contain;
            pointer-events: none; /* Evita arrastrar la imagen directamente */
        }

        /* Controles individuales de logo */
        .logo-controls {
            position: absolute;
            top: -8px;
            right: -8px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .logo-admin-item:hover .logo-controls {
            opacity: 1;
        }

        .btn-logo-ctrl {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #000;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.65rem;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s;
        }

        .btn-logo-ctrl:hover {
            transform: scale(1.1);
            background: var(--primary);
        }

        .btn-logo-ctrl.delete:hover {
            background: #ef4444;
        }

        /* Botones de navegación interna (izquierda/derecha) */
        .logo-nav-btns {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            background: #161722;
            border: 1px solid var(--border-color);
            padding: 2px 6px;
            border-radius: 10px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .logo-admin-item:hover .logo-nav-btns {
            opacity: 1;
        }

        .btn-logo-nav {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.75rem;
            transition: color 0.2s;
        }

        .btn-logo-nav:hover {
            color: var(--primary);
        }

        /* MODAL CONTAINER */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .modal.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            animation: modalSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalSlideUp {
            from { transform: translateY(30px); }
            to { transform: translateY(0); }
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-close-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: #fff;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group-modal {
            margin-bottom: 20px;
        }

        .form-group-modal label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group-modal input[type="text"],
        .form-group-modal input[type="password"],
        .form-group-modal textarea,
        .form-group-modal select {
            width: 100%;
            padding: 10px 15px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            outline: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 45px 10px 15px !important;
        }

        .form-group-modal input[type="text"]:focus,
        .form-group-modal input[type="password"]:focus,
        .form-group-modal textarea:focus,
        .form-group-modal select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
        }

        /* Drag-and-drop file uploader */
        .dropzone {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            background: rgba(255, 255, 255, 0.01);
            cursor: pointer;
            transition: all 0.3s;
        }

        .dropzone:hover, .dropzone.dragover {
            border-color: var(--primary);
            background: rgba(220, 53, 69, 0.05);
        }

        .dropzone i {
            font-size: 2rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .dropzone p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .dropzone-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-img-container {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            background: #000;
        }

        .preview-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-img-container .btn-delete-preview {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(0,0,0,0.8);
            border: none;
            color: #fff;
            font-size: 0.6rem;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-secondary {
            padding: 10px 20px;
            background: transparent;
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* CLIENTS PAGE BUTTONS */
        .clients-actions-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-save-order {
            background-color: var(--success);
            display: none; /* Solo se muestra cuando hay cambios */
        }
        .btn-save-order:hover {
            background-color: #059669;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.25);
        }

        /* RESPONSIVE DESIGN FOR MOBILE PHONES & TABLETS */
        @media (max-width: 1024px) {
            .admin-main {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            header.admin-header {
                padding: 15px;
                flex-direction: column;
                gap: 15px;
                align-items: center;
                text-align: center;
            }
            header.admin-header .brand {
                flex-direction: column;
                gap: 5px;
            }
            header.admin-header .brand h1 {
                font-size: 1.15rem;
            }
            header.admin-header .user-menu {
                width: 100%;
                justify-content: space-between;
                gap: 10px;
            }
            .admin-main {
                padding: 15px;
                gap: 20px;
            }
            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 8px;
                gap: 10px;
                scrollbar-width: none;
                border-bottom: 1px solid var(--border-color);
            }
            .tabs::-webkit-scrollbar {
                display: none;
            }
            .tab-btn {
                flex: 0 0 auto;
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            .controls-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .search-filter-group {
                flex-direction: column;
                max-width: 100%;
                gap: 10px;
            }
            .filter-select {
                width: 100%;
            }
            .btn-primary {
                justify-content: center;
                width: 100%;
            }
            .modal-content {
                width: 95%;
                margin: 10px;
                max-height: 95vh;
            }
            .modal-body, .modal-footer, .modal-header {
                padding: 15px;
            }
            
            /* Responsive table wrapper */
            .table-responsive-wrapper {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Keep table buttons from wrapping */
            .category-row td, .user-row td, table th, table td {
                white-space: nowrap;
            }
        }

        @media (max-width: 480px) {
            .works-grid {
                grid-template-columns: 1fr;
            }
            .work-card .thumbnail-wrapper {
                padding-top: 60%;
            }
            .card-actions {
                flex-direction: column;
                gap: 8px;
            }
            .btn-action-card {
                width: 100%;
            }
            .logo-admin-item {
                min-width: 100px;
                max-width: 100px;
                height: 70px;
            }
            .logos-row {
                min-height: 100px;
                padding-bottom: 15px;
            }
            .clients-line-card h2 {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .clients-line-card h2 .btn-primary {
                width: 100%;
            }
        }

    </style>
</head>
<body>

    <!-- Toast de notificaciones -->
    <div id="toast" class="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-message">Operación exitosa</span>
    </div>

    <header class="admin-header">
        <div class="brand">
            <img src="../imagenes/logos/logo.png" alt="Imagen Visual">
            <h1>Panel Administrativo</h1>
        </div>
        <div class="user-menu">
            <span class="username" onclick="switchTab('configuracion')" style="cursor: pointer;" title="Ir a Mi Configuración"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="./logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </header>

    <main class="admin-main">
        <!-- Tabs de navegación -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('trabajos')">
                <i class="fas fa-briefcase"></i> Gestión de Trabajos
            </button>
            <button class="tab-btn" onclick="switchTab('clientes')">
                <i class="fas fa-images"></i> Carrusel de Clientes (3 Líneas)
            </button>
            <button class="tab-btn" onclick="switchTab('categorias')">
                <i class="fas fa-tags"></i> Gestión de Categorías
            </button>
            <?php if ($_SESSION['role'] === 'superadmin'): ?>
            <button class="tab-btn" onclick="switchTab('usuarios')">
                <i class="fas fa-users"></i> Gestión de Usuarios
            </button>
            <?php endif; ?>
            <button class="tab-btn" onclick="switchTab('configuracion')">
                <i class="fas fa-cog"></i> Configuración
            </button>
        </div>

        <!-- PANEL DE TRABAJOS -->
        <section id="panel-trabajos" class="tab-panel active">
            <div class="controls-bar">
                <div class="search-filter-group">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="work-search" placeholder="Buscar por título o descripción..." oninput="filterWorks()">
                    </div>
                    <select id="work-category-filter" class="filter-select" onchange="filterWorks()">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-primary" onclick="openAddWorkModal()">
                    <i class="fas fa-plus"></i> Agregar Trabajo
                </button>
            </div>

            <div class="works-grid" id="works-grid">
                <?php foreach ($works as $w): ?>
                    <div class="work-card" 
                         data-id="<?php echo $w['id']; ?>" 
                         data-category="<?php echo htmlspecialchars($w['category']); ?>"
                         data-title="<?php echo htmlspecialchars(strtolower($w['title'])); ?>"
                         data-desc="<?php echo htmlspecialchars(strtolower($w['description'])); ?>">
                        <div class="thumbnail-wrapper">
                            <img src="../<?php echo ltrim($w['images'][0], './'); ?>" alt="<?php echo htmlspecialchars($w['title']); ?>" loading="lazy">
                            <span class="category-tag">
                                <?php 
                                    $resolved_name = $w['category'];
                                    foreach ($categories as $cat) {
                                        if ($cat['slug'] === $w['category']) {
                                            $resolved_name = $cat['name'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars($resolved_name);
                                ?>
                            </span>
                            <?php if (count($w['images']) > 1): ?>
                                <span class="image-count-tag"><?php echo count($w['images']); ?> fotos</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($w['title']); ?></h3>
                            <p><?php echo htmlspecialchars($w['description']); ?></p>
                            <div class="card-actions">
                                <button class="btn-action-card" onclick="openEditWorkModal(<?php echo htmlspecialchars(json_encode($w)); ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn-action-card delete" onclick="deleteWork(<?php echo $w['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- PANEL DE CLIENTES -->
        <section id="panel-clientes" class="tab-panel">
            <div class="clients-container">
                <!-- LÍNEA 1 -->
                <div class="clients-line-card" data-line="line1">
                    <h2>
                        <span>Línea 1 (Carrusel Superior)</span>
                        <button class="btn-primary" onclick="openAddLogoModal('line1')">
                            <i class="fas fa-plus"></i> Agregar Logo
                        </button>
                    </h2>
                    <p class="line-subtitle">Desplazamiento automático hacia la derecha en la web pública.</p>
                    <div class="logos-row" id="logo-row-line1">
                        <?php foreach ($clients['line1'] as $logo): ?>
                            <div class="logo-admin-item" data-src="<?php echo htmlspecialchars($logo['src']); ?>" data-alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                <div class="img-container">
                                    <img src="../<?php echo ltrim($logo['src'], './'); ?>" alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                </div>
                                <input type="text" class="logo-width-input" placeholder="Ancho (ej. 75px)" value="<?php echo htmlspecialchars($logo['width'] ?? ''); ?>" oninput="markLogoChanges()" style="width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; font-size: 0.7rem; text-align: center; color: #333; background: #fff; outline: none; margin-top: 4px;">
                                <div class="logo-controls">
                                    <button class="btn-logo-ctrl delete" onclick="deleteLogo('line1', '<?php echo htmlspecialchars($logo['src']); ?>')" title="Eliminar"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="logo-nav-btns">
                                    <button class="btn-logo-nav" onclick="moveLogo('line1', '<?php echo htmlspecialchars($logo['src']); ?>', 'left')"><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn-logo-nav" onclick="moveLogo('line1', '<?php echo htmlspecialchars($logo['src']); ?>', 'right')"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- LÍNEA 2 -->
                <div class="clients-line-card" data-line="line2">
                    <h2>
                        <span>Línea 2 (Carrusel Medio)</span>
                        <button class="btn-primary" onclick="openAddLogoModal('line2')">
                            <i class="fas fa-plus"></i> Agregar Logo
                        </button>
                    </h2>
                    <p class="line-subtitle">Desplazamiento automático hacia la izquierda en la web pública.</p>
                    <div class="logos-row" id="logo-row-line2">
                        <?php foreach ($clients['line2'] as $logo): ?>
                            <div class="logo-admin-item" data-src="<?php echo htmlspecialchars($logo['src']); ?>" data-alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                <div class="img-container">
                                    <img src="../<?php echo ltrim($logo['src'], './'); ?>" alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                </div>
                                <input type="text" class="logo-width-input" placeholder="Ancho (ej. 75px)" value="<?php echo htmlspecialchars($logo['width'] ?? ''); ?>" oninput="markLogoChanges()" style="width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; font-size: 0.7rem; text-align: center; color: #333; background: #fff; outline: none; margin-top: 4px;">
                                <div class="logo-controls">
                                    <button class="btn-logo-ctrl delete" onclick="deleteLogo('line2', '<?php echo htmlspecialchars($logo['src']); ?>')" title="Eliminar"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="logo-nav-btns">
                                    <button class="btn-logo-nav" onclick="moveLogo('line2', '<?php echo htmlspecialchars($logo['src']); ?>', 'left')"><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn-logo-nav" onclick="moveLogo('line2', '<?php echo htmlspecialchars($logo['src']); ?>', 'right')"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- LÍNEA 3 -->
                <div class="clients-line-card" data-line="line3">
                    <h2>
                        <span>Línea 3 (Carrusel Inferior)</span>
                        <button class="btn-primary" onclick="openAddLogoModal('line3')">
                            <i class="fas fa-plus"></i> Agregar Logo
                        </button>
                    </h2>
                    <p class="line-subtitle">Desplazamiento automático hacia la derecha en la web pública.</p>
                    <div class="logos-row" id="logo-row-line3">
                        <?php foreach ($clients['line3'] as $logo): ?>
                            <div class="logo-admin-item" data-src="<?php echo htmlspecialchars($logo['src']); ?>" data-alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                <div class="img-container">
                                    <img src="../<?php echo ltrim($logo['src'], './'); ?>" alt="<?php echo htmlspecialchars($logo['alt']); ?>">
                                </div>
                                <input type="text" class="logo-width-input" placeholder="Ancho (ej. 75px)" value="<?php echo htmlspecialchars($logo['width'] ?? ''); ?>" oninput="markLogoChanges()" style="width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; font-size: 0.7rem; text-align: center; color: #333; background: #fff; outline: none; margin-top: 4px;">
                                <div class="logo-controls">
                                    <button class="btn-logo-ctrl delete" onclick="deleteLogo('line3', '<?php echo htmlspecialchars($logo['src']); ?>')" title="Eliminar"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="logo-nav-btns">
                                    <button class="btn-logo-nav" onclick="moveLogo('line3', '<?php echo htmlspecialchars($logo['src']); ?>', 'left')"><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn-logo-nav" onclick="moveLogo('line3', '<?php echo htmlspecialchars($logo['src']); ?>', 'right')"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Fila de Guardar Orden (Flotante o visible cuando hay cambios) -->
            <div class="clients-actions-row">
                <button id="btn-save-order" class="btn-primary btn-save-order" onclick="saveLogosOrder()">
                    <i class="fas fa-save"></i> Guardar Posiciones y Tamaños de Logos
                </button>
            </div>
        </section>

        <!-- PANEL DE CATEGORÍAS -->
        <section id="panel-categorias" class="tab-panel">
            <div class="controls-bar">
                <div></div>
                <button class="btn-primary" onclick="openAddCategoryModal()">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>
            
            <div class="table-responsive-wrapper" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; overflow-x: auto; margin-top: 15px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;" id="categories-table">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem;">
                            <th style="padding: 15px 20px;">Nombre</th>
                            <th style="padding: 15px 20px;">Slug / URL</th>
                            <th style="padding: 15px 20px;">Descripción</th>
                            <th style="padding: 15px 20px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="categories-list-body">
                        <?php foreach ($categories as $cat): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);" class="category-row" data-id="<?php echo $cat['id']; ?>">
                                <td style="padding: 15px 20px; font-weight: 600;"><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td style="padding: 15px 20px;"><code style="background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;">/<?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                <td style="padding: 15px 20px; color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td style="padding: 15px 20px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-logo-nav" onclick="moveCategory(<?php echo $cat['id']; ?>, 'up')" title="Subir"><i class="fas fa-chevron-up"></i></button>
                                        <button class="btn-logo-nav" onclick="moveCategory(<?php echo $cat['id']; ?>, 'down')" title="Bajar"><i class="fas fa-chevron-down"></i></button>
                                        <button class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem;" onclick='openEditCategoryModal(<?php echo htmlspecialchars(json_encode($cat, JSON_HEX_APOS | JSON_HEX_QUOT)); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-primary delete" style="padding: 6px 12px; font-size: 0.8rem; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: var(--primary);" onclick="deleteCategory(<?php echo $cat['id']; ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="clients-actions-row" id="category-order-actions" style="display: none; margin-top: 20px;">
                <button class="btn-primary btn-save-order" style="display: block;" onclick="saveCategoryOrder()">
                    <i class="fas fa-save"></i> Guardar Orden de Categorías
                </button>
            </div>
        </section>

        <?php if ($_SESSION['role'] === 'superadmin'): ?>
        <!-- PANEL DE USUARIOS -->
        <section id="panel-usuarios" class="tab-panel">
            <div class="controls-bar">
                <div></div>
                <button class="btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>
            
            <div class="table-responsive-wrapper" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; overflow-x: auto; margin-top: 15px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem;">
                            <th style="padding: 15px 20px;">Nombre</th>
                            <th style="padding: 15px 20px;">Usuario</th>
                            <th style="padding: 15px 20px;">Rol</th>
                            <th style="padding: 15px 20px;">Google 2FA</th>
                            <th style="padding: 15px 20px; text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 15px 20px; font-weight: 600; color: #fff;"><?php echo htmlspecialchars($u['name'] ?? 'Sin Nombre'); ?></td>
                                <td style="padding: 15px 20px; color: var(--text-muted);"><?php echo htmlspecialchars($u['username']); ?></td>
                                <td style="padding: 15px 20px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; background: <?php echo $u['role'] === 'superadmin' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(59, 130, 246, 0.15)'; ?>; color: <?php echo $u['role'] === 'superadmin' ? '#ef4444' : '#3b82f6'; ?>;">
                                        <?php echo htmlspecialchars($u['role']); ?>
                                    </span>
                                </td>
                                <td style="padding: 15px 20px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; background: <?php echo $u['is_2fa_enabled'] ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)'; ?>; color: <?php echo $u['is_2fa_enabled'] ? '#10b981' : '#f59e0b'; ?>;">
                                        <?php echo $u['is_2fa_enabled'] ? 'Activo' : 'Pendiente (Primer inicio)'; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px 20px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); color: #38bdf8;" onclick="openResetPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')" title="Restablecer Contraseña">
                                            <i class="fas fa-key"></i> Contraseña
                                        </button>
                                        <button class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #f59e0b;" onclick="resetUser2FA(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')" title="Restablecer 2FA (Para recuperar si perdió teléfono)">
                                            <i class="fas fa-shield-alt"></i> Reset 2FA
                                        </button>
                                        <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                            <button class="btn-primary delete" style="padding: 6px 12px; font-size: 0.8rem; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: var(--primary);" onclick="deleteUser(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <!-- PANEL DE CONFIGURACIÓN -->
        <section id="panel-configuracion" class="tab-panel">
            <div style="max-width: 550px; margin: 20px auto; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 35px 30px; box-shadow: 0 12px 30px rgba(0,0,0,0.35);">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(220, 53, 69, 0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 1.3rem; font-weight: 600; color: #fff; margin: 0;">Mi Configuración</h2>
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 4px;">Cambiar contraseña de acceso a la cuenta</p>
                    </div>
                </div>

                <form id="change-password-form" onsubmit="event.preventDefault(); open2FAModal();">
                    <div class="form-group-modal" style="margin-bottom: 20px;">
                        <label for="current_password" style="font-weight: 500;"><i class="fas fa-lock" style="color: var(--text-muted); margin-right: 6px;"></i> Contraseña Actual</label>
                        <div class="input-wrapper" style="position: relative;">
                            <input type="password" id="current_password" name="current_password" required autocomplete="current-password" placeholder="Ingresa tu contraseña actual" style="padding-right: 45px;">
                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('current_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 1.1rem;"></i>
                        </div>
                    </div>

                    <div class="form-group-modal" style="margin-bottom: 20px;">
                        <label for="new_password" style="font-weight: 500;"><i class="fas fa-key" style="color: var(--text-muted); margin-right: 6px;"></i> Nueva Contraseña</label>
                        <div class="input-wrapper" style="position: relative;">
                            <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password" placeholder="Ingresa la nueva contraseña" style="padding-right: 45px;">
                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('new_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 1.1rem;"></i>
                        </div>
                    </div>

                    <div class="form-group-modal" style="margin-bottom: 25px;">
                        <label for="confirm_password" style="font-weight: 500;"><i class="fas fa-check-double" style="color: var(--text-muted); margin-right: 6px;"></i> Confirmar Nueva Contraseña</label>
                        <div class="input-wrapper" style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password" placeholder="Confirma la nueva contraseña" style="padding-right: 45px;">
                            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('confirm_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; font-size: 1.1rem;"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 13px; justify-content: center; font-size: 1rem; border-radius: 8px;">
                        <i class="fas fa-shield-alt"></i> Cambiar Contraseña
                    </button>
                </form>
            </div>
        </section>
    </main>

    <!-- MODAL TRABAJOS (AGREGAR / EDITAR) -->
    <div class="modal" id="work-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Agregar Nuevo Trabajo</h3>
                <button class="modal-close-btn" onclick="closeWorkModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="work-form">
                    <input type="hidden" id="work-id" name="id">
                    <input type="hidden" name="action" id="work-form-action" value="add_work">
                    <input type="hidden" name="deleted_images" id="work-deleted-images" value="[]">
                    <input type="hidden" name="image_order" id="work-image-order" value="[]">

                    <div class="form-group-modal">
                        <label for="work-category">Categoría</label>
                        <select id="work-category" name="category" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-modal">
                        <label for="work-title">Título del Trabajo</label>
                        <input type="text" id="work-title" name="title" required placeholder="Ej. Letras corpóreas de acrílico">
                    </div>

                    <div class="form-group-modal">
                        <label for="work-description">Descripción</label>
                        <textarea id="work-description" name="description" rows="3" placeholder="Detalle técnico, materiales usados, etc."></textarea>
                    </div>

                    <!-- SUBIDA DE IMAGEN PRINCIPAL -->
                    <div class="form-group-modal" id="primary-image-group">
                        <label>Imagen Principal (Requerido)</label>
                        <div class="dropzone" id="primary-dropzone" onclick="document.getElementById('primary-image-input').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Haz clic para subir o arrastra la foto principal aquí</p>
                            <input type="file" id="primary-image-input" name="primary_image" accept="image/*" style="display:none" onchange="previewPrimaryImage(this)">
                        </div>
                        <div class="dropzone-preview" id="primary-preview"></div>
                    </div>

                    <!-- SUBIDA DE IMÁGENES SECUNDARIAS -->
                    <div class="form-group-modal">
                        <label>Imágenes Secundarias (Opcional - Múltiple)</label>
                        <div class="dropzone" id="secondary-dropzone" onclick="document.getElementById('secondary-images-input').click()">
                            <i class="fas fa-images"></i>
                            <p>Haz clic para seleccionar múltiples fotos secundarias</p>
                            <input type="file" id="secondary-images-input" name="secondary_images[]" accept="image/*" multiple style="display:none" onchange="previewSecondaryImages(this)">
                        </div>
                        <div class="dropzone-preview" id="secondary-preview"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeWorkModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitWorkForm()">Guardar</button>
            </div>
        </div>
    </div>

    <!-- MODAL AGREGAR LOGO CLIENTE -->
    <div class="modal" id="logo-modal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3>Agregar Logotipo de Cliente</h3>
                <button class="modal-close-btn" onclick="closeLogoModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="logo-form">
                    <input type="hidden" name="action" value="add_logo">
                    <input type="hidden" name="line" id="logo-line">

                    <div class="form-group-modal">
                        <label for="logo-alt">Nombre del Cliente (Ej. Abitab)</label>
                        <input type="text" id="logo-alt" name="alt" required placeholder="Nombre para el texto alternativo">
                    </div>

                    <div class="form-group-modal">
                        <label>Archivo del Logotipo (PNG/SVG transparentes recomendados)</label>
                        <div class="dropzone" onclick="document.getElementById('logo-file-input').click()">
                            <i class="fas fa-file-image"></i>
                            <p>Seleccionar logotipo</p>
                            <input type="file" id="logo-file-input" name="logo_file" accept="image/*" required style="display:none" onchange="previewLogoImage(this)">
                        </div>
                        <div class="dropzone-preview" id="logo-preview"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeLogoModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitLogoForm()">Subir Logo</button>
            </div>
        </div>
    </div>

    <!-- MODAL CATEGORÍAS (AGREGAR / EDITAR) -->
    <div class="modal" id="category-modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="category-modal-title">Agregar Nueva Categoría</h3>
                <button class="modal-close-btn" onclick="closeCategoryModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <input type="hidden" id="category-id" name="id">
                    <input type="hidden" name="action" id="category-form-action" value="add_category">

                    <div class="form-group-modal">
                        <label for="category-name">Nombre de la Categoría</label>
                        <input type="text" id="category-name" name="name" required placeholder="Ej. Fachadas Especiales" oninput="suggestSlug(this.value)">
                    </div>

                    <div class="form-group-modal">
                        <label for="category-slug">Slug / URL (Solo letras, números y guiones)</label>
                        <input type="text" id="category-slug" name="slug" required placeholder="Ej. fachadas-especiales">
                    </div>

                    <div class="form-group-modal">
                        <label for="category-description">Descripción Breve (Se muestra en la página del producto)</label>
                        <textarea id="category-description" name="description" rows="3" placeholder="Detalle sobre esta categoría de productos..."></textarea>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); margin: 20px 0; padding-top: 15px;">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 15px; color: var(--primary);">Configuración SEO (Meta Tags)</h4>
                        
                        <div class="form-group-modal">
                            <label for="category-seo-title">Título SEO (Etiqueta Title)</label>
                            <input type="text" id="category-seo-title" name="seo_title" placeholder="Ej. Fachadas de Aluminio Compuesto en Uruguay">
                        </div>

                        <div class="form-group-modal">
                            <label for="category-seo-desc">Descripción SEO (Etiqueta Meta Description)</label>
                            <textarea id="category-seo-desc" name="seo_desc" rows="2" placeholder="Breve resumen llamativo para los buscadores de Google..."></textarea>
                        </div>

                        <div class="form-group-modal">
                            <label for="category-seo-keywords">Palabras Clave SEO (Separadas por comas)</label>
                            <input type="text" id="category-seo-keywords" name="seo_keywords" placeholder="Ej. fachadas uruguay, paneles de aluminio, revestimiento comercial">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeCategoryModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitCategoryForm()">Guardar</button>
            </div>
        </div>
    </div>

    <?php if ($_SESSION['role'] === 'superadmin'): ?>
    <!-- MODAL AGREGAR USUARIO -->
    <div class="modal" id="user-modal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3>Crear Nuevo Usuario</h3>
                <button class="modal-close-btn" onclick="closeUserModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" name="action" value="add_user">

                    <div class="form-group-modal">
                        <label for="user-name">Nombre Completo</label>
                        <input type="text" id="user-name" name="name" required placeholder="Ej. Juan Pérez">
                    </div>

                    <div class="form-group-modal">
                        <label for="user-username">Nombre de Usuario</label>
                        <input type="text" id="user-username" name="username" required autocomplete="off" placeholder="Ej. vendedor">
                    </div>

                    <div class="form-group-modal">
                        <label for="user-password">Contraseña Inicial</label>
                        <input type="text" id="user-password" name="password" required placeholder="Contraseña de inicio">
                    </div>

                    <div class="form-group-modal">
                        <label for="user-role">Rol de Acceso</label>
                        <select id="user-role" name="role" required>
                            <option value="admin">Administrador (Gestión de Trabajos/Logos/Categorías)</option>
                            <option value="superadmin">Superadministrador (Gestión Completa + Usuarios)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeUserModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitUserForm()">Crear Usuario</button>
            </div>
        </div>
    </div>

    <!-- MODAL RESTABLECER CONTRASEÑA -->
    <div class="modal" id="reset-password-modal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3>Restablecer Contraseña</h3>
                <button class="modal-close-btn" onclick="closeResetPasswordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="reset-password-form">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="id" id="reset-pass-user-id">

                    <div class="form-group-modal">
                        <label>Usuario</label>
                        <input type="text" id="reset-pass-username" readonly style="background: rgba(255,255,255,0.02); color: var(--text-muted); border-color: transparent;">
                    </div>

                    <div class="form-group-modal">
                        <label for="reset-pass-password">Nueva Contraseña</label>
                        <input type="text" id="reset-pass-password" name="password" required placeholder="Ingresa la nueva contraseña">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeResetPasswordModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitResetPasswordForm()">Guardar Contraseña</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- MODAL CONFIRMACIÓN 2FA PARA CAMBIO DE CONTRASEÑA -->
    <div class="modal" id="change-password-2fa-modal">
        <div class="modal-content" style="max-width: 440px;">
            <div class="modal-header">
                <h3 style="display: flex; align-items: center; gap: 10px; color: #38bdf8;">
                    <i class="fas fa-shield-alt"></i> Verificación 2FA
                </h3>
                <button class="modal-close-btn" onclick="close2FAModal()">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5;">
                    Para autorizar el cambio de contraseña, ingresa el código de 6 dígitos generado por tu aplicación <strong>Google Authenticator</strong>:
                </p>

                <form id="change-password-2fa-form" onsubmit="event.preventDefault(); submitChangePassword();">
                    <div class="form-group-modal">
                        <div class="input-wrapper">
                            <input type="text" id="otp_code_change" name="otp_code" required pattern="[0-9]{6}" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="000000" style="text-align: center; font-size: 1.6rem; letter-spacing: 8px; font-weight: bold; background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(56, 189, 248, 0.4); color: #fff; width: 100%; border-radius: 8px; padding: 12px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="close2FAModal()">Cancelar</button>
                <button class="btn-primary" onclick="submitChangePassword()" style="background: #38bdf8; color: #000; font-weight: bold;">
                    <i class="fas fa-check"></i> Confirmar y Cambiar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Interceptar todas las peticiones fetch de tipo POST para inyectar automáticamente el token CSRF
        const originalFetch = window.fetch;
        window.fetch = function (url, options = {}) {
            if (options.method === 'POST') {
                if (!options.headers) {
                    options.headers = {};
                }
                options.headers['X-CSRF-Token'] = '<?php echo $_SESSION['csrf_token']; ?>';
            }
            return originalFetch(url, options);
        };

        let hasUnsavedLogoChanges = false;
        let deletedSecondaryImages = [];
        let currentWorkImages = [];

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            
            const icon = toast.querySelector('i');
            if (isError) {
                toast.classList.add('error');
                icon.className = 'fas fa-exclamation-circle';
            } else {
                toast.classList.remove('error');
                icon.className = 'fas fa-check-circle';
            }

            toast.classList.add('show');
        }

        function togglePasswordVisibility(inputId, iconEl) {
            const input = document.getElementById(inputId);
            if (input) {
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                iconEl.classList.toggle('fa-eye');
                iconEl.classList.toggle('fa-eye-slash');
            }
        }

        function open2FAModal() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (!currentPassword) {
                showToast('Debes ingresar tu contraseña actual.', true);
                return;
            }

            if (newPassword.length < 6) {
                showToast('La nueva contraseña debe tener al menos 6 caracteres.', true);
                return;
            }

            if (newPassword !== confirmPassword) {
                showToast('La nueva contraseña y su confirmación no coinciden.', true);
                return;
            }

            if (currentPassword === newPassword) {
                showToast('La nueva contraseña no puede ser igual a la contraseña actual.', true);
                return;
            }

            const otpInput = document.getElementById('otp_code_change');
            otpInput.value = '';
            document.getElementById('change-password-2fa-modal').classList.add('open');
            setTimeout(() => otpInput.focus(), 150);
        }

        function close2FAModal() {
            document.getElementById('change-password-2fa-modal').classList.remove('open');
        }

        async function submitChangePassword() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const otpCode = document.getElementById('otp_code_change').value.trim();

            if (otpCode.length !== 6 || !/^\d+$/.test(otpCode)) {
                showToast('El código 2FA debe ser de 6 dígitos numéricos.', true);
                return;
            }

            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);
            formData.append('otp_code', otpCode);

            try {
                const res = await fetch('./api.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    close2FAModal();
                    showToast(data.message);
                    document.getElementById('change-password-form').reset();
                } else {
                    showToast(data.message || 'Error al cambiar la contraseña.', true);
                }
            } catch (err) {
                console.error(err);
                showToast('Error de conexión con el servidor.', true);
            }
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));

            const tabBtns = Array.from(document.querySelectorAll('.tab-btn'));
            const activeBtn = tabBtns.find(btn => btn.getAttribute('onclick').includes(tab));
            if (activeBtn) activeBtn.classList.add('active');

            const activePanel = document.getElementById('panel-' + tab);
            if (activePanel) activePanel.classList.add('active');
        }

        function filterWorks() {
            const searchVal = document.getElementById('work-search').value.toLowerCase().trim();
            const catVal = document.getElementById('work-category-filter').value;
            const cards = document.querySelectorAll('.work-card');

            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const desc = card.getAttribute('data-desc');
                const cat = card.getAttribute('data-category');

                const matchesSearch = title.includes(searchVal) || desc.includes(searchVal);
                const matchesCat = catVal === '' || cat === catVal;

                if (matchesSearch && matchesCat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function openAddWorkModal() {
            document.getElementById('work-form').reset();
            document.getElementById('work-id').value = '';
            document.getElementById('work-form-action').value = 'add_work';
            document.getElementById('modal-title').textContent = 'Agregar Nuevo Trabajo';
            
            document.getElementById('primary-image-group').style.display = 'block';
            document.getElementById('primary-preview').innerHTML = '';
            document.getElementById('secondary-preview').innerHTML = '';
            deletedSecondaryImages = [];
            currentWorkImages = [];

            document.getElementById('work-modal').classList.add('open');
        }

        function openEditWorkModal(work) {
            document.getElementById('work-form').reset();
            document.getElementById('work-id').value = work.id;
            document.getElementById('work-form-action').value = 'edit_work';
            document.getElementById('modal-title').textContent = 'Editar Trabajo';

            const categorySelect = document.getElementById('work-category');
            // Limpiar opción huérfana temporal
            const tempOption = categorySelect.querySelector('.temp-orphaned-option');
            if (tempOption) tempOption.remove();

            let optionExists = false;
            for (let i = 0; i < categorySelect.options.length; i++) {
                if (categorySelect.options[i].value === work.category) {
                    optionExists = true;
                    break;
                }
            }

            if (!optionExists && work.category) {
                const opt = document.createElement('option');
                opt.value = work.category;
                opt.text = work.category + ' (Categoría no existente)';
                opt.className = 'temp-orphaned-option';
                categorySelect.add(opt);
            }

            categorySelect.value = work.category;
            document.getElementById('work-title').value = work.title;
            document.getElementById('work-description').value = work.description;

            // Ocultar imagen principal de subida por defecto (se maneja en edición por orden o borrado)
            document.getElementById('primary-image-group').style.display = 'none';
            document.getElementById('primary-preview').innerHTML = '';
            
            deletedSecondaryImages = [];
            currentWorkImages = [...work.images];

            renderEditingImagesPreview();

            document.getElementById('work-modal').classList.add('open');
        }

        function closeWorkModal() {
            document.getElementById('work-modal').classList.remove('open');
        }

        function renderEditingImagesPreview() {
            const preview = document.getElementById('secondary-preview');
            preview.innerHTML = '';
            
            currentWorkImages.forEach((imgSrc, index) => {
                const container = document.createElement('div');
                container.className = 'preview-img-container';
                container.setAttribute('data-src', imgSrc);
                
                const img = document.createElement('img');
                img.src = '../' + imgSrc.replace(/^\.\//, '');
                container.appendChild(img);

                const delBtn = document.createElement('button');
                delBtn.className = 'btn-delete-preview';
                delBtn.type = 'button';
                delBtn.innerHTML = '&times;';
                delBtn.onclick = function() {
                    deletedSecondaryImages.push(imgSrc);
                    currentWorkImages.splice(index, 1);
                    renderEditingImagesPreview();
                };
                container.appendChild(delBtn);

                const moveLeft = document.createElement('button');
                moveLeft.style = 'position:absolute; bottom:2px; left:2px; background:rgba(0,0,0,0.8); border:none; color:#fff; font-size:0.6rem; width:18px; height:18px; border-radius:50%; cursor:pointer; display:flex; justify-content:center; align-items:center;';
                moveLeft.innerHTML = '←';
                moveLeft.type = 'button';
                moveLeft.onclick = function() {
                    if (index > 0) {
                        const temp = currentWorkImages[index];
                        currentWorkImages[index] = currentWorkImages[index - 1];
                        currentWorkImages[index - 1] = temp;
                        renderEditingImagesPreview();
                    }
                };
                container.appendChild(moveLeft);

                const moveRight = document.createElement('button');
                moveRight.style = 'position:absolute; bottom:2px; right:2px; background:rgba(0,0,0,0.8); border:none; color:#fff; font-size:0.6rem; width:18px; height:18px; border-radius:50%; cursor:pointer; display:flex; justify-content:center; align-items:center;';
                moveRight.innerHTML = '→';
                moveRight.type = 'button';
                moveRight.onclick = function() {
                    if (index < currentWorkImages.length - 1) {
                        const temp = currentWorkImages[index];
                        currentWorkImages[index] = currentWorkImages[index + 1];
                        currentWorkImages[index + 1] = temp;
                        renderEditingImagesPreview();
                    }
                };
                container.appendChild(moveRight);

                preview.appendChild(container);
            });
        }

        function previewPrimaryImage(input) {
            const preview = document.getElementById('primary-preview');
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.className = 'preview-img-container';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    container.appendChild(img);
                    preview.appendChild(container);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewSecondaryImages(input) {
            const preview = document.getElementById('secondary-preview');
            // En creación limpiamos, en edición anexamos visualmente
            const isAddMode = document.getElementById('work-form-action').value === 'add_work';
            if (isAddMode) {
                preview.innerHTML = '';
            }

            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const container = document.createElement('div');
                        container.className = 'preview-img-container';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        container.appendChild(img);
                        preview.appendChild(container);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function submitWorkForm() {
            const form = document.getElementById('work-form');
            const formData = new FormData(form);

            // Si estamos editando, pasar las listas procesadas
            if (document.getElementById('work-form-action').value === 'edit_work') {
                formData.set('deleted_images', JSON.stringify(deletedSecondaryImages));
                formData.set('image_order', JSON.stringify(currentWorkImages));
            }

            fetch('./api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeWorkModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error en la conexión con el servidor.', true);
            });
        }

        function deleteWork(id) {
            if (confirm('¿Estás seguro de que deseas eliminar permanentemente este trabajo y todas sus imágenes?')) {
                const formData = new FormData();
                formData.append('action', 'delete_work');
                formData.append('id', id);

                fetch('./api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Error al conectar con el servidor.', true);
                });
            }
        }

        function openAddLogoModal(line) {
            document.getElementById('logo-form').reset();
            document.getElementById('logo-line').value = line;
            document.getElementById('logo-preview').innerHTML = '';
            document.getElementById('logo-modal').classList.add('open');
        }

        function closeLogoModal() {
            document.getElementById('logo-modal').classList.remove('open');
        }

        function previewLogoImage(input) {
            const preview = document.getElementById('logo-preview');
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.className = 'preview-img-container';
                    container.style.background = '#fff';
                    container.style.padding = '5px';
                    container.style.display = 'flex';
                    container.style.justifyContent = 'center';
                    container.style.alignItems = 'center';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxHeight = '70px';
                    img.style.maxWidth = '70px';
                    img.style.objectFit = 'contain';
                    container.appendChild(img);
                    preview.appendChild(container);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function submitLogoForm() {
            const form = document.getElementById('logo-form');
            if (!form.checkValidity()) {
                showToast('Completa todos los campos obligatorios.', true);
                return;
            }
            const formData = new FormData(form);

            fetch('./api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeLogoModal();
                    const line = formData.get('line');
                    const container = document.getElementById(`logo-row-${line}`);
                    
                    const newItem = document.createElement('div');
                    newItem.className = 'logo-admin-item';
                    newItem.setAttribute('data-src', data.logo.src);
                    newItem.setAttribute('data-alt', data.logo.alt);
                    
                    newItem.innerHTML = `
                        <div class="img-container">
                            <img src="../${data.logo.src.replace(/^\.\//, '')}" alt="${data.logo.alt}">
                        </div>
                        <input type="text" class="logo-width-input" placeholder="Ancho (ej. 75px)" value="" oninput="markLogoChanges()" style="width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; font-size: 0.7rem; text-align: center; color: #333; background: #fff; outline: none; margin-top: 4px;">
                        <div class="logo-controls">
                            <button class="btn-logo-ctrl delete" onclick="deleteLogo('${line}', '${data.logo.src}')"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="logo-nav-btns">
                            <button class="btn-logo-nav" onclick="moveLogo('${line}', '${data.logo.src}', 'left')"><i class="fas fa-chevron-left"></i></button>
                            <button class="btn-logo-nav" onclick="moveLogo('${line}', '${data.logo.src}', 'right')"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    `;
                    container.insertBefore(newItem, container.firstChild);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error en la conexión con el servidor.', true);
            });
        }

        function deleteLogo(line, src) {
            if (confirm('¿Estás seguro de que deseas quitar este logotipo de cliente?')) {
                const formData = new FormData();
                formData.append('action', 'delete_logo');
                formData.append('line', line);
                formData.append('src', src);

                fetch('./api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        const container = document.getElementById(`logo-row-${line}`);
                        const items = container.querySelectorAll('.logo-admin-item');
                        items.forEach(item => {
                            if (item.getAttribute('data-src') === src) {
                                item.remove();
                            }
                        });
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Error al conectar con el servidor.', true);
                });
            }
        }

        function moveLogo(line, src, direction) {
            const container = document.getElementById(`logo-row-${line}`);
            const items = Array.from(container.querySelectorAll('.logo-admin-item'));
            
            let itemIdx = -1;
            for (let i = 0; i < items.length; i++) {
                if (items[i].getAttribute('data-src') === src) {
                    itemIdx = i;
                    break;
                }
            }

            if (itemIdx === -1) return;

            if (direction === 'left' && itemIdx > 0) {
                container.insertBefore(items[itemIdx], items[itemIdx - 1]);
                markLogoChanges();
            } else if (direction === 'right' && itemIdx < items.length - 1) {
                container.insertBefore(items[itemIdx], items[itemIdx + 2] || null);
                markLogoChanges();
            }
        }

        function markLogoChanges() {
            hasUnsavedLogoChanges = true;
            document.getElementById('btn-save-order').style.display = 'block';
        }

        function saveLogosOrder() {
            const orderData = {
                line1: [],
                line2: [],
                line3: []
            };

            ['line1', 'line2', 'line3'].forEach(line => {
                const container = document.getElementById(`logo-row-${line}`);
                const items = container.querySelectorAll('.logo-admin-item');
                items.forEach(item => {
                    const widthVal = item.querySelector('.logo-width-input').value.trim();
                    const logoObj = {
                        src: item.getAttribute('data-src'),
                        alt: item.getAttribute('data-alt')
                    };
                    if (widthVal) {
                        logoObj.width = widthVal;
                    }
                    orderData[line].push(logoObj);
                });
            });

            const formData = new FormData();
            formData.append('action', 'reorder_logos');
            formData.append('order_data', JSON.stringify(orderData));

            fetch('./api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    hasUnsavedLogoChanges = false;
                    document.getElementById('btn-save-order').style.display = 'none';
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al guardar las posiciones en el servidor.', true);
            });
        }

        window.addEventListener('beforeunload', function (e) {
            if (hasUnsavedLogoChanges || hasUnsavedCategoryChanges) {
                e.preventDefault();
                e.returnValue = 'Tienes cambios de posición en logotipos o categorías sin guardar. ¿Seguro que deseas salir?';
            }
        });

        function openAddUserModal() {
            document.getElementById('user-form').reset();
            document.getElementById('user-modal').classList.add('open');
        }

        function closeUserModal() {
            document.getElementById('user-modal').classList.remove('open');
        }

        function submitUserForm() {
            const form = document.getElementById('user-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeUserModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error en la comunicación con el servidor.', true);
            });
        }

        function openResetPasswordModal(id, username) {
            document.getElementById('reset-password-form').reset();
            document.getElementById('reset-pass-user-id').value = id;
            document.getElementById('reset-pass-username').value = username;
            document.getElementById('reset-password-modal').classList.add('open');
        }

        function closeResetPasswordModal() {
            document.getElementById('reset-password-modal').classList.remove('open');
        }

        function submitResetPasswordForm() {
            const form = document.getElementById('reset-password-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeResetPasswordModal();
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error en la comunicación con el servidor.', true);
            });
        }

        function deleteUser(id, username) {
            if (!confirm(`¿Estás seguro de que deseas eliminar permanentemente al usuario "${username}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('id', id);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al intentar eliminar el usuario.', true);
            });
        }

        function resetUser2FA(id, username) {
            if (!confirm(`¿Deseas reiniciar la clave secreta 2FA para el usuario "${username}"? Tendrá que escanear un nuevo código QR en su próximo inicio de sesión.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'reset_2fa');
            formData.append('id', id);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al restablecer 2FA del usuario.', true);
            });
        }

        let hasUnsavedCategoryChanges = false;

        function suggestSlug(val) {
            if (document.getElementById('category-form-action').value === 'add_category') {
                const suggested = val.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
                    .replace(/[^a-z0-9\s-]/g, '')    // Quitar caracteres especiales
                    .trim()
                    .replace(/\s+/g, '-');            // Espacios a guiones
                document.getElementById('category-slug').value = suggested;
            }
        }

        function openAddCategoryModal() {
            document.getElementById('category-form').reset();
            document.getElementById('category-id').value = '';
            document.getElementById('category-form-action').value = 'add_category';
            document.getElementById('category-modal-title').textContent = 'Agregar Nueva Categoría';
            document.getElementById('category-modal').classList.add('open');
        }

        function openEditCategoryModal(cat) {
            document.getElementById('category-form').reset();
            document.getElementById('category-id').value = cat.id;
            document.getElementById('category-form-action').value = 'edit_category';
            document.getElementById('category-modal-title').textContent = 'Editar Categoría';

            document.getElementById('category-name').value = cat.name;
            document.getElementById('category-slug').value = cat.slug;
            document.getElementById('category-description').value = cat.description;
            document.getElementById('category-seo-title').value = cat.seo_title || '';
            document.getElementById('category-seo-desc').value = cat.seo_desc || '';
            document.getElementById('category-seo-keywords').value = cat.seo_keywords || '';

            document.getElementById('category-modal').classList.add('open');
        }

        function closeCategoryModal() {
            document.getElementById('category-modal').classList.remove('open');
        }

        function submitCategoryForm() {
            const form = document.getElementById('category-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeCategoryModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error en la comunicación con el servidor.', true);
            });
        }

        function deleteCategory(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar esta categoría? Esta acción es irreversible y requiere que la categoría no tenga ningún trabajo asociado.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_category');
            formData.append('id', id);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al intentar eliminar la categoría.', true);
            });
        }

        function moveCategory(id, direction) {
            const rows = Array.from(document.querySelectorAll('.category-row'));
            const index = rows.findIndex(row => parseInt(row.getAttribute('data-id')) === id);
            if (index === -1) return;

            const tbody = document.getElementById('categories-list-body');
            const currentRow = rows[index];

            if (direction === 'up' && index > 0) {
                tbody.insertBefore(currentRow, rows[index - 1]);
                markCategoryChanges();
            } else if (direction === 'down' && index < rows.length - 1) {
                tbody.insertBefore(currentRow, rows[index + 1].nextSibling);
                markCategoryChanges();
            }
        }

        function markCategoryChanges() {
            hasUnsavedCategoryChanges = true;
            document.getElementById('category-order-actions').style.display = 'flex';
        }

        function saveCategoryOrder() {
            const rows = Array.from(document.querySelectorAll('.category-row'));
            const orderData = rows.map(row => ({
                id: parseInt(row.getAttribute('data-id'))
            }));

            const formData = new FormData();
            formData.append('action', 'reorder_categories');
            formData.append('order_data', JSON.stringify(orderData));

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    hasUnsavedCategoryChanges = false;
                    document.getElementById('category-order-actions').style.display = 'none';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error al guardar el orden de las categorías.', true);
            });
        }
    </script>
</body>
</html>
