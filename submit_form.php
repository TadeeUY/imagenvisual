<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

if (empty($name) || empty($email) || empty($phone) || empty($message)) {
    http_response_code(400);
    echo "Por favor, complete todos los campos obligatorios.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "El correo electrónico no es válido.";
    exit;
}

$to = "info@imagenvisual.com.uy";
$subject = "Nueva consulta desde la Web de: " . $name;

$email_content = "Has recibido una nueva consulta a través del formulario de contacto del sitio web:\n\n";
$email_content .= "Nombre: $name\n";
$email_content .= "Email: $email\n";
$email_content .= "Teléfono: $phone\n\n";
$email_content .= "Mensaje:\n$message\n";

$headers = "From: Formulario Imagen Visual <no-reply@imagenvisual.com.uy>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (@mail($to, $subject, $email_content, $headers)) {
    http_response_code(200);
    echo "¡Mensaje enviado con éxito!";
} else {
    http_response_code(500);
    echo "Hubo un problema del servidor al procesar el envío.";
}
?>