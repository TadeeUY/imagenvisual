<?php
define('SECURE_ACCESS', true);

define('ADMIN_USER', 'admin');

// Hash bcrypt de la contraseña de administrador
define('ADMIN_PASS_HASH', '$2y$10$L1VwG.j2sEaPzH2P5T9Lne6gR8l8Q9W4M.G9sE2r5u2q2K3J7yV7q');

define('SESSION_NAME', 'IMAGEN_VISUAL_ADMIN_SESSION');

define('UPLOAD_DIR_WORKS', '../imagenes/trabajos_subidos/');
define('UPLOAD_DIR_CLIENTS', '../imagenes/clientes/');

if (!file_exists(UPLOAD_DIR_WORKS)) {
    @mkdir(UPLOAD_DIR_WORKS, 0755, true);
    // index.html vacío para evitar listado de directorio
    @file_put_contents(UPLOAD_DIR_WORKS . 'index.html', '');
}
?>
