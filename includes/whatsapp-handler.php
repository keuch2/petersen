<?php
// Handler para guardar datos antes de redirigir a WhatsApp
require_once __DIR__ . '/../cms/includes/database.php';
require_once __DIR__ . '/../cms/includes/site-options.php';
require_once __DIR__ . '/../cms/includes/contact-message.php';

header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar campos requeridos
$required = ['nombre', 'email', 'mensaje', 'area'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben ser completados']);
        exit;
    }
}

// Validar email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El email no es válido']);
    exit;
}

try {
    // Conectar a la base de datos
    $db = Database::getInstance()->getConnection();
    
    // Crear instancias de los modelos
    $contactMessage = new ContactMessage($db);
    $siteOptions = new SiteOptions($db);
    
    // Preparar datos
    $data = [
        'nombre' => trim($_POST['nombre']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'empresa' => trim($_POST['empresa'] ?? ''),
        'ciudad' => trim($_POST['ciudad'] ?? ''),
        'area' => trim($_POST['area']),
        'mensaje' => trim($_POST['mensaje'])
    ];
    
    // Guardar en la base de datos
    $result = $contactMessage->create($data);
    
    if (!$result['success']) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje']);
        exit;
    }
    
    // Obtener configuración de WhatsApp
    $whatsappConfig = $siteOptions->getWhatsAppConfig();
    
    // Mapear área a número de WhatsApp
    $areaMap = [
        'ventas' => $whatsappConfig['ventas'],
        'soporte' => $whatsappConfig['soporte'],
        'rrhh' => $whatsappConfig['rrhh'],
        'administracion' => $whatsappConfig['administracion'],
        'otro' => $whatsappConfig['general']
    ];
    
    $whatsappNumber = $areaMap[$data['area']] ?? $whatsappConfig['general'];
    
    if (empty($whatsappNumber)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'No hay número de WhatsApp configurado para esta área'
        ]);
        exit;
    }
    
    // Crear mensaje para WhatsApp
    $whatsappMessage = "Hola, mi nombre es *{$data['nombre']}*\n\n";
    
    if (!empty($data['empresa'])) {
        $whatsappMessage .= "Empresa: {$data['empresa']}\n";
    }
    
    if (!empty($data['telefono'])) {
        $whatsappMessage .= "Teléfono: {$data['telefono']}\n";
    }
    
    if (!empty($data['ciudad'])) {
        $whatsappMessage .= "Ciudad: {$data['ciudad']}\n";
    }
    
    $whatsappMessage .= "\nMensaje:\n{$data['mensaje']}";
    
    // Crear URL de WhatsApp
    $whatsappUrl = "https://wa.me/" . $whatsappNumber . "?text=" . urlencode($whatsappMessage);
    
    // Respuesta exitosa con URL de WhatsApp
    echo json_encode([
        'success' => true,
        'message' => 'Datos guardados correctamente',
        'whatsapp_url' => $whatsappUrl
    ]);
    
} catch (Exception $e) {
    error_log('Error en whatsapp-handler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}
