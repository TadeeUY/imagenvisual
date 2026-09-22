<?php
require_once __DIR__ . '/config.php';

session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Requiere verificación de doble factor (2FA).']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    // Buscar el token en $_SERVER (estándar para CGI/FastCGI/FPM) y en las cabeceras
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Error de seguridad (CSRF inválido).']);
        exit;
    }
}

header('Content-Type: application/json');

require_once __DIR__ . '/ECC256Crypt.php';
require_once __DIR__ . '/GoogleAuthenticator.php';

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

function convert_to_webp_or_fallback($temp_file, $target_path, $quality = 80) {
    if (function_exists('imagewebp') && function_exists('imagecreatefromstring')) {
        $data = @file_get_contents($temp_file);
        if ($data !== false) {
            $image = @imagecreatefromstring($data);
            if ($image !== false) {
                // Preservar la transparencia (por si viene de un PNG)
                imagealphablending($image, false);
                imagesavealpha($image, true);
                $success = @imagewebp($image, $target_path, $quality);
                imagedestroy($image);
                if ($success) {
                    return true;
                }
            }
        }
    }
    return false;
}

function save_works($works) {
    $content = "<?php\n";
    $content .= "defined('SECURE_ACCESS') or die('Direct access not permitted');\n";
    $content .= "return " . var_export($works, true) . ";\n";
    return file_put_contents(__DIR__ . '/../data/works.php', $content) !== false;
}

function save_clients($clients) {
    $content = "<?php\n";
    $content .= "defined('SECURE_ACCESS') or die('Direct access not permitted');\n";
    $content .= "return " . var_export($clients, true) . ";\n";
    return file_put_contents(__DIR__ . '/../data/clients.php', $content) !== false;
}

function save_categories($categories) {
    $content = "<?php\n";
    $content .= "defined('SECURE_ACCESS') or die('Direct access not permitted');\n";
    $content .= "return " . var_export($categories, true) . ";\n";
    return file_put_contents(__DIR__ . '/../data/categories.php', $content) !== false;
}

function save_users($users) {
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
    return file_put_contents(__DIR__ . '/../data/users.php', $content) !== false;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add_work') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (empty($title) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'El título y la categoría son requeridos.']);
        exit;
    }

    $uploaded_images = [];

    if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['primary_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen principal no permitido.']);
            exit;
        }

        $filename_base = 'work_' . time() . '_' . rand(1000, 9999);
        if ($ext === 'svg') {
            $filename = $filename_base . '.svg';
            $target_path = UPLOAD_DIR_WORKS . $filename;
            $success = move_uploaded_file($file['tmp_name'], $target_path);
        } else {
            $filename = $filename_base . '.webp';
            $target_path = UPLOAD_DIR_WORKS . $filename;
            $success = convert_to_webp_or_fallback($file['tmp_name'], $target_path, 80);
            if (!$success) {
                $filename = $filename_base . '.' . $ext;
                $target_path = UPLOAD_DIR_WORKS . $filename;
                $success = move_uploaded_file($file['tmp_name'], $target_path);
            }
        }

        if ($success) {
            $uploaded_images[] = './imagenes/trabajos_subidos/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen principal.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'La imagen principal es requerida.']);
        exit;
    }

    if (isset($_FILES['secondary_images'])) {
        $files = $_FILES['secondary_images'];
        $file_count = count($files['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                
                if (in_array($ext, $allowed)) {
                    $filename_base = 'work_' . time() . '_' . rand(1000, 9999) . '_sec';
                    if ($ext === 'svg') {
                        $filename = $filename_base . '.svg';
                        $target_path = UPLOAD_DIR_WORKS . $filename;
                        $success = move_uploaded_file($files['tmp_name'][$i], $target_path);
                    } else {
                        $filename = $filename_base . '.webp';
                        $target_path = UPLOAD_DIR_WORKS . $filename;
                        $success = convert_to_webp_or_fallback($files['tmp_name'][$i], $target_path, 80);
                        if (!$success) {
                            $filename = $filename_base . '.' . $ext;
                            $target_path = UPLOAD_DIR_WORKS . $filename;
                            $success = move_uploaded_file($files['tmp_name'][$i], $target_path);
                        }
                    }
                    if ($success) {
                        $uploaded_images[] = './imagenes/trabajos_subidos/' . $filename;
                    }
                }
            }
        }
    }

    $new_id = 1;
    if (count($works) > 0) {
        $ids = array_column($works, 'id');
        $new_id = max($ids) + 1;
    }

    $new_work = [
        'id' => $new_id,
        'category' => $category,
        'title' => $title,
        'description' => $description,
        'images' => $uploaded_images
    ];

    $works[] = $new_work;

    if (save_works($works)) {
        echo json_encode(['success' => true, 'message' => 'Trabajo agregado correctamente.', 'work' => $new_work]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el trabajo en la base de datos.']);
    }
    exit;
}

elseif ($action === 'edit_work') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (empty($title) || empty($category) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios incompletos.']);
        exit;
    }

    $work_index = -1;
    foreach ($works as $index => $w) {
        if ($w['id'] === $id) {
            $work_index = $index;
            break;
        }
    }

    if ($work_index === -1) {
        echo json_encode(['success' => false, 'message' => 'Trabajo no encontrado.']);
        exit;
    }

    $existing_images = $works[$work_index]['images'];

    $deleted_images = json_decode($_POST['deleted_images'] ?? '[]', true);
    $filtered_images = [];
    foreach ($existing_images as $img) {
        if (in_array($img, $deleted_images)) {
            $local_path = __DIR__ . '/../' . ltrim($img, './');
            if (file_exists($local_path) && is_file($local_path)) {
                @unlink($local_path);
            }
        } else {
            $filtered_images[] = $img;
        }
    }

    $image_order = json_decode($_POST['image_order'] ?? '[]', true);
    if (!empty($image_order)) {
        // Aseguramos que solo contenga imágenes que no hayan sido eliminadas
        $filtered_images = array_values(array_filter($image_order, function($img) use ($filtered_images) {
            return in_array($img, $filtered_images);
        }));
    }

    if (isset($_FILES['secondary_images'])) {
        $files = $_FILES['secondary_images'];
        $file_count = count($files['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                
                if (in_array($ext, $allowed)) {
                    $filename_base = 'work_' . time() . '_' . rand(1000, 9999) . '_sec';
                    if ($ext === 'svg') {
                        $filename = $filename_base . '.svg';
                        $target_path = UPLOAD_DIR_WORKS . $filename;
                        $success = move_uploaded_file($files['tmp_name'][$i], $target_path);
                    } else {
                        $filename = $filename_base . '.webp';
                        $target_path = UPLOAD_DIR_WORKS . $filename;
                        $success = convert_to_webp_or_fallback($files['tmp_name'][$i], $target_path, 80);
                        if (!$success) {
                            $filename = $filename_base . '.' . $ext;
                            $target_path = UPLOAD_DIR_WORKS . $filename;
                            $success = move_uploaded_file($files['tmp_name'][$i], $target_path);
                        }
                    }
                    if ($success) {
                        $filtered_images[] = './imagenes/trabajos_subidos/' . $filename;
                    }
                }
            }
        }
    }

    if (empty($filtered_images)) {
        echo json_encode(['success' => false, 'message' => 'El trabajo debe tener al menos una imagen.']);
        exit;
    }

    $works[$work_index]['title'] = $title;
    $works[$work_index]['description'] = $description;
    $works[$work_index]['category'] = $category;
    $works[$work_index]['images'] = $filtered_images;

    if (save_works($works)) {
        echo json_encode(['success' => true, 'message' => 'Trabajo actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en el archivo.']);
    }
    exit;
}

elseif ($action === 'delete_work') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    $work_index = -1;
    foreach ($works as $index => $w) {
        if ($w['id'] === $id) {
            $work_index = $index;
            break;
        }
    }

    if ($work_index === -1) {
        echo json_encode(['success' => false, 'message' => 'Trabajo no encontrado.']);
        exit;
    }

    foreach ($works[$work_index]['images'] as $img) {
        $local_path = __DIR__ . '/../' . ltrim($img, './');
        if (file_exists($local_path) && is_file($local_path)) {
            @unlink($local_path);
        }
    }

    array_splice($works, $work_index, 1);

    if (save_works($works)) {
        echo json_encode(['success' => true, 'message' => 'Trabajo eliminado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en la base de datos.']);
    }
    exit;
}

elseif ($action === 'add_logo') {
    $line = trim($_POST['line'] ?? '');
    $alt = trim($_POST['alt'] ?? '');

    if (!in_array($line, ['line1', 'line2', 'line3']) || empty($alt)) {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos.']);
        exit;
    }

    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de logo no permitido.']);
            exit;
        }

        $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $alt));
        $filename_base = 'logo_' . $clean_name . '_' . time();
        if ($ext === 'svg') {
            $filename = $filename_base . '.svg';
            $target_path = UPLOAD_DIR_CLIENTS . $filename;
            $success = move_uploaded_file($file['tmp_name'], $target_path);
        } else {
            $filename = $filename_base . '.webp';
            $target_path = UPLOAD_DIR_CLIENTS . $filename;
            $success = convert_to_webp_or_fallback($file['tmp_name'], $target_path, 85); // Mayor calidad para logos
            if (!$success) {
                $filename = $filename_base . '.' . $ext;
                $target_path = UPLOAD_DIR_CLIENTS . $filename;
                $success = move_uploaded_file($file['tmp_name'], $target_path);
            }
        }

        if ($success) {
            $new_logo = [
                'src' => './imagenes/clientes/' . $filename,
                'alt' => $alt
            ];
            
            array_unshift($clients[$line], $new_logo);

            if (save_clients($clients)) {
                echo json_encode(['success' => true, 'message' => 'Logo agregado correctamente.', 'logo' => $new_logo]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo de logo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'El archivo de logo es obligatorio.']);
    }
    exit;
}

elseif ($action === 'delete_logo') {
    $line = trim($_POST['line'] ?? '');
    $src = trim($_POST['src'] ?? '');

    if (!in_array($line, ['line1', 'line2', 'line3']) || empty($src)) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
        exit;
    }

    $logo_index = -1;
    foreach ($clients[$line] as $index => $logo) {
        if ($logo['src'] === $src) {
            $logo_index = $index;
            break;
        }
    }

    if ($logo_index === -1) {
        echo json_encode(['success' => false, 'message' => 'Logo no encontrado en esta línea.']);
        exit;
    }

    array_splice($clients[$line], $logo_index, 1);

    if (save_clients($clients)) {
        // Eliminar archivo físico si está en nuestra carpeta de subidas y no es referenciado en otro lado
        $local_path = __DIR__ . '/../' . ltrim($src, './');
        $is_referenced = false;
        foreach ($clients as $l => $logos) {
            foreach ($logos as $logo) {
                if ($logo['src'] === $src) {
                    $is_referenced = true;
                    break 2;
                }
            }
        }
        
        if (!$is_referenced && file_exists($local_path) && is_file($local_path)) {
            @unlink($local_path);
        }

        echo json_encode(['success' => true, 'message' => 'Logo eliminado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar cambios de eliminación de logo.']);
    }
    exit;
}

elseif ($action === 'reorder_logos') {
    $new_order_json = $_POST['order_data'] ?? '';
    $new_order = json_decode($new_order_json, true);

    if (!$new_order || !isset($new_order['line1']) || !isset($new_order['line2']) || !isset($new_order['line3'])) {
        echo json_encode(['success' => false, 'message' => 'Datos de ordenamiento inválidos.']);
        exit;
    }

    // Reconstruimos la base de datos de clientes basándonos en el orden provisto.
    // Esto previene inyecciones de datos corruptos al validar que los archivos src realmente existan en el array original.
    $sanitized_clients = [
        'line1' => [],
        'line2' => [],
        'line3' => []
    ];

    $all_available_logos = [];
    foreach ($clients as $l => $logos) {
        foreach ($logos as $logo) {
            $all_available_logos[$logo['src']] = $logo;
        }
    }

    foreach (['line1', 'line2', 'line3'] as $line_key) {
        foreach ($new_order[$line_key] as $logo_item) {
            $src = $logo_item['src'] ?? '';
            if (isset($all_available_logos[$src])) {
                $original_logo = $all_available_logos[$src];
                $sanitized_logo = [
                    'src' => $original_logo['src'],
                    'alt' => $logo_item['alt'] ?? $original_logo['alt']
                ];
                if (isset($logo_item['width'])) {
                    $w = trim($logo_item['width']);
                    if ($w !== '') {
                        $sanitized_logo['width'] = $w;
                    }
                }
                $sanitized_clients[$line_key][] = $sanitized_logo;
            }
        }
    }

    if (save_clients($sanitized_clients)) {
        echo json_encode(['success' => true, 'message' => 'Posición de logotipos guardada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en el archivo.']);
    }
    exit;
}

elseif ($action === 'add_category') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_desc = trim($_POST['seo_desc'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    if (empty($name) || empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'El nombre y el slug son requeridos.']);
        exit;
    }

    $slug = preg_replace('/[^a-z0-9-]+/', '', strtolower(str_replace(' ', '-', $slug)));

    foreach ($categories as $cat) {
        if ($cat['slug'] === $slug) {
            echo json_encode(['success' => false, 'message' => 'El slug ya está en uso por otra categoría.']);
            exit;
        }
    }

    $new_id = 1;
    if (count($categories) > 0) {
        $ids = array_column($categories, 'id');
        $new_id = max($ids) + 1;
    }

    $new_cat = [
        'id' => $new_id,
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'seo_title' => $seo_title ? $seo_title : "$name | Imagen Visual",
        'seo_desc' => $seo_desc ? $seo_desc : $description,
        'seo_keywords' => $seo_keywords
    ];

    $categories[] = $new_cat;

    if (save_categories($categories)) {
        echo json_encode(['success' => true, 'message' => 'Categoría agregada correctamente.', 'category' => $new_cat]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la categoría en el archivo.']);
    }
    exit;
}

elseif ($action === 'edit_category') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_desc = trim($_POST['seo_desc'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    if (empty($name) || empty($slug) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Campos obligatorios vacíos.']);
        exit;
    }

    $slug = preg_replace('/[^a-z0-9-]+/', '', strtolower(str_replace(' ', '-', $slug)));

    $cat_index = -1;
    foreach ($categories as $index => $cat) {
        if ($cat['id'] === $id) {
            $cat_index = $index;
            break;
        }
    }

    if ($cat_index === -1) {
        echo json_encode(['success' => false, 'message' => 'Categoría no encontrada.']);
        exit;
    }

    foreach ($categories as $index => $cat) {
        if ($cat['id'] !== $id && $cat['slug'] === $slug) {
            echo json_encode(['success' => false, 'message' => 'El slug ya está siendo usado por otra categoría.']);
            exit;
        }
    }

    $old_slug = $categories[$cat_index]['slug'];

    // Si cambió el slug, actualizar todos los trabajos asociados para evitar que queden huérfanos
    if ($old_slug !== $slug) {
        $works_updated = false;
        foreach ($works as &$w) {
            if ($w['category'] === $old_slug) {
                $w['category'] = $slug;
                $works_updated = true;
            }
        }
        unset($w);
        if ($works_updated) {
            save_works($works);
        }
    }

    $categories[$cat_index]['name'] = $name;
    $categories[$cat_index]['slug'] = $slug;
    $categories[$cat_index]['description'] = $description;
    $categories[$cat_index]['seo_title'] = $seo_title ? $seo_title : "$name | Imagen Visual";
    $categories[$cat_index]['seo_desc'] = $seo_desc ? $seo_desc : $description;
    $categories[$cat_index]['seo_keywords'] = $seo_keywords;

    if (save_categories($categories)) {
        echo json_encode(['success' => true, 'message' => 'Categoría actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios de la categoría.']);
    }
    exit;
}

elseif ($action === 'delete_category') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    $cat_index = -1;
    foreach ($categories as $index => $cat) {
        if ($cat['id'] === $id) {
            $cat_index = $index;
            break;
        }
    }

    if ($cat_index === -1) {
        echo json_encode(['success' => false, 'message' => 'Categoría no encontrada.']);
        exit;
    }

    $cat_slug = $categories[$cat_index]['slug'];

    $assoc_count = 0;
    foreach ($works as $w) {
        if ($w['category'] === $cat_slug) {
            $assoc_count++;
        }
    }

    if ($assoc_count > 0) {
        echo json_encode([
            'success' => false, 
            'message' => "No se puede eliminar la categoría porque tiene {$assoc_count} trabajos asociados. Por favor, elimínalos o reasígnalos primero."
        ]);
        exit;
    }

    array_splice($categories, $cat_index, 1);

    if (save_categories($categories)) {
        echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en la base de datos.']);
    }
    exit;
}

elseif ($action === 'reorder_categories') {
    $new_order_json = $_POST['order_data'] ?? '';
    $new_order = json_decode($new_order_json, true);

    if (!$new_order || !is_array($new_order)) {
        echo json_encode(['success' => false, 'message' => 'Datos de ordenamiento inválidos.']);
        exit;
    }

    $sanitized_categories = [];

    $indexed_categories = [];
    foreach ($categories as $cat) {
        $indexed_categories[$cat['id']] = $cat;
    }

    foreach ($new_order as $order_item) {
        $id = (int)($order_item['id'] ?? 0);
        if (isset($indexed_categories[$id])) {
            $sanitized_categories[] = $indexed_categories[$id];
        }
    }

    if (count($sanitized_categories) !== count($categories)) {
        echo json_encode(['success' => false, 'message' => 'Error de consistencia en el ordenamiento.']);
        exit;
    }

    if (save_categories($sanitized_categories)) {
        echo json_encode(['success' => true, 'message' => 'Orden de categorías guardado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el nuevo orden de categorías.']);
    }
    exit;
}

elseif ($action === 'add_user') {
    if ($_SESSION['role'] !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Rol insuficiente.']);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'admin');

    if (empty($name) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Nombre, usuario y contraseña son obligatorios.']);
        exit;
    }

    foreach ($users as $u) {
        if (strtolower($u['username']) === strtolower($username)) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está registrado.']);
            exit;
        }
    }

    $new_id = 1;
    if (count($users) > 0) {
        $ids = array_column($users, 'id');
        $new_id = max($ids) + 1;
    }

    $new_user = [
        'id' => $new_id,
        'name' => $name,
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role === 'superadmin' ? 'superadmin' : 'admin',
        'google_2fa_secret' => '',
        'is_2fa_enabled' => false
    ];

    $users[] = $new_user;

    if (save_users($users)) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el usuario en el archivo.']);
    }
    exit;
}

elseif ($action === 'delete_user') {
    if ($_SESSION['role'] !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    // No permitir eliminarse a sí mismo
    if ($id === (int)$_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario.']);
        exit;
    }

    $user_idx = -1;
    foreach ($users as $index => $u) {
        if ($u['id'] === $id) {
            $user_idx = $index;
            break;
        }
    }

    if ($user_idx === -1) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    array_splice($users, $user_idx, 1);

    if (save_users($users)) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios de base de datos.']);
    }
    exit;
}

elseif ($action === 'reset_password') {
    if ($_SESSION['role'] !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    $new_password = trim($_POST['password'] ?? '');

    if ($id <= 0 || empty($new_password)) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
        exit;
    }

    $user_idx = -1;
    foreach ($users as $index => $u) {
        if ($u['id'] === $id) {
            $user_idx = $index;
            break;
        }
    }

    if ($user_idx === -1) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    $users[$user_idx]['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);

    if (save_users($users)) {
        echo json_encode(['success' => true, 'message' => 'Contraseña del usuario restablecida correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en la base de datos.']);
    }
    exit;
}

elseif ($action === 'reset_2fa') {
    if ($_SESSION['role'] !== 'superadmin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    $user_idx = -1;
    foreach ($users as $index => $u) {
        if ($u['id'] === $id) {
            $user_idx = $index;
            break;
        }
    }

    if ($user_idx === -1) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    $users[$user_idx]['google_2fa_secret'] = '';
    $users[$user_idx]['is_2fa_enabled'] = false;

    if (save_users($users)) {
        echo json_encode(['success' => true, 'message' => 'Se ha restablecido el 2FA. El usuario deberá escanear un nuevo código QR en su próximo inicio de sesión.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios en el archivo.']);
    }
    exit;
}

elseif ($action === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $otp_code = trim($_POST['otp_code'] ?? '');

    if (empty($current_password) || empty($new_password) || empty($confirm_password) || empty($otp_code)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña y su confirmación no coinciden.']);
        exit;
    }

    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
        exit;
    }

    $user_id = (int)($_SESSION['user_id'] ?? 0);
    $user_idx = -1;
    foreach ($users as $index => $u) {
        if ($u['id'] === $user_id) {
            $user_idx = $index;
            break;
        }
    }

    if ($user_idx === -1) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado en la sesión.']);
        exit;
    }

    $user = $users[$user_idx];

    if (!password_verify($current_password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta.']);
        exit;
    }

    // Validar que la nueva contraseña no sea igual a la actual
    if (password_verify($new_password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña no puede ser igual a la contraseña actual.']);
        exit;
    }

    if (empty($user['is_2fa_enabled']) || empty($user['google_2fa_secret'])) {
        echo json_encode(['success' => false, 'message' => '2FA no está configurado para esta cuenta.']);
        exit;
    }

    if (!GoogleAuthenticator::verifyCode($user['google_2fa_secret'], $otp_code)) {
        echo json_encode(['success' => false, 'message' => 'El código de verificación 2FA es incorrecto o ha expirado.']);
        exit;
    }

    $users[$user_idx]['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);

    if (save_users($users)) {
        echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la nueva contraseña en el servidor.']);
    }
    exit;
}

else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Acción inválida o no provista.']);
    exit;
}
?>
