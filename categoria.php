<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SECURE_ACCESS', true);
$categories = require __DIR__ . '/data/categories.php';
$works = require __DIR__ . '/data/works.php';

$current_slug = $_GET['slug'] ?? '';

$active_category = null;
foreach ($categories as $cat) {
    if ($cat['slug'] === $current_slug) {
        $active_category = $cat;
        break;
    }
}

if (!$active_category) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$category = $active_category['slug'];
$filtered_works = array_filter($works, function($w) use ($category) {
    return $w['category'] === $category;
});
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./imagenes/logos/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($active_category['seo_title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($active_category['seo_desc']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($active_category['seo_keywords']); ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
    <meta name="author" content="Imagen Visual Uruguay">
    <link rel="canonical" href="https://www.imagenvisual.com.uy/<?php echo htmlspecialchars($active_category['slug']); ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_UY">
    <meta property="og:site_name" content="Imagen Visual">
    <meta property="og:title" content="<?php echo htmlspecialchars($active_category['seo_title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($active_category['seo_desc']); ?>">
    <meta property="og:image" content="https://www.imagenvisual.com.uy/imagenes/logos/favicon.png">
    <meta property="og:url" content="https://www.imagenvisual.com.uy/<?php echo htmlspecialchars($active_category['slug']); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($active_category['seo_title']); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($active_category['seo_desc']); ?>">
    <meta name="twitter:image" content="https://www.imagenvisual.com.uy/imagenes/logos/favicon.png">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://db.onlinewebfonts.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://db.onlinewebfonts.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/b60b9d7947c319b934d276c5cc819555?family=VW+Head+Light+Regular"
        rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/0d1062ff99c782bdbdd1ac3b47c577ba?family=VW+Text" rel="stylesheet"
        type="text/css" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-314709220"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'AW-314709220');
    </script>
    <style>
        /* 1. ESTILOS BASE Y GENERALES */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "VW Text", sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding-top: 70px;
        }

        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            font-weight: 700;
            padding-bottom: 10px;
            margin-top: 50px;
            margin-bottom: 40px;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #dc3545;
            border-radius: 2px;
        }

        .separador {
            border: 0;
            height: 1px;
            background-color: #dc3545;
            margin: 40px 0;
        }

        .whatsapp {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1000;
        }

        .whatsapp img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .whatsapp img:hover {
            transform: scale(1.1);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f0f0f0;
        }

        ::-webkit-scrollbar-thumb {
            background: #dc3545;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #99222e;
        }

        @media (max-width: 768px) {
            .whatsapp img {
                width: 50px;
                height: 50px;
            }
        }

        :root {
            --primary-color: #dc3545;
            --primary-dark: #99222e;
            --text-color: #333;
            --light-gray: #f8f8f8;
            --white: #ffffff;
            --dark-bg: rgba(39, 37, 40, 0.95);
        }

        /* 2. HEADER/NAVBAR */

        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px 20px;
            background-color: var(--dark-bg);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.4s ease;
        }

        .navbar.scrolled {
            background-color: var(--dark-bg);
            padding: 12px 20px;
        }

        .navbar-inner {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            width: 100%;
            padding: 0 20px;
            margin: 0 auto;
            transition: all 0.4s ease;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0;
            gap: 10px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            margin-bottom: 0;
            text-align: center;
        }

        .logo-area img {
            max-width: 400px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .navbar.scrolled .logo-area img {
            transform: scale(0.9);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            font-size: 0.9em;
            transition: all 0.3s;
            border-radius: 4px;
            font-weight: normal;
            font-family: "VW Head Light Regular", sans-serif;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .logo-area a:hover {
            background-color: transparent !important;
        }

        .menu-toggle {
            display: none;
            font-size: 1.2em;
            cursor: pointer;
            color: white;
            transition: all 0.3s;
            background: none;
            border: none;
            padding: 5px;
        }

        .navbar.scrolled .menu-toggle {
            font-size: 1.5em;
        }

        .dropdown {
            position: relative;
            display: flex;
            align-items: center;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(16, 16, 16, 0.95);
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            left: 0;
            top: 100%;
            border-radius: 0 0 5px 5px;
            overflow: hidden;
            backdrop-filter: blur(5px);
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            border-bottom: 1px solid #333;
            transition: background-color 0.3s;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: var(--primary-color);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .social-icons-navbar i {
            font-size: 1em;
            color: white;
            margin-left: 8px;
            transition: all 0.3s;
        }

        .navbar.scrolled .social-icons-navbar i {
            font-size: 1.2em;
            margin-left: 10px;
        }

        .social-icons-navbar i:hover {
            color: var(--primary-color);
        }

        /* 3. SECCIÓN GALERÍA */

        .gallery-container {
            padding: 40px 0;
        }

        .gallery-container h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
            font-family: "VW Head Light Regular", sans-serif;
        }

        .page-description {
            text-align: center;
            color: #666;
            max-width: 800px;
            margin: 0 auto 40px;
            font-size: 1.1em;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            width: 100%;
        }

        .gallery-item {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform 0.5s ease;
            display: block;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-caption {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-top: 1px solid #eee;
        }

        .gallery-caption h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1.25em;
            font-weight: 600;
        }

        .gallery-caption p {
            margin: 0;
            color: #666;
            font-size: 0.95em;
            line-height: 1.5;
        }

        .image-count-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* 4. LIGHTBOX MODAL */

        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 3000;
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(5px);
        }

        .lightbox-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 80%;
            max-height: 70vh;
            object-fit: contain;
            animation-name: zoom;
            animation-duration: 0.3s;
        }

        @keyframes zoom {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .lightbox-close {
            position: absolute;
            top: 25px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            line-height: 1;
        }

        .lightbox-close:hover,
        .lightbox-close:focus {
            color: #dc3545;
            text-decoration: none;
        }

        .lightbox-caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 800px;
            text-align: center;
            color: #ccc;
            padding: 15px 0;
            font-size: 1.1rem;
            margin-top: 15px;
            line-height: 1.6;
        }

        .lightbox-caption strong {
            color: #fff;
            font-size: 1.3rem;
        }

        /* Botones de navegación del Lightbox */
        .lightbox-prev,
        .lightbox-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            padding: 16px;
            cursor: pointer;
            user-select: none;
            transition: 0.3s;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            text-decoration: none;
            border: none;
        }

        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }

        .lightbox-prev:hover,
        .lightbox-next:hover {
            background-color: #dc3545;
        }

        .lightbox-position {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 6px 16px;
            border-radius: 20px;
        }

        /* 5. FOOTER */

        .main-footer {
            background-color: #f0f0f0;
            color: #666;
            margin-top: 50px;
            border-top: 1px solid #ddd;
        }

        .footer-content-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 15px;
        }

        .footer-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .footer-col .fw-bold {
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.1em;
            letter-spacing: 1px;
        }

        .footer-col a {
            color: #666;
            text-decoration: none;
            margin-bottom: 8px;
            font-size: 0.95em;
            transition: color 0.3s;
        }

        .footer-col a:hover {
            color: #dc3545;
        }

        .footer-col p {
            margin: 0 0 8px;
            color: #666;
            font-size: 0.95em;
            text-align: left;
        }

        .social-icons-footer {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            margin: 10px 0 0;
        }

        .social-icons-footer img {
            width: 30px;
            height: auto;
            vertical-align: middle;
        }

        .social-icons-footer a {
            display: inline-block;
            line-height: 0;
        }

        .footer-bottom {
            background-color: #e6e6e6;
            color: #6c757d;
            font-size: 0.8rem;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #ddd;
        }

        .footer-bottom strong {
            color: #333;
            font-weight: 600;
        }

        .footer-bottom a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-bottom a:hover {
            text-decoration: underline;
        }

        .footer-logo {
            vertical-align: middle;
            margin-top: -2px;
        }

        @media (max-width: 900px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
            }

            .footer-col {
                flex: 1 1 100%;
                text-align: center;
                align-items: center;
                margin-bottom: 20px;
            }

            .footer-col p,
            .footer-col a {
                text-align: center !important;
            }

            body {
                padding-top: 60px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 5px 20px;
                box-shadow: none;
                justify-content: space-between;
                transition: background-color 0.3s ease, backdrop-filter 0.3s ease;
            }

            .navbar.menu-active {
                background-color: rgba(16, 16, 16, 0.98) !important;
                backdrop-filter: blur(10px);
            }

            .navbar.scrolled {
                background-color: var(--dark-bg);
                padding: 10px 20px;
                backdrop-filter: blur(10px);
            }

            .navbar.menu-active.scrolled {
                box-shadow: none;
            }

            .navbar-inner {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            .logo-area {
                margin: 0;
                display: flex;
                justify-content: center;
                flex: 1;
            }

            .logo-area img {
                max-width: 150px;
            }

            .navbar .nav-links {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0;
                right: -100%;
                width: 70%;
                max-width: 300px;
                height: 100vh;
                background-color: rgba(16, 16, 16, 0.98);
                backdrop-filter: blur(10px);
                margin-top: 0;
                box-shadow: none;
                transition: right 0.3s ease-in-out;
                z-index: 1004;
                text-align: center;
                padding-top: 80px;
                justify-content: flex-start;
            }

            .navbar .nav-links.open {
                right: 0;
            }

            .navbar a {
                padding: 15px 25px;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                font-size: 1.1em;
                display: block;
            }

            .menu-toggle {
                display: block;
                width: 30px;
                height: 24px;
                cursor: pointer;
                background: none;
                border: none;
                padding: 0;
                order: 2;
                position: relative;
                z-index: 1005;
                transition: transform 0.3s ease;
            }

            .hamburger-line {
                display: block;
                width: 100%;
                height: 3px;
                background-color: var(--white);
                margin-bottom: 5px;
                transition: all 0.4s cubic-bezier(0.68, -0.6, 0.32, 1.6);
                border-radius: 3px;
            }

            .hamburger-line:last-child {
                margin-bottom: 0;
            }

            .menu-toggle.open .hamburger-line:nth-child(1) {
                transform: translateY(8px) rotate(45deg);
            }

            .menu-toggle.open .hamburger-line:nth-child(2) {
                opacity: 0;
            }

            .menu-toggle.open .hamburger-line:nth-child(3) {
                transform: translateY(-8px) rotate(-45deg);
            }

            .dropdown {
                width: 100%;
                position: static;
                display: block;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .dropdown .dropbtn {
                width: 100%;
                display: block;
                padding: 15px 25px;
                text-align: center;
            }

            .dropdown .dropbtn:focus {
                outline: none;
            }

            .dropdown-content {
                position: static;
                background-color: rgba(16, 16, 16, 0.5);
                width: 100%;
                box-shadow: none;
                border-radius: 0;
            }

            .dropdown-content a {
                padding: 10px 40px;
                background-color: transparent;
                border-bottom: none;
                font-size: 0.95em;
                text-align: center;
            }

            .dropdown-content {
                display: block;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease-in-out;
                background-color: rgba(16, 16, 16, 0.5);
            }

            .dropdown-content.show {
                max-height: 500px;
            }

            .dropdown .dropbtn::after {
                content: ' ▼';
                font-size: 0.8em;
                transition: transform 0.3s;
                display: inline-block;
            }

            .dropdown .dropbtn.open::after {
                transform: rotate(180deg);
            }

            .social-icons-navbar {
                display: none;
            }

            .lightbox-modal {
                padding-top: 60px;
            }
            .lightbox-content {
                width: 95%;
                max-width: 95%;
                margin-top: 40px;
                max-height: 65vh;
            }
            .lightbox-caption {
                width: 90%;
                padding: 10px 0;
                font-size: 0.95rem;
                margin-top: 10px;
            }
            .lightbox-caption strong {
                font-size: 1.1rem;
            }
            .lightbox-prev,
            .lightbox-next {
                position: fixed;
                top: 50%;
                width: 40px;
                height: 40px;
                font-size: 16px;
                margin: 0;
                z-index: 3002;
                background-color: rgba(0, 0, 0, 0.6);
                border: none;
            }
            .lightbox-prev { left: 10px; }
            .lightbox-next { right: 10px; }
            .lightbox-prev:hover,
            .lightbox-next:hover {
                background-color: #dc3545;
                transform: translateY(-50%);
            }
            .lightbox-position {
                top: 20px;
                font-size: 12px;
                background-color: rgba(0, 0, 0, 0.7);
                padding: 4px 12px;
            }
            .lightbox-close {
                position: fixed;
                top: 15px;
                right: 15px;
                font-size: 20px;
                background-color: rgba(0, 0, 0, 0.6);
                width: 40px;
                height: 40px;
                border: none;
            }
        }

        @media (max-width: 480px) {
            .lightbox-content {
                width: 98%;
                max-width: 98%;
                margin-top: 60px;
                max-height: 60vh;
            }
            .lightbox-prev,
            .lightbox-next {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
            .lightbox-prev { left: 5px; }
            .lightbox-next { right: 5px; }
            .lightbox-close {
                width: 35px;
                height: 35px;
                font-size: 18px;
                top: 15px;
                right: 15px;
            }
            .lightbox-position { top: 20px; }
        }

        @media (max-width: 600px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            .gallery-item img {
                height: 250px;
            }
        }

        ::-webkit-scrollbar {
            width: 0;
            height: 0;
            background: transparent;
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="navbar-inner">
            <div class="logo-area">
                <a href="./"><img src="./imagenes/logos/logo.png" alt="Imagen Visual Logo"></a>
            </div>
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            <nav class="nav-links" id="navLinks">
                <a href="./" style="font-weight: bold;">Inicio</a>
                <div class="dropdown">
                    <a href="#" class="dropbtn" style="font-weight: bold;">Productos</a>
                    <div class="dropdown-content">
                        <?php foreach ($categories as $cat): ?>
                            <a href="./<?php echo htmlspecialchars($cat['slug']); ?>" <?php echo ($category === $cat['slug'] ? 'class="active-link"' : ''); ?> style="font-weight: bold;"><?php echo htmlspecialchars($cat['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="./#nosotros" style="font-weight: bold;">Nosotros</a>
                <a href="./#contacto" style="font-weight: bold;">Contacto</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="gallery-container">
            <div class="content-wrapper">
                <h1><?php echo htmlspecialchars($active_category['name']); ?></h1>
                <p class="page-description">
                    <?php echo htmlspecialchars($active_category['description']); ?>
                </p>
                <div class="gallery-grid">
                    <?php foreach ($filtered_works as $w): 
                        $main_image = $w['images'][0] ?? '';
                        $data_images_arr = [];
                        foreach ($w['images'] as $img) {
                            $data_images_arr[] = [
                                'src' => $img,
                                'alt' => $w['title']
                            ];
                        }
                        $data_images_json = json_encode($data_images_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $data_images_attr = count($w['images']) > 1 ? " data-images='" . htmlspecialchars($data_images_json, ENT_QUOTES, 'UTF-8') . "'" : "";
                    ?>
                    <div class="gallery-item"<?php echo $data_images_attr; ?>>
                        <img src="<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($w['title']); ?>" loading="lazy">
                        <?php if (count($w['images']) > 1): ?>
                            <div class="image-count-indicator"><?php echo count($w['images']); ?> fotos</div>
                        <?php endif; ?>
                        <div class="gallery-caption">
                            <h4><?php echo htmlspecialchars($w['title']); ?></h4>
                            <p><?php echo htmlspecialchars($w['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="imageModal" class="lightbox-modal">
        <span class="lightbox-close">&times;</span>
        <button class="lightbox-prev" id="lightboxPrevBtn">&#10094;</button>
        <button class="lightbox-next" id="lightboxNextBtn">&#10095;</button>
        <div class="lightbox-position" id="lightboxPositionIndicator">1/1</div>
        <img class="lightbox-content" id="modalImage">
        <div id="modalCaption" class="lightbox-caption"></div>
    </div>

    <footer class="main-footer">
        <div class="footer-content-wrapper">
            <div class="footer-col" id="direccion-col">
                <span class="fw-bold">DIRECCIÓN</span>
                <p>Cnel. Justo Aréchaga 3294</p>
                <p>esq. Av. Luis A. de Herrera,</p>
                <p style="margin-bottom: 10px;">Montevideo, Uruguay</p>
            </div>
            <div class="footer-col" id="direccion-col">
                <span class="fw-bold">CONTACTO</span>

                <p>
                    <i class="fa-solid fa-envelope"></i>
                    <a href="mailto:info@imagenvisual.com.uy">info@imagenvisual.com.uy</a>
                </p>

                <p style="margin-bottom: 10px;">
                    <i class="fa-solid fa-phone"></i>
                    <a href="tel:+59822025656">(+598) 2202 5656</a>
                </p>
            </div>
            <div class="footer-col">
                <span class="fw-bold">SÍGUENOS</span>
                <div class="social-icons-footer">
                    <a href="https://www.facebook.com/imagenvisualuruguay" target="_blank"><img
                            src="./imagenes/logos/footer-facebook.png" alt="Facebook"></a>
                    <a href="https://www.instagram.com/imagenvisualuruguay/" target="_blank"><img
                            src="./imagenes/logos/footer-instagram.png" alt="Instagram"></a>
                    <a href="https://www.linkedin.com/company/imagen-visual-uruguay" target="_blank"><img
                            src="./imagenes/logos/footer-linkedin.png" alt="LinkedIn"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            © <span id="currentYear">2025</span> <strong>Imagen Visual</strong>. Todos los derechos reservados. | Diseño
            y Desarrollo por:<a href="https://tadeoalvarez.dev"><img src="./imagenes/logos/TadeDev.png"
                    alt="Logo Tadeo Alvarez" class="footer-logo" width="40"></a>
        </div>
        <div class="whatsapp">
            <a href="https://wa.me/59892139068" target="_blank"><img src="./imagenes/logos/footer-whatsapp.png"
                    alt="WhatsApp"></a>
        </div>
    </footer>

    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        (function() {
            const navbar = document.querySelector('.navbar');
            if (!navbar) return;
            let ticking = false;

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        if (window.scrollY > 50) {
                            navbar.classList.add('scrolled');
                        } else {
                            navbar.classList.remove('scrolled');
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        })();

        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        const navbar = document.querySelector('.navbar');

        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', function () {
                navLinks.classList.toggle('open');
                menuToggle.classList.toggle('open');
                navbar.classList.toggle('menu-active');
            });
        }

        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 768) {
                const isClickInsideNav = navLinks.contains(event.target) || menuToggle.contains(event.target);
                if (!isClickInsideNav && navLinks.classList.contains('open')) {
                    navLinks.classList.remove('open');
                    menuToggle.classList.remove('open');
                    navbar.classList.remove('menu-active');
                }
            }
        });

        const dropbtn = document.querySelector('.dropbtn');
        const dropdownContent = document.querySelector('.dropdown-content');

        if (dropbtn && dropdownContent) {
            dropbtn.addEventListener('click', function (e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    dropdownContent.classList.toggle('show');
                    dropbtn.classList.toggle('open');
                }
            });
        }

        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        const captionText = document.getElementById("modalCaption");
        const span = document.getElementsByClassName("lightbox-close")[0];
        const prevBtn = document.getElementById("lightboxPrevBtn");
        const nextBtn = document.getElementById("lightboxNextBtn");
        const positionIndicator = document.getElementById("lightboxPositionIndicator");

        let currentGroup = [];
        let currentIndex = 0;

        function getImagesFromGalleryItem(galleryItem) {
            const images = [];
            const caption = galleryItem.querySelector('.gallery-caption h4').textContent;
            const description = galleryItem.querySelector('.gallery-caption p').textContent;

            const dataImagesAttr = galleryItem.getAttribute('data-images');
            if (dataImagesAttr) {
                try {
                    const parsed = JSON.parse(dataImagesAttr);
                    parsed.forEach(img => {
                        images.push({
                            src: img.src,
                            alt: img.alt,
                            caption: caption,
                            description: description
                        });
                    });
                } catch (e) {
                    console.error("Error parsing data-images", e);
                }
            } else {
                const img = galleryItem.querySelector('img');
                if (img) {
                    images.push({
                        src: img.src,
                        alt: img.alt,
                        caption: caption,
                        description: description
                    });
                }
            }
            return images;
        }

        function openModal() {
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        function updateModal() {
            if (currentGroup.length === 0) return;
            const currentImage = currentGroup[currentIndex];
            modalImg.src = currentImage.src;
            modalImg.alt = currentImage.alt;
            captionText.innerHTML = `<strong>${currentImage.caption}</strong><br>${currentImage.description}`;
            positionIndicator.textContent = `${currentIndex + 1}/${currentGroup.length}`;
            
            if (currentGroup.length > 1) {
                prevBtn.style.display = "block";
                nextBtn.style.display = "block";
                positionIndicator.style.display = "block";
            } else {
                prevBtn.style.display = "none";
                nextBtn.style.display = "none";
                positionIndicator.style.display = "none";
            }
        }

        function closeModal() {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        }

        document.querySelectorAll('.gallery-item').forEach(galleryItem => {
            galleryItem.addEventListener('click', function () {
                currentGroup = getImagesFromGalleryItem(this);
                if (currentGroup.length === 0) return;
                currentIndex = 0;
                openModal();
                updateModal();
            });
        });

        if (span) span.addEventListener('click', closeModal);
        if (prevBtn) prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + currentGroup.length) % currentGroup.length;
            updateModal();
        });
        if (nextBtn) nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % currentGroup.length;
            updateModal();
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal || event.target === modalImg.parentNode) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (modal.style.display === "block") {
                if (event.key === "ArrowLeft") {
                    currentIndex = (currentIndex - 1 + currentGroup.length) % currentGroup.length;
                    updateModal();
                } else if (event.key === "ArrowRight") {
                    currentIndex = (currentIndex + 1) % currentGroup.length;
                    updateModal();
                } else if (event.key === "Escape") {
                    closeModal();
                }
            }
        });
    </script>
</body>

</html>
