<?php
// Handler del formulario de recordatorio de La Feria Petersen
require_once __DIR__ . '/../cms/includes/config.php';
require_once __DIR__ . '/../cms/includes/security.php';
require_once __DIR__ . '/../cms/includes/database.php';
require_once __DIR__ . '/../cms/includes/feria.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar token CSRF
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'feria_lead']);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recargá la página e intentá de nuevo.']);
    exit;
}

// Honeypot: los bots completan este campo oculto, las personas no.
if (!empty($_POST['website'])) {
    echo json_encode(['success' => true, 'message' => '¡Listo! Te avisamos cuando la feria esté por comenzar.']);
    exit;
}

// Limitar a 5 registros cada 15 minutos por IP
$rateLimit = Security::checkRateLimit('feria_lead', 5, 900);
if (!$rateLimit['allowed']) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Demasiados intentos. Probá de nuevo en unos minutos.'
    ]);
    exit;
}

$db = Database::getInstance()->getConnection();
$feria = new Feria($db);

// El administrador puede cerrar los registros desde el CMS.
if (!$feria->getConfig()['activa']) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Los registros están cerrados por el momento.'
    ]);
    exit;
}

$nombre   = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email    = trim($_POST['email'] ?? '');
$ciudad   = trim($_POST['ciudad'] ?? '');

$errors = [];

if (mb_strlen($nombre) < 3) {
    $errors['nombre'] = 'Ingresá tu nombre completo';
} elseif (mb_strlen($nombre) > 255) {
    $errors['nombre'] = 'El nombre es demasiado largo';
}

$telefonoNormalizado = Feria::normalizePhone($telefono);
if ($telefonoNormalizado === '') {
    $errors['telefono'] = 'Ingresá un número de WhatsApp válido (ej: 0981 234 567)';
}

// El email es opcional, pero si se envía debe ser válido.
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'El email no es válido';
}

if ($ciudad === '') {
    $errors['ciudad'] = 'Ingresá tu ciudad';
} elseif (mb_strlen($ciudad) > 100) {
    $errors['ciudad'] = 'El nombre de la ciudad es demasiado largo';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Revisá los datos ingresados',
        'errors'  => $errors
    ]);
    exit;
}

try {
    $result = $feria->createLead([
        'nombre'   => $nombre,
        'telefono' => $telefonoNormalizado,
        'email'    => $email,
        'ciudad'   => $ciudad
    ]);

    if (!$result['success']) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No pudimos guardar tu registro. Intentá de nuevo.']);
        exit;
    }

    // El registro fue exitoso: no penalizar a este visitante en el rate limit.
    Security::resetRateLimit('feria_lead');

    echo json_encode([
        'success' => true,
        'message' => '¡Listo! Te avisamos por WhatsApp cuando la feria esté por comenzar.'
    ]);

} catch (Exception $e) {
    error_log('Error en feria-handler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}
