<?php
// Handler específico para descargas de catálogos
require_once __DIR__ . '/../cms/includes/database.php';
require_once __DIR__ . '/../cms/includes/catalog.php';
require_once __DIR__ . '/../cms/includes/catalog-lead.php';

header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar campos requeridos
$required = ['catalog_id', 'name', 'email', 'phone'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
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
    $catalogModel = new Catalog($db);
    $catalogLead = new CatalogLead($db);
    
    $catalogId = $_POST['catalog_id'];
    
    // Verificar que el catálogo existe y está activo
    $catalog = $catalogModel->getById($catalogId);
    
    if (!$catalog) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Catálogo no encontrado']);
        exit;
    }
    
    if ($catalog['status'] !== 'active') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Este catálogo no está disponible']);
        exit;
    }
    
    if (empty($catalog['pdf_path']) || !file_exists($catalog['pdf_path'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Archivo PDF no encontrado']);
        exit;
    }
    
    // Preparar datos del lead
    $leadData = [
        'catalog_id' => $catalogId,
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'company' => trim($_POST['company'] ?? ''),
        'city' => trim($_POST['city'] ?? '')
    ];
    
    // Guardar lead en la base de datos
    $result = $catalogLead->create($leadData);
    
    if (!$result['success']) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al registrar la descarga']);
        exit;
    }
    
    // Generar URL de descarga
    $downloadUrl = str_replace(__DIR__ . '/../', '', $catalog['pdf_path']);
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Descarga registrada correctamente',
        'download_url' => $downloadUrl,
        'catalog_title' => $catalog['title']
    ]);
    
} catch (Exception $e) {
    error_log('Error en catalog-download-handler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}
