<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SECURE_ACCESS', true);
$clients = require __DIR__ . '/data/clients.php';
$categories = require __DIR__ . '/data/categories.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="wR7cGgO62lVlgatD2xaewK8IA2fJIW6gvC4gkmSumEc" />
    <title>Imagen Visual - Inicio | Cartelería y Comunicación Visual Uruguay</title>
    <link rel="preconnect" href="https://db.onlinewebfonts.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://db.onlinewebfonts.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="shortcut icon" href="./imagenes/logos/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="description"
        content="Imagen Visual - Más de 30 años de experiencia en cartelería, carpintería comercial, toldos y gráfica vehicular en Uruguay. Soluciones visuales integrales.">
    <meta name="keywords"
        content="cartelería en Uruguay, carpintería comercial, toldos, gráfica vehicular, ploteos, exhibidores, imagen visual, letreros, cartelería montevideo">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.imagenvisual.com.uy/">
    <meta property="og:title" content="Imagen Visual - Comunicación Visual en Uruguay">
    <meta property="og:description"
        content="Más de 30 años de experiencia en cartelería, carpintería comercial y gráfica vehicular">
    <meta property="og:image" content="./imagenes/logos/favicon.png">
    <meta property="og:url" content="https://www.imagenvisual.com.uy">
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
        gtag('event', 'conversion', { 'send_to': 'AW-314709220/WwgeCKb00-0CEOSpiJYB' });
    </script>
    <style>
        /* 1. ESTILOS BASE Y GENERALES */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #dc3545;
            --primary-dark: #99222e;
            --text-color: #333;
            --light-gray: #f8f8f8;
            --white: #ffffff;
            --dark-bg: rgba(39, 37, 40, 0.95);
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: "VW Text", sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-gray);
            padding-top: 0;
            overflow-x: hidden;
        }

        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        h2.section-title {
            text-align: center;
            color: #444;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-top: 40px;
            margin-bottom: 30px;
            font-size: clamp(1.5rem, 4vw, 2rem);
        }

        .h2-no-border {
            border-bottom: none !important;
            margin-bottom: 20px !important;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .filosofia-title {
            color: var(--primary-dark);
            font-weight: bold;
            text-align: left !important;
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            margin-top: 25px;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .separador {
            border: 0;
            height: 1px;
            background-color: var(--primary-color);
            margin: 40px 0;
        }

        /* 2. HEADER/NAVBAR */

        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px 20px;
            background-color: transparent;
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

        /* 3. HERO SECTION */

        .hero-section {
            width: 100%;
            height: 100svh;
            position: relative;
            overflow: hidden;
            background-color: #000;
        }

        .hero-slider-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            transition: opacity 0.8s ease;
            cursor: grab;
        }

        .hero-slider-container:active {
            cursor: grabbing;
        }

        .hero-slider-container.fade-out {
            opacity: 0;
        }

        .slider-track {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.6s ease-in-out;
        }

        .slide-item {
            min-width: 100%;
            width: 100%;
            height: 100%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #000;
            overflow: hidden;
        }

        .slide-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 6s ease, opacity 0.5s ease;
        }

        .slide-item.active img {
            transform: scale(1.15);
        }

        .slider-track .slide-item:nth-child(6) img,
        .slider-track .slide-item:nth-child(9) img {
            object-fit: contain;
            background-color: #000;
        }

        .slider-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            font-size: clamp(1.5rem, 4vw, 2rem);
            padding: 10px 15px;
            cursor: pointer;
            z-index: 10;
            line-height: 1;
            transition: background 0.3s;
        }

        .slider-control:hover {
            background: var(--primary-color);
        }

        .slider-control.prev {
            left: 0;
            border-radius: 0 5px 5px 0;
        }

        .slider-control.next {
            right: 0;
            border-radius: 5px 0 0 5px;
        }

        .slider-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .dot.active {
            background: var(--white);
            transform: scale(1.2);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .intro-text-section {
            padding: 50px 0;
            background-color: var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #eee;
        }

        .intro-text {
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
        }

        .intro-text p {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            line-height: 1.8;
            color: #444;
            font-weight: 300;
            margin-bottom: 15px;
        }

        /* 4. SECCIÓN SERVICIOS */

        .services-section {
            position: relative;
            margin-bottom: 40px;
        }

        .services-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            overflow-x: auto;
            padding: 10px 0 20px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) #f0f0f0;
            -webkit-overflow-scrolling: touch;
        }

        .services-container::-webkit-scrollbar {
            height: 8px;
        }

        .services-container::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }

        .services-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .services-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        .service-card {
            flex: 0 0 280px;
            background-color: var(--white);
            color: #333;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .service-card h3 {
            color: var(--primary-color);
            margin-top: 0;
            font-size: clamp(1rem, 2.5vw, 1.2rem);
        }

        .service-card p {
            flex-grow: 1;
            font-size: 0.9em;
            text-align: center;
        }

        .info-button {
            color: white;
            background-color: var(--primary-color);
            text-decoration: none;
            border: 1px solid var(--primary-color);
            padding: 8px 15px;
            display: inline-block;
            margin-top: 10px;
            transition: all 0.3s;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .info-button:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }

        .scroll-indicator {
            display: none;
            text-align: center;
            margin-top: 10px;
            color: var(--primary-color);
            font-size: 0.9em;
        }

        .scroll-indicator i {
            margin: 0 5px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateX(0);
            }

            40% {
                transform: translateX(-5px);
            }

            60% {
                transform: translateX(-3px);
            }
        }

        /* 5. SECCIÓN CARACTERÍSTICAS */
        .features-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: center;
            margin: 40px 0;
        }

        .feature-item {
            padding: 0 10px;
        }

        .feature-item h3 {
            color: var(--primary-color);
            font-size: clamp(1rem, 2.5vw, 1.2rem);
        }

        .feature-item img {
            margin-bottom: 15px;
            max-width: 90px;
            height: auto;
        }

        /* 6. SECCIÓN EQUIPO */

        .team-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            margin-bottom: 40px;
            width: 100%;
        }

        .team-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width: 100%;
        }

        .team-row.top {
            justify-content: center;
        }

        .team-row.bottom {
            justify-content: center;
            max-width: 760px;
            margin: 0 auto;
        }

        .team-member {
            flex: 1 1 280px;
            max-width: 320px;
            padding: 25px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: var(--white);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .team-member h3 {
            color: var(--primary-color);
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .team-member p {
            font-size: 0.95em;
            text-align: center;
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .team-member a {
            color: #333;
            text-decoration: none;
            padding: 0;
            transition: color 0.3s;
            font-size: 0.9em;
        }

        .team-member a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .team-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: var(--primary-color);
            font-size: 2em;
        }

        /* 7. CARRUSEL DE CLIENTES */

        .multi-line-carousel-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            margin: 40px 0;
        }

        .carousel-line {
            position: relative;
            overflow: hidden;
            height: 120px;
            margin-bottom: 10px;
        }

        .carousel-track {
            display: flex;
            position: absolute;
            transition: transform 0.5s ease-in-out;
            width: max-content;
        }

        .logo-item {
            min-width: 200px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            box-sizing: border-box;
            filter: grayscale(100%);
            transition: filter 0.5s ease, transform 0.3s;
            cursor: pointer;
        }

        .logo-item:hover {
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        .logo-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        @media (max-width: 1024px) {
            .carousel-line {
                height: 100px;
            }

            .logo-item {
                min-width: 180px;
                height: 80px;
            }
        }

        @media (max-width: 768px) {
            .carousel-line {
                height: 90px;
            }

            .logo-item {
                min-width: 160px;
                height: 80px;
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            .carousel-line {
                height: 85px;
            }

            .logo-item {
                min-width: 33.33vw;
                height: 75px;
                padding: 6px;
            }
        }

        @media (max-width: 360px) {
            .carousel-line {
                height: 80px;
            }

            .logo-item {
                min-width: 33.33vw;
                height: 70px;
                padding: 4px;
            }
        }

        /* 8. SECCIÓN CONTACTO */

        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 50px;
        }

        .contact-section {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background-color: var(--white);
            border: 1px solid #e0e0e0;
        }

        .message-pill {
            order: 1;
        }

        .info-pill {
            order: 2;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .lead-contact {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 20px;
        }

        .info-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .info-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-pill h3 {
            color: #333;
            font-weight: bold;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-top: 0;
        }

        .info-pill h4 {
            color: var(--primary-color);
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 0;
            min-width: 120px;
            text-align: left;
        }

        .info-pill p {
            margin: 0;
            text-align: left;
            color: #555;
        }

        .contact-link {
            color: #333 !important;
            text-decoration: none !important;
            font-weight: normal;
        }

        .contact-link:hover {
            text-decoration: underline !important;
            color: var(--primary-color) !important;
        }

        .social-icons-contact {
            display: flex;
            margin-top: 10px;
        }

        .social-icons-contact i {
            font-size: 1.5em;
            color: var(--primary-color);
            margin-right: 15px;
            transition: color 0.3s;
        }

        .social-icons-contact i:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }

        .form-group .required {
            color: var(--primary-color);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s;
            font-family: Arial, Helvetica, sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .submit-area {
            text-align: center;
            margin-top: 20px;
        }

        .contact-button {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            font-size: 1.1em;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
            border-radius: 5px;
            border: none;
            transition: background-color 0.3s;
            width: 100%;
            max-width: 200px;
        }

        .contact-button:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }

        /* 9. FOOTER */

        .main-footer {
            background-color: var(--white);
            color: #444;
            padding-top: 30px;
            font-size: 0.9em;
            border-top: 1px solid #ddd;
        }

        .footer-content-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px 30px;
        }

        .footer-col {
            padding: 15px;
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .footer-col .fw-bold {
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .footer-col a {
            color: #666;
            text-decoration: none;
            margin-bottom: 5px;
            font-size: 0.95em;
        }

        .footer-col a:hover {
            color: var(--primary-color);
        }

        .footer-col p {
            margin: 0 0 5px;
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

        .footer-map {
            padding: 15px;
        }

        .footer-map h3 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .footer-map-container {
            border-radius: 8px;
            overflow: hidden;
            height: 200px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer-map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .footer-bottom {
            background-color: #f0f0f0;
            color: #6c757d;
            font-size: 0.75rem;
            padding: 10px 15px;
            text-align: center;
        }

        .footer-bottom strong {
            color: #333;
            font-weight: bold;
        }

        .footer-bottom a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .footer-bottom a:hover {
            text-decoration: underline;
        }

        .footer-logo {
            vertical-align: middle;
            margin-top: -2px;
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

        /* 10. MEDIA QUERIES */


        @media (max-width: 1024px) {
            .content-wrapper {
                padding: 0 20px;
            }
        }

        @media (max-width: 1366px) and (min-width: 1025px) {
            .navbar {
                padding: 3px 15px;
            }

            .navbar.scrolled {
                padding: 8px 15px;
            }

            .logo-area img {
                max-width: 150px;
            }

            .navbar a {
                padding: 6px 10px;
                font-size: 0.85em;
            }

            .navbar-inner {
                max-width: 100%;
                padding: 0 20px;
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

            .contact-container {
                flex-direction: column;
            }

            .contact-section {
                width: 100%;
            }

            .footer-content-wrapper {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-col {
                align-items: center;
            }

            .filosofia-chico {
                font-size: 20px;
            }

            .social-icons-footer {
                justify-content: center;
            }

            .hero-section {
                height: 100svh;
                position: relative;
            }

            .hero-section::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40%;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
                pointer-events: none;
                z-index: 5;
            }

            .slider-control {
                padding: 12px 8px;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                font-size: 1rem;
                display: none;
            }

            @media (min-width: 481px) {
                .slider-control {
                    display: block;
                }
            }

            .slide-item img {
                object-position: center;
                object-fit: contain;
                width: 100%;
                height: 100%;
                transform: scale(1);
            }

            .slide-item.active img {
                transform: scale(1.1);
            }

            .slider-dots {
                bottom: 25px;
                background: rgba(255, 255, 255, 0.1);
                padding: 8px 15px;
                border-radius: 20px;
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                z-index: 10;
            }

            .dot {
                width: 10px;
                height: 10px;
                background: rgba(255, 255, 255, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .dot.active {
                width: 25px;
                border-radius: 10px;
                background: var(--white);
                box-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
            }

            .scroll-indicator {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .content-wrapper {
                padding: 0 15px;
            }

            h2 {
                margin-top: 30px;
                margin-bottom: 20px;
            }

            .intro-text-section {
                padding: 30px 0;
            }

            .service-card,
            .team-member {
                padding: 15px;
            }

            .features-container,
            .team-container {
                gap: 15px;
            }

            .intro-text .filosofia-chico {
                font-size: 20px;
            }

            .contact-section {
                padding: 20px;
            }

            .whatsapp img {
                width: 50px;
                height: 50px;
            }

            .footer-col,
            .footer-map {
                padding: 10px;
            }
        }

        /* 11. ESTILOS ADICIONALES */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f0f0f0;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        .negrita-rojo {
            color: var(--primary-dark);
            font-weight: bold;
        }

        .negrita-negro {
            font-weight: bold;
        }

        #nosotros,
        #contacto {
            scroll-margin-top: 120px;
        }

        @media (max-width: 768px) {

            #nosotros,
            #contacto {
                scroll-margin-top: 80px;
            }
        }

        .centrar-nosotros {
            text-align: center;
        }

        .filosofia-chico {
            color: var(--primary-dark);
            font-weight: bold;
            font-size: 26px;
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="navbar-inner">
            <div class="logo-area">
                <a href="./"><img src="./imagenes/logos/logo.png" alt="Imagen Visual Logo"></a>
            </div>
            <nav class="nav-links" id="navLinks">
                <a href="./" class="active-link" style="font-weight: bold;">Inicio</a>
                <div class="dropdown">
                    <a href="#" class="dropbtn" style="font-weight: bold;">Productos</a>
                    <div class="dropdown-content">
                        <?php foreach ($categories as $cat): ?>
                            <a href="./<?php echo htmlspecialchars($cat['slug']); ?>" style="font-weight: bold;"><?php echo htmlspecialchars($cat['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="./#nosotros" style="font-weight: bold;">Nosotros</a>
                <a href="./#contacto" style="font-weight: bold;">Contacto</a>
            </nav>
        </div>
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </header>

    <main>
        <h1 class="visually-hidden">Imagen Visual - Comunicación Visual y Publicidad en Uruguay</h1>
        <a id="inicio"></a>
        <div class="hero-section">
            <div class="hero-slider-container" id="heroSliderContainer">
                <div class="slider-track" id="heroSliderTrack">
                    <div class="slide-item"><img src="./imagenes/inicio/1.webp" alt="Cartelería" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/3.webp" alt="Carpintería" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/4.webp" alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/5.webp" alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/7.webp" alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/Pigalle.webp" alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img
                            src="./imagenes/inicio/CARTELERIA FARMACITY ALUMINIO COMPUESTO AV. BRASIL Y CHUCARRO NOCHE.webp"
                            alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img src="./imagenes/inicio/Frontlight Medica Uruguaya Av. Italia C.webp"
                            alt="Toldos" loading="lazy"></div>
                    <div class="slide-item"><img
                            src="./imagenes/inicio/Mesa Grande Motorola Antel Nuevocentro Shopping.webp" alt="Toldos"
                            loading="lazy"></div>
                </div>
                <button class="slider-control prev" data-direction="prev" aria-label="Imagen anterior">❮</button>
                <button class="slider-control next" data-direction="next" aria-label="Imagen siguiente">❯</button>
                <div class="slider-dots" id="sliderDots"></div>
            </div>
        </div>

        <div class="separador"></div>
        <a id="nosotros"></a>
        <div class="content-wrapper">
            <div class="intro-text">
                <h2 class="section-title">Nosotros</h2>
                <bra>
                    <p class="centrar-nosotros">En <strong class="negrita-rojo">Imagen Visual</strong>, contamos con una
                        sólida trayectoria de más de tres décadas de experiencia en el sector, especializándonos en la
                        ejecución integral de proyectos de comunicación visual, abarcando desde la cartelería hasta la
                        carpintería comercial.</p>
                    <p class="centrar-nosotros">Desde nuestros inicios, nuestro objetivo ha sido fusionar la innovación
                        tecnológica con la precisión artesanal para transformar las ideas de nuestros clientes en
                        realidades visuales de alto impacto.</p>
                    <br>
                    <h3 class="filosofia-chico" style="text-align: center;">Nuestra Filosofía</h3>
                    <br>
                    <p class="centrar-nosotros">Nuestro compromiso fundamental es transformar las ideas de nuestros
                        clientes en realidades tangibles y visualmente impactantes.</p>
                    <p class="centrar-nosotros">Gracias a un sistema de trabajo integrado, garantizamos un servicio que
                        se distingue por el trato cercano y personalizado, el máximo detalle en cada fase de producción
                        y la máxima fidelidad en la ejecución de su proyecto, asegurando que el resultado final supere
                        las expectativas.</p>
            </div>
        </div>

        <div class="content-wrapper">
            <hr class="separador">

            <h2 class="section-title">Productos</h2>
            <div class="services-section">
                <div class="services-container">
                    <?php foreach ($categories as $cat): ?>
                    <div class="service-card">
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p><?php echo htmlspecialchars($cat['description']); ?></p>
                        <a href="./<?php echo htmlspecialchars($cat['slug']); ?>" class="info-button">MÁS INFO</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="scroll-indicator">
                    <i class="fas fa-arrow-left"></i> Desliza para ver más servicios <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <hr class="separador">

            <div class="features-container">
                <div class="feature-item">
                    <img src="./imagenes/logos/icono_1.png" height="90" alt="Icono Presupuestos"><br>
                    <h3 class="negrita-rojo">Presupuestos antes de 72 hs</h3>
                    <p>Creemos firmemente en la rapidez y eficacia de nuestra respuesta hacia el cliente.</p>
                </div>
                <div class="feature-item">
                    <img src="./imagenes/logos/icono_2.png" height="90" alt="Icono Asesoramiento"><br>
                    <h3 class="negrita-rojo">Asesoramiento personalizado</h3>
                    <p>Nos involucramos completamente en cada proyecto, capturando y desarrollando la idea inicial del
                        cliente para garantizar los mejores resultados.</p>
                </div>
                <div class="feature-item">
                    <img src="./imagenes/logos/icono_3.png" height="90" alt="Icono Trabajos a medida"><br>
                    <h3 class="negrita-rojo">Trabajos a medida</h3>
                    <p>Desarrollamos trabajos que se adaptan con precisión a los requerimientos y las necesidades
                        específicas de su proyecto.</p>
                </div>
            </div>

            <hr class="separador">

            <h2 class="section-title">Nuestro Equipo</h2>
            <div class="team-container">
                <div class="team-row top">
                    <div class="team-member">
                        <h3>Gabriel Carlotto | Director</h3>
                        <p>
                            <a href="mailto:gabriel.carlotto@imagenvisual.com.uy">gabriel.carlotto@imagenvisual.com.uy</a><br>
                            <a href="tel:+59822025656">(+598) 2202 5656</a>
                        </p>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Responsable de la gestión, planificación y coordinación operativa de los proyectos.
                        </p>
                    </div>
                    <div class="team-member">
                        <h3>Mikaela Olivar | Gerencia - Administración</h3>
                        <p>
                            <a href="mailto:mikaela.olivar@imagenvisual.com.uy">mikaela.olivar@imagenvisual.com.uy</a><br>
                            <a href="tel:+59892139068">(+598) 92 139 068</a>
                        </p>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Responsable de la dirección ejecutiva de los procesos administrativos y del desarrollo de la
                            estrategia de fidelización y relacionamiento con el cliente.
                        </p>
                    </div>
                    <div class="team-member">
                        <h3>Emiliano Horacio | Diseñador gráfico</h3>
                        <p>
                            <a href="mailto:emiliano@imagenvisual.com.uy">emiliano@imagenvisual.com.uy</a><br>
                            <a href="tel:+59892257854">(+598) 92 257 854</a>
                        </p>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Especialista en diseño creativo y desarrollo de soluciones visuales.
                        </p>
                    </div>
                </div>
                <div class="team-row bottom">
                    <div class="team-member">
                        <h3>Andrés Martínez | Producción</h3>
                        <p>
                            <a href="tel:+5987464697">(+598) 97 464 697</a>
                        </p>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Responsable de la ejecución técnica y coordinación operativa de proyectos de cartelería. Especialista en la materialización y calidad de soluciones gráficas.
                        </p>
                    </div>
                    <div class="team-member">
                        <h3>Héctor Araujo | Ventas</h3>
                        <p>
                            <a href="mailto:hector.araujo@imagenvisual.com.uy">hector.araujo@imagenvisual.com.uy</a><br>
                            <a href="tel:+5981958020">(+598)  91 958 020</a>
                        </p>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Gestión comercial y asesoramiento integral. Enfocado en identificar necesidades y brindar soluciones efectivas para potenciar la marca de cada cliente.
                        </p>
                    </div>
                </div>
            </div>

            <hr class="separador">

            <h2 class="h2-no-border">Confían en nosotros</h2>

            <div class="multi-line-carousel-container">
            <?php
            $directions = ['right', 'left', 'right'];
            $lines = ['line1', 'line2', 'line3'];
            for ($i = 0; $i < 3; $i++):
                $line_key = $lines[$i];
                $direction = $directions[$i];
                $line_logos = $clients[$line_key] ?? [];
            ?>
            <div class="carousel-line">
                <div class="carousel-track" data-direction="<?php echo $direction; ?>">
                    <?php foreach ($line_logos as $logo): 
                        $logo_width = '';
                        if (isset($logo['width']) && !empty($logo['width'])) {
                            $w = trim($logo['width']);
                            $logo_width = is_numeric($w) ? $w . 'px' : $w;
                        }
                        $style_attr = !empty($logo_width) ? ' style="width: ' . htmlspecialchars($logo_width) . '; max-width: ' . htmlspecialchars($logo_width) . ';"' : '';
                    ?>
                    <div class="logo-item" data-client="<?php echo htmlspecialchars($logo['alt']); ?>">
                        <img src="<?php echo htmlspecialchars($logo['src']); ?>" alt="<?php echo htmlspecialchars($logo['alt']); ?>"<?php echo $style_attr; ?>>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endfor; ?>
        </div>

            <div class="whatsapp">
                <a href="https://wa.me/59892139068" target="_blank"><img src="./imagenes/logos/footer-whatsapp.png"
                        alt="WhatsApp"></a>
            </div>

            <hr class="separador">

            <a id="contacto"></a>
            <h2 class="section-title">Comuníquese con nosotros</h2>

            <div class="contact-container">
                <section class="contact-section message-pill"
                    style="box-shadow: none; border: none; padding: 0; background-color: transparent;">
                    <p style="font-size: 1.2em; font-weight: bold; color: #444; margin-bottom: 25px;">
                        ¿Desea iniciar una consulta? Por favor, complete los campos requeridos a continuación y nos
                        pondremos en contacto con usted a la brevedad.
                    </p>

                    <form name="contactForm" id="contactForm" style="padding: 0;">

                        <div class="form-group">
                            <input type="text" id="name" name="name" required placeholder="Nombre">
                        </div>

                        <div class="form-group">
                            <input type="email" id="email" name="email" required placeholder="Correo">
                        </div>

                        <div class="form-group">
                            <input type="tel" id="phone" name="phone" required placeholder="Número de teléfono">
                        </div>

                        <div class="form-group">
                            <textarea id="message" name="message" rows="4" required placeholder="Consulta"
                                style="resize: vertical;"></textarea>
                        </div>

                        <div class="form-group submit-area" style="text-align: left; margin-top: 5px;">
                            <button type="submit" id="submitButton" style="
                                background-color: #dc3545; 
                                border: none; 
                                color: white; 
                                padding: 8px 30px; 
                                font-size: 1em; 
                                cursor: pointer; 
                                border-radius: 4px; 
                                font-weight: normal; 
                                text-transform: none; 
                                width: auto; 
                                transition: background-color 0.3s;
                            ">Enviar</button>
                        </div>

                        <div id="formStatus" style="margin-top: 15px; font-weight: bold;"></div>
                    </form>
                </section>

                <section class="contact-section info-pill" style="
                    flex: 1; 
                    min-width: 300px; 
                    padding: 0; 
                    border-radius: 10px; 
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
                    background-color: white;
                    display: block; 
                    height: 480px;
                    overflow: hidden;
                ">
                    <iframe loading="lazy" style="border: 0; border-radius: 10px; width: 100%; height: 100%;"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3272.2235306871147!2d-56.15570532439369!3d-34.89886407289944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x959f816b6680a651%3A0x1d378cc619163e80!2sCnel.%20Justo%20Ar%C3%A9chaga%203294%2C%2011700%20Montevideo%2C%20Departamento%20de%20Montevideo!5e0!3m2!1ses-419!2suy!4v1715875225381!5m2!1ses-419!2suy"
                        allowfullscreen="" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </section>
            </div>
        </div>
    </main>

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
    </footer>

    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        const heroSliderTrack = document.getElementById('heroSliderTrack');
        const sliderDots = document.getElementById('sliderDots');
        const slides = document.querySelectorAll('.hero-slider-container .slide-item');
        const slideCount = slides.length;
        let currentSlide = 0;
        let startX = 0;
        let isDragging = false;

        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateHeroSlider();
                resetInterval();
            });
            sliderDots.appendChild(dot);
        });

        document.querySelectorAll('.hero-slider-container .slider-control').forEach(button => {
            button.addEventListener('click', () => {
                const direction = button.getAttribute('data-direction');

                if (direction === 'next') {
                    currentSlide = (currentSlide + 1) % slideCount;
                } else {
                    currentSlide = (currentSlide - 1 + slideCount) % slideCount;
                }

                updateHeroSlider();
                resetInterval();
            });
        });

        function updateHeroSlider() {
            const offset = -currentSlide * 100;
            heroSliderTrack.style.transform = `translateX(${offset}%)`;

            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });

            slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === currentSlide);
            });
        }

        updateHeroSlider();

        const handleDragStart = (e) => {
            startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            isDragging = true;
            heroSliderTrack.style.transition = 'none';
        };

        const handleDragEnd = (e) => {
            if (!isDragging) return;
            const endX = e.type.includes('mouse') ? e.clientX : e.changedTouches[0].clientX;
            const diff = startX - endX;

            heroSliderTrack.style.transition = 'transform 0.6s ease-in-out';

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    currentSlide = (currentSlide + 1) % slideCount;
                } else {
                    currentSlide = (currentSlide - 1 + slideCount) % slideCount;
                }
            }
            updateHeroSlider();
            resetInterval();
            isDragging = false;
        };

        const handleDragMove = (e) => {
            if (!isDragging) return;
            const currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            const diff = startX - currentX;
            const trackWidth = heroSliderContainer.offsetWidth;
            const offset = -currentSlide * 100 - (diff / trackWidth * 100);

            heroSliderTrack.style.transform = `translateX(${offset}%)`;
        };

        heroSliderContainer.addEventListener('touchstart', handleDragStart, { passive: true });
        heroSliderContainer.addEventListener('touchend', handleDragEnd, { passive: true });
        heroSliderContainer.addEventListener('touchmove', handleDragMove, { passive: true });

        heroSliderContainer.addEventListener('mousedown', handleDragStart);
        window.addEventListener('mouseup', handleDragEnd);
        window.addEventListener('mousemove', handleDragMove);

        heroSliderTrack.querySelectorAll('img').forEach(img => {
            img.addEventListener('dragstart', (e) => e.preventDefault());
        });

        let slideInterval = setInterval(() => {
            currentSlide = (currentSlide + 1) % slideCount;
            updateHeroSlider();
        }, 5000);

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % slideCount;
                updateHeroSlider();
            }, 5000);
        }

        (function() {
            const navbar = document.querySelector('.navbar');
            const heroSection = document.querySelector('.hero-section');
            const heroSliderContainer = document.getElementById('heroSliderContainer');
            if (!navbar) return;

            let ticking = false;
            let heroHeight = heroSection ? heroSection.offsetHeight : 600;

            window.addEventListener('resize', function() {
                if (heroSection) heroHeight = heroSection.offsetHeight;
            }, { passive: true });

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        const scrollPosition = window.scrollY;

                        if (scrollPosition > 50) {
                            navbar.classList.add('scrolled');
                        } else {
                            navbar.classList.remove('scrolled');
                        }

                        if (heroSection && heroSliderContainer) {
                            const fadeStart = heroHeight * 0.3;
                            const fadeEnd = heroHeight * 0.7;

                            if (scrollPosition < fadeStart) {
                                heroSliderContainer.style.opacity = '1';
                                heroSliderContainer.classList.remove('fade-out');
                            } else if (scrollPosition >= fadeStart && scrollPosition <= fadeEnd) {
                                const opacity = 1 - (scrollPosition - fadeStart) / (fadeEnd - fadeStart);
                                heroSliderContainer.style.opacity = opacity;
                            } else {
                                heroSliderContainer.style.opacity = '0';
                                heroSliderContainer.classList.add('fade-out');
                            }
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

        document.addEventListener('DOMContentLoaded', function () {
            initMultiLineCarousel();
        });

        function initMultiLineCarousel() {
            const carouselTracks = document.querySelectorAll('.carousel-track');
            let isPaused = false;
            let intervals = [];

            function moveCarousel(track, direction) {
                if (isPaused) return;

                const items = track.querySelectorAll('.logo-item');
                const firstItem = items[0];
                const itemWidth = firstItem.offsetWidth;

                if (direction === 'right') {
                    track.style.transition = 'transform 0.5s ease-in-out';
                    track.style.transform = `translateX(-${itemWidth}px)`;

                    setTimeout(() => {
                        track.style.transition = 'none';
                        track.appendChild(firstItem);
                        track.style.transform = 'translateX(0)';
                    }, 500);
                } else {
                    const lastItem = items[items.length - 1];
                    track.style.transition = 'none';
                    track.insertBefore(lastItem, firstItem);
                    track.style.transform = `translateX(-${itemWidth}px)`;

                    setTimeout(() => {
                        track.style.transition = 'transform 0.5s ease-in-out';
                        track.style.transform = 'translateX(0)';
                    }, 10);
                }
            }

            carouselTracks.forEach(track => {
                const direction = track.getAttribute('data-direction');
                const interval = setInterval(() => {
                    moveCarousel(track, direction);
                }, 3000);
                intervals.push(interval);
            });

            const container = document.querySelector('.multi-line-carousel-container');

            const logoItems = document.querySelectorAll('.logo-item');
            logoItems.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    isPaused = true;
                });

                item.addEventListener('mouseleave', () => {
                    isPaused = false;
                });
            });
        }

        const contactForm = document.getElementById('contactForm');
        const formStatus = document.getElementById('formStatus');

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const formStatus = document.getElementById('formStatus');

            formStatus.textContent = 'Enviando...';
            submitButton.disabled = true;

            const formData = new FormData(this);

            fetch('submit_form.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {

                        formStatus.textContent = '¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.';
                        formStatus.style.color = 'green';
                        contactForm.reset();
                    } else {
                        throw new Error('Error en el envío');
                    }
                })
                .catch(error => {
                    formStatus.textContent = 'Hubo un problema. Por favor, intenta más tarde.';
                    formStatus.style.color = 'red';
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.nav-links a');

            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        const isDropdownToggle = link.parentElement.classList.contains('dropdown');

                        if (!isDropdownToggle) {
                            navLinksContainer.classList.remove('open');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>