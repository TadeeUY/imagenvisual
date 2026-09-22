<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/GoogleAuthenticator.php';

session_name(SESSION_NAME);
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    header('Location: ./index.php');
    exit;
}

$error = '';
$step = 1; // 1: Usuario/Contraseña, 2: 2FA Setup, 3: 2FA Verification

require_once __DIR__ . '/ECC256Crypt.php';

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

if (isset($_GET['cancel'])) {
    session_destroy();
    header('Location: ./login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['otp_code'])) {
        if (!isset($_SESSION['temp_user_id'])) {
            header('Location: ./login.php');
            exit;
        }

        $otp_code = trim($_POST['otp_code']);
        $user_id = $_SESSION['temp_user_id'];
        
        $user_index = -1;
        foreach ($users as $index => $u) {
            if ($u['id'] === $user_id) {
                $user_index = $index;
                break;
            }
        }

        if ($user_index === -1) {
            $error = 'Usuario inválido en sesión.';
            $step = 1;
            session_destroy();
        } else {
            $user = $users[$user_index];
            $is_setup = !$user['is_2fa_enabled'];
            $secret = $is_setup ? $_SESSION['temp_2fa_secret'] : $user['google_2fa_secret'];

            if (GoogleAuthenticator::verifyCode($secret, $otp_code)) {
                if ($is_setup) {
                    $users[$user_index]['google_2fa_secret'] = $secret;
                    $users[$user_index]['is_2fa_enabled'] = true;

                    $encrypted_users = [];
                    foreach ($users as $u) {
                        if (isset($u['google_2fa_secret'])) {
                            $u['google_2fa_secret'] = ECC256Crypt::encrypt($u['google_2fa_secret']);
                        }
                        $encrypted_users[] = $u;
                    }
                    $content = "<?php\n";
                    $content .= "defined('SECURE_ACCESS') or die('Direct access not permitted');\n";
                    $content .= "return " . var_export($encrypted_users, true) . ";\n";
                    file_put_contents(__DIR__ . '/../data/users.php', $content);
                }

                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['2fa_verified'] = true;

                unset($_SESSION['temp_user_id']);
                unset($_SESSION['temp_username']);
                unset($_SESSION['temp_role']);
                unset($_SESSION['temp_2fa_secret']);
                unset($_SESSION['temp_2fa_enabled']);

                header('Location: ./index.php');
                exit;
            } else {
                $error = 'Código de verificación incorrecto. Inténtalo de nuevo.';
                $step = $is_setup ? 2 : 3;
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $matched_user = null;
        foreach ($users as $u) {
            if (strtolower($u['username']) === strtolower($username)) {
                $matched_user = $u;
                break;
            }
        }

        if ($matched_user) {
            if (password_verify($password, $matched_user['password_hash'])) {
                $_SESSION['temp_user_id'] = $matched_user['id'];
                $_SESSION['temp_username'] = $matched_user['username'];
                $_SESSION['temp_role'] = $matched_user['role'];
                $_SESSION['temp_2fa_enabled'] = $matched_user['is_2fa_enabled'];

                if ($matched_user['is_2fa_enabled']) {
                    $step = 3; // Ir a ingreso de código directo
                } else {
                    // Primer inicio de sesión. Generar clave secreta de 2FA
                    $secret = GoogleAuthenticator::createSecret();
                    $_SESSION['temp_2fa_secret'] = $secret;
                    $step = 2; // Ir a pantalla de configuración
                }
            } else {
                $error = 'Contraseña incorrecta.';
                $step = 1;
            }
        } else {
            $error = 'Usuario no encontrado.';
            $step = 1;
        }
    }
} else {
    // Si ya existe sesión temporal por recarga
    if (isset($_SESSION['temp_user_id'])) {
        $step = $_SESSION['temp_2fa_enabled'] ? 3 : 2;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Imagen Visual Admin</title>
    <link rel="shortcut icon" href="../imagenes/logos/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #dc3545;
            --primary-hover: #b02a37;
            --bg-dark: #0f1016;
            --card-bg: rgba(30, 31, 41, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #09090e 0%, #11121a 50%, #060609 100%);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            overflow-y: auto;
            position: relative;
        }

        body::before, body::after {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            z-index: 0;
            filter: blur(90px);
            opacity: 0.12;
            pointer-events: none;
        }
        body::before {
            background-color: var(--primary);
            top: 15%;
            left: 20%;
        }
        body::after {
            background-color: #6366f1;
            bottom: 15%;
            right: 20%;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px 30px;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            z-index: 1;
            margin: 20px 0;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-area img {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }

        .logo-area h2 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #fff;
        }

        .logo-area p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.875rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i:not(.toggle-password) {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 40px 12px 45px; /* Incrementar padding derecho para dar espacio al ojo */
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #fff;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.25);
        }

        .error-message {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #f87171;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #fff;
        }

        /* 2FA SETUP UI */
        .setup-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .qr-code-wrapper {
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .qr-code-wrapper img {
            width: 180px;
            height: 180px;
            display: block;
        }

        .secret-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 2px;
            color: #38bdf8;
            margin-bottom: 20px;
            user-select: all;
            word-break: break-all;
        }

        .setup-instruction {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo-area">
            <img src="../imagenes/logos/logo.png" alt="Imagen Visual">
            <h2>Panel Administrativo</h2>
            <?php if ($step === 1): ?>
                <p>Accede para gestionar trabajos y clientes</p>
            <?php elseif ($step === 2): ?>
                <p style="color: #38bdf8; font-weight: bold;"><i class="fas fa-shield-alt"></i> Configurar Segundo Factor (2FA)</p>
            <?php else: ?>
                <p style="color: #10b981; font-weight: bold;"><i class="fas fa-lock"></i> Verificación de Identidad (2FA)</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- PASO 1: LOGIN TRADICIONAL -->
        <?php if ($step === 1): ?>
            <form action="./login.php" method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" required autocomplete="username" placeholder="Ingresa tu usuario">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Ingresa tu contraseña">
                        <i class="fas fa-lock"></i>
                        <i class="fas fa-eye toggle-password" id="togglePasswordBtn"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Ingresar</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </form>

            <div class="back-link">
                <a href="../"><i class="fas fa-arrow-left"></i> Volver al sitio web</a>
            </div>

        <!-- PASO 2: CONFIGURAR 2FA (PRIMER LOGIN) -->
        <?php elseif ($step === 2): ?>
            <div class="setup-container">
                <p class="setup-instruction">
                    Para asegurar tu cuenta, es obligatorio configurar un segundo factor de autenticación.
                    Escanea este código QR con la aplicación **Google Authenticator** o **Microsoft Authenticator**:
                </p>

                <div class="qr-code-wrapper">
                    <?php 
                        $qr_text = GoogleAuthenticator::getQRText($_SESSION['temp_username'], $_SESSION['temp_2fa_secret']);
                        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($qr_text) . "&size=200x200&ecc=M";
                    ?>
                    <img src="<?php echo $qr_url; ?>" alt="Código QR 2FA">
                </div>

                <p class="setup-instruction">O introduce esta clave de respaldo manualmente en la aplicación:</p>
                <div class="secret-box"><?php echo htmlspecialchars($_SESSION['temp_2fa_secret']); ?></div>

                <form action="./login.php" method="POST" style="width: 100%;">
                    <div class="form-group">
                        <label for="otp_code">Código de 6 dígitos del teléfono</label>
                        <div class="input-wrapper">
                            <input type="text" id="otp_code" name="otp_code" required autocomplete="one-time-code" pattern="[0-9]{6}" inputmode="numeric" maxlength="6" placeholder="000000" style="text-align: center; font-size: 1.3rem; letter-spacing: 5px; padding-left: 15px;">
                            <i class="fas fa-key" style="left: auto; right: 20px; display: none;"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Verificar y Activar</span>
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            </div>

            <div class="back-link">
                <a href="?cancel=1"><i class="fas fa-arrow-left"></i> Volver atrás</a>
            </div>

        <!-- PASO 3: INGRESAR CÓDIGO OTP -->
        <?php elseif ($step === 3): ?>
            <div class="setup-container">
                <p class="setup-instruction">
                    Introduce el código de verificación de 6 dígitos generado por tu aplicación **Google Authenticator**:
                </p>

                <form action="./login.php" method="POST" style="width: 100%; margin-top: 15px;">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" id="otp_code" name="otp_code" required autocomplete="one-time-code" pattern="[0-9]{6}" inputmode="numeric" maxlength="6" autofocus placeholder="000000" style="text-align: center; font-size: 1.5rem; letter-spacing: 8px; padding-left: 15px; padding-right: 15px;">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top: 25px;">
                        <span>Verificar Identidad</span>
                        <i class="fas fa-shield-alt"></i>
                    </button>
                </form>
            </div>

            <div class="back-link">
                <a href="?cancel=1"><i class="fas fa-arrow-left"></i> Iniciar sesión con otra cuenta</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePasswordBtn');

        if (passwordInput && toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>
