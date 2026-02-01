<?php
// Handler unificado para todos los formularios del sitio
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

// Validar campos requeridos básicos
$required = ['nombre', 'email'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nombre y email son obligatorios']);
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
    
    // Determinar el tipo de formulario y área
    $formType = $_POST['form_type'] ?? 'general';
    $area = $_POST['area'] ?? determineArea($formType);
    
    // Preparar datos
    $data = [
        'nombre' => trim($_POST['nombre']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'empresa' => trim($_POST['empresa'] ?? ''),
        'ciudad' => trim($_POST['ciudad'] ?? ''),
        'area' => $area,
        'mensaje' => trim($_POST['mensaje'] ?? buildMessage($formType, $_POST))
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
    
    if ($smtpConfig['enabled'] && !empty($smtpConfig['recipient_email'])) {
        $emailSent = sendFormEmail($data, $smtpConfig, $formType);
        
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
    error_log('Error en form-handler: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al procesar el mensaje']);
}

function determineArea($formType) {
    $areaMap = [
        'division_bosque' => 'ventas',
        'division_construccion' => 'ventas',
        'division_ferreteria' => 'ventas',
        'division_industrial' => 'ventas',
        'division_mecanica' => 'ventas',
        'division_metalurgica' => 'ventas',
        'servicios' => 'soporte',
        'catalogo' => 'ventas',
        'contacto' => 'otro'
    ];
    
    return $areaMap[$formType] ?? 'otro';
}

function buildMessage($formType, $data) {
    if (!empty($data['mensaje'])) {
        return $data['mensaje'];
    }
    
    $messages = [
        'division_bosque' => 'Solicitud de asesoría para División Bosque & Jardín',
        'division_construccion' => 'Solicitud de asesoría para División Construcción',
        'division_ferreteria' => 'Solicitud de asesoría para División Ferretería',
        'division_industrial' => 'Solicitud de asesoría para División Industrial',
        'division_mecanica' => 'Solicitud de asesoría para División Mecánica',
        'division_metalurgica' => 'Solicitud de asesoría para División Metalúrgica',
        'servicios' => 'Solicitud de información sobre servicios',
        'catalogo' => 'Solicitud de descarga de catálogo: ' . ($data['catalog_name'] ?? 'No especificado')
    ];
    
    return $messages[$formType] ?? 'Consulta general';
}

function sendFormEmail($data, $config, $formType) {
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
        
        // Mapear tipos de formulario a nombres
        $formNames = [
            'division_bosque' => 'División Bosque & Jardín',
            'division_construccion' => 'División Construcción',
            'division_ferreteria' => 'División Ferretería',
            'division_industrial' => 'División Industrial',
            'division_mecanica' => 'División Mecánica',
            'division_metalurgica' => 'División Metalúrgica',
            'servicios' => 'Servicios',
            'catalogo' => 'Descarga de Catálogo',
            'contacto' => 'Contacto'
        ];
        
        $formName = $formNames[$formType] ?? 'Formulario General';
        
        // Crear el asunto con el área y tipo de formulario
        $subject = "[" . $areaNombre . "] " . $formName . " - " . $data['nombre'];
        
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
                    <h2>" . $formName . "</h2>
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
        
        $message .= "
                    <div class='field'>
                        <div class='label'>Área:</div>
                        <div class='value'>" . htmlspecialchars($areaNombre) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Mensaje:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($data['mensaje'])) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Este mensaje fue enviado desde " . $formName . " de Petersen</p>
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
        $mail->addAddress($config['recipient_email']);
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
