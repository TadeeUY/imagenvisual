<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Imagen Visual</title>
    <link rel="shortcut icon" href="./imagenes/logos/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://db.onlinewebfonts.com/c/b60b9d7947c319b934d276c5cc819555?family=VW+Head+Light+Regular"
        rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/0d1062ff99c782bdbdd1ac3b47c577ba?family=VW+Text" rel="stylesheet"
        type="text/css" />
    <style>
        :root {
            --primary-color: #dc3545;
            --primary-dark: #99222e;
            --text-color: #333;
            --light-gray: #f8f8f8;
            --white: #ffffff;
            --dark-bg: rgba(39, 37, 40, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "VW Text", sans-serif;
            background-color: var(--light-gray);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .header-simple {
            background-color: var(--dark-bg);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .header-simple img {
            max-width: 300px;
            height: auto;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-card {
            max-width: 700px;
            width: 100%;
            background: var(--white);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-bottom: 6px solid var(--primary-color);
            text-align: center;
        }

        .error-visual {
            position: relative;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .error-code {
            font-size: clamp(100px, 15vw, 150px);
            font-weight: 900;
            color: #f0f0f0;
            line-height: 1;
        }

        .error-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3.5rem;
            color: var(--primary-color);
        }

        .error-message {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: var(--primary-dark);
            font-weight: bold;
            margin-bottom: 15px;
            font-family: "VW Head Light Regular", sans-serif;
        }

        .error-description {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.95rem;
        }

        .btn-action:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .links-ayuda {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .links-ayuda a {
            color: #777;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .links-ayuda a:hover {
            color: var(--primary-color);
        }

        .fa-triangle-exclamation {
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        @media (max-width: 768px) {
            .header-simple img {
                max-width: 220px;
            }

            .error-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>

    <header class="header-simple">
        <a href="./">
            <img src="./imagenes/logos/logo.png" alt="Imagen Visual Logo">
        </a>
    </header>

    <main class="main-content">
        <div class="error-card">
            <div class="error-visual">
                <div class="error-code">404</div>
                <div class="error-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <h1 class="error-message">¿Buscas una página? Esta no es la correcta.</h1>

            <p class="error-description">
                Lo sentimos, la página que intentas visualizar no existe o ha sido movida.
                <br>
                Utiliza el botón debajo para volver a nuestro catálogo principal de servicios.
            </p>

            <a href="./" class="btn-action">
                <i class="fa-solid fa-house"></i> Volver al Inicio
            </a>

            <div class="links-ayuda">
                <a href="./#contacto">Contacto</a>
                <a href="./#nosotros">Nosotros</a>
                <a href="https://wa.me/59892139068" target="_blank">WhatsApp</a>
            </div>
        </div>
    </main>

</body>

</html>