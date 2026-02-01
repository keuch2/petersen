<?php
// Handler para postulaciones laborales
require_once __DIR__ . '/../cms/includes/database.php';
require_once __DIR__ . '/../cms/includes/site-options.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar campos requeridos
$required = ['nombre', 'telefono', 'email', 'puesto'];
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

// Validar archivo CV
if (empty($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe adjuntar su CV']);
    exit;
}

$file = $_FILES['cv'];
$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validar tipo de archivo
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de archivo no permitido. Use PDF, DOC o DOCX']);
    exit;
}

// Validar tamaño
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 5MB']);
    exit;
}

try {
    // Conectar a la base de datos
    $db = Database::getInstance()->getConnection();
    
    // Crear directorio para CVs si no existe
    $uploadDir = __DIR__ . '/../uploads/cvs/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Error al guardar el archivo');
    }
    
    // Preparar datos
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $puesto = trim($_POST['puesto']);
    $cvPath = 'uploads/cvs/' . $fileName;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Guardar en base de datos
    $stmt = $db->prepare("
        INSERT INTO job_applications (nombre, telefono, email, puesto, cv_path, ip_address, created_at)
        VALUES (:nombre, :telefono, :email, :puesto, :cv_path, :ip_address, CURRENT_TIMESTAMP)
    ");
    
    $result = $stmt->execute([
        'nombre' => $nombre,
        'telefono' => $telefono,
        'email' => $email,
        'puesto' => $puesto,
        'cv_path' => $cvPath,
        'ip_address' => $ipAddress
    ]);
    
    if (!$result) {
        throw new Exception('Error al guardar la postulación');
    }
    
    // Enviar email si SMTP está configurado
    $siteOptions = new SiteOptions($db);
    $smtpEnabled = $siteOptions->get('smtp_enabled');
    
    if ($smtpEnabled === '1') {
        require_once __DIR__ . '/../cms/vendor/autoload.php';
        
        $mail = new PHPMailer(true);
        
        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $siteOptions->get('smtp_host');
            $mail->SMTPAuth = true;
            $mail->Username = $siteOptions->get('smtp_username');
            $mail->Password = $siteOptions->get('smtp_password');
            $mail->SMTPSecure = $siteOptions->get('smtp_encryption') ?: 'tls';
            $mail->Port = $siteOptions->get('smtp_port') ?: 587;
            $mail->CharSet = 'UTF-8';
            
            // Remitente y destinatario
            $mail->setFrom($siteOptions->get('smtp_from_email'), $siteOptions->get('smtp_from_name'));
            $mail->addAddress($siteOptions->get('contact_recipient_email'));
            $mail->addReplyTo($email, $nombre);
            
            // Adjuntar CV
            $mail->addAttachment($filePath, $fileName);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = '[RRHH] Nueva Postulación Laboral - ' . $puesto;
            
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #2c3e5c; color: white; padding: 20px; text-align: center; }
                        .content { background: #f9f9f9; padding: 20px; }
                        .field { margin-bottom: 15px; }
                        .label { font-weight: bold; color: #2c3e5c; }
                        .value { margin-top: 5px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Nueva Postulación Laboral</h2>
                        </div>
                        <div class='content'>
                            <div class='field'>
                                <div class='label'>Nombre Completo:</div>
                                <div class='value'>{$nombre}</div>
                            </div>
                            <div class='field'>
                                <div class='label'>Teléfono:</div>
                                <div class='value'>{$telefono}</div>
                            </div>
                            <div class='field'>
                                <div class='label'>Email:</div>
                                <div class='value'>{$email}</div>
                            </div>
                            <div class='field'>
                                <div class='label'>Puesto al que se Postula:</div>
                                <div class='value'>{$puesto}</div>
                            </div>
                            <div class='field'>
                                <div class='label'>CV Adjunto:</div>
                                <div class='value'>{$fileName}</div>
                            </div>
                            <div class='field'>
                                <div class='label'>Fecha de Postulación:</div>
                                <div class='value'>" . date('d/m/Y H:i') . "</div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $mail->send();
            
        } catch (Exception $e) {
            error_log('Error al enviar email de postulación: ' . $mail->ErrorInfo);
        }
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => '¡Gracias por tu postulación! Hemos recibido tu CV y nos pondremos en contacto contigo pronto.'
    ]);
    
} catch (Exception $e) {
    error_log('Error en job-application-handler: ' . $e->getMessage());
    
    // Eliminar archivo si se subió pero hubo error
    if (isset($filePath) && file_exists($filePath)) {
        unlink($filePath);
    }
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar la postulación. Por favor, intente nuevamente.']);
}
