<?php
// Handler para procesar el formulario de contacto del sitio público
require_once __DIR__ . '/../cms/includes/database.php';
require_once __DIR__ . '/../cms/includes/site-options.php';
require_once __DIR__ . '/../cms/includes/contact-message.php';
require_once __DIR__ . '/../cms/includes/form-settings.php';

header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar campos requeridos
$required = ['nombre', 'email', 'mensaje'];
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

// Validar longitud del mensaje
if (strlen($_POST['mensaje']) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El mensaje debe tener al menos 10 caracteres']);
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
        'area' => trim($_POST['area'] ?? ''),
        'mensaje' => trim($_POST['mensaje'])
    ];
    
    // Guardar en la base de datos
    $result = $contactMessage->create($data);
    
    if (!$result['success']) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje']);
        exit;
    }
    
    $messageId = $result['id'];
    
    // Intentar enviar por email si SMTP está configurado
    $smtpConfig = $siteOptions->getSMTPConfig();
    $emailSent = false;
    
    // Obtener emails configurados para el formulario de contacto
    $recipientEmails = FormSettings::getFormEmails('contacto');
    
    if ($smtpConfig['enabled'] && !empty($recipientEmails)) {
        $emailSent = sendContactEmail($data, $smtpConfig, $recipientEmails);
        
        if ($emailSent) {
            $contactMessage->markAsSent($messageId);
        }
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.',
        'email_sent' => $emailSent
    ]);
    
} catch (Exception $e) {
    error_log('Error en contact-handler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar el mensaje']);
}

function sendContactEmail($data, $config, $recipientEmails) {
    try {
        // Mapear áreas a nombres descriptivos
        $areas = [
            'ventas' => 'Ventas',
            'soporte' => 'Soporte Técnico',
            'rrhh' => 'Recursos Humanos',
            'administracion' => 'Administración',
            'otro' => 'Otro'
        ];
        
        $areaNombre = $areas[$data['area']] ?? $data['area'];
        
        // Crear el asunto con el área
        $subject = "[" . $areaNombre . "] Nuevo mensaje de contacto - " . $data['nombre'];
        
        $message = "
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
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Nuevo Mensaje de Contacto</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Nombre:</div>
                        <div class='value'>" . htmlspecialchars($data['nombre']) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>" . htmlspecialchars($data['email']) . "</div>
                    </div>";
        
        if (!empty($data['telefono'])) {
            $message .= "
                    <div class='field'>
                        <div class='label'>Teléfono:</div>
                        <div class='value'>" . htmlspecialchars($data['telefono']) . "</div>
                    </div>";
        }
        
        if (!empty($data['empresa'])) {
            $message .= "
                    <div class='field'>
                        <div class='label'>Empresa:</div>
                        <div class='value'>" . htmlspecialchars($data['empresa']) . "</div>
                    </div>";
        }
        
        if (!empty($data['ciudad'])) {
            $message .= "
                    <div class='field'>
                        <div class='label'>Ciudad:</div>
                        <div class='value'>" . htmlspecialchars($data['ciudad']) . "</div>
                    </div>";
        }
        
        if (!empty($data['area'])) {
            $message .= "
                    <div class='field'>
                        <div class='label'>Área:</div>
                        <div class='value'>" . htmlspecialchars($data['area']) . "</div>
                    </div>";
        }
        
        $message .= "
                    <div class='field'>
                        <div class='label'>Mensaje:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($data['mensaje'])) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Este mensaje fue enviado desde el formulario de contacto de Petersen</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Configurar PHPMailer
        require_once __DIR__ . '/../cms/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = $config['port'];
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom($config['from_email'], $config['from_name']);
        
        // Agregar múltiples destinatarios si están separados por coma
        $emails = array_map('trim', explode(',', $recipientEmails));
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($email);
            }
        }
        
        $mail->addReplyTo($data['email'], $data['nombre']);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $message));
        
        // Enviar
        return $mail->send();
        
    } catch (Exception $e) {
        error_log('Error enviando email: ' . $e->getMessage());
        return false;
    }
}
