<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/database.php';
require_once 'includes/site-options.php';

Security::setSecurityHeaders();

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!$auth->isAdmin()) {
    header('Location: index.php?error=no_permission');
    exit;
}

$db = Database::getInstance()->getConnection();
$siteOptions = new SiteOptions($db);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'site_options']);
    } else {
        $action = $_POST['form_action'] ?? 'smtp';
        
        if ($action === 'smtp') {
            $config = [
                'enabled' => isset($_POST['smtp_enabled']) ? 1 : 0,
                'host' => $_POST['smtp_host'] ?? '',
                'port' => $_POST['smtp_port'] ?? 587,
                'username' => $_POST['smtp_username'] ?? '',
                'password' => $_POST['smtp_password'] ?? '',
                'encryption' => $_POST['smtp_encryption'] ?? 'tls',
                'from_email' => $_POST['smtp_from_email'] ?? '',
                'from_name' => $_POST['smtp_from_name'] ?? 'Petersen',
                'recipient_email' => $_POST['contact_recipient_email'] ?? ''
            ];
            
            if ($siteOptions->updateSMTPConfig($config)) {
                $message = 'Configuración SMTP guardada correctamente';
                $messageType = 'success';
                Security::logSecurityEvent('SITE_OPTIONS_UPDATED', ['user_id' => $_SESSION['user_id'], 'type' => 'smtp']);
            } else {
                $message = 'Error al guardar la configuración SMTP';
                $messageType = 'danger';
            }
        } elseif ($action === 'whatsapp') {
            $config = [
                'ventas' => $_POST['whatsapp_ventas'] ?? '',
                'soporte' => $_POST['whatsapp_soporte'] ?? '',
                'rrhh' => $_POST['whatsapp_rrhh'] ?? '',
                'administracion' => $_POST['whatsapp_administracion'] ?? '',
                'general' => $_POST['whatsapp_general'] ?? ''
            ];
            
            if ($siteOptions->updateWhatsAppConfig($config)) {
                $message = 'Configuración de WhatsApp guardada correctamente';
                $messageType = 'success';
                Security::logSecurityEvent('SITE_OPTIONS_UPDATED', ['user_id' => $_SESSION['user_id'], 'type' => 'whatsapp']);
            } else {
                $message = 'Error al guardar la configuración de WhatsApp';
                $messageType = 'danger';
            }
        } elseif ($action === 'social') {
            $config = [
                'facebook' => $_POST['social_facebook'] ?? '',
                'instagram' => $_POST['social_instagram'] ?? '',
                'youtube' => $_POST['social_youtube'] ?? '',
                'linkedin' => $_POST['social_linkedin'] ?? ''
            ];
            
            if ($siteOptions->updateSocialMediaConfig($config)) {
                $message = 'Configuración de redes sociales guardada correctamente';
                $messageType = 'success';
                Security::logSecurityEvent('SITE_OPTIONS_UPDATED', ['user_id' => $_SESSION['user_id'], 'type' => 'social']);
            } else {
                $message = 'Error al guardar la configuración de redes sociales';
                $messageType = 'danger';
            }
        }
    }
}

$smtpConfig = $siteOptions->getSMTPConfig();
$whatsappConfig = $siteOptions->getWhatsAppConfig();
$socialConfig = $siteOptions->getSocialMediaConfig();

$pageTitle = 'Opciones del Sitio';
require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>Opciones del Sitio</h1>
        <p>Configuración del servidor SMTP para envío de correos electrónicos</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Configuración SMTP</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="form_action" value="smtp">
                
                <div class="form-section">
                    <h3>Estado del Servicio</h3>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="smtp_enabled" value="1" <?php echo $smtpConfig['enabled'] ? 'checked' : ''; ?>>
                            <span>Habilitar envío de correos por SMTP</span>
                        </label>
                        <small class="form-text">Si está deshabilitado, los mensajes solo se guardarán en la base de datos</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Configuración del Servidor</h3>
                    
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="smtp_host">Servidor SMTP *</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                   value="<?php echo htmlspecialchars($smtpConfig['host']); ?>" 
                                   placeholder="smtp.gmail.com" required>
                            <small class="form-text">Ejemplo: smtp.gmail.com, smtp.office365.com</small>
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="smtp_port">Puerto *</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                   value="<?php echo htmlspecialchars($smtpConfig['port']); ?>" 
                                   placeholder="587" required>
                            <small class="form-text">Común: 587 (TLS), 465 (SSL)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="smtp_encryption">Tipo de Encriptación *</label>
                        <select class="form-control" id="smtp_encryption" name="smtp_encryption" required>
                            <option value="tls" <?php echo $smtpConfig['encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo $smtpConfig['encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="none" <?php echo $smtpConfig['encryption'] === 'none' ? 'selected' : ''; ?>>Ninguna</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Credenciales de Autenticación</h3>
                    
                    <div class="form-group">
                        <label for="smtp_username">Usuario SMTP *</label>
                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                               value="<?php echo htmlspecialchars($smtpConfig['username']); ?>" 
                               placeholder="usuario@dominio.com" autocomplete="off" required>
                        <small class="form-text">Generalmente es tu dirección de correo electrónico</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_password">Contraseña SMTP</label>
                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                               placeholder="Dejar en blanco para mantener la actual" autocomplete="new-password">
                        <small class="form-text">Para Gmail, usa una "Contraseña de aplicación" en lugar de tu contraseña normal</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Configuración de Remitente</h3>
                    
                    <div class="form-group">
                        <label for="smtp_from_email">Email del Remitente *</label>
                        <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" 
                               value="<?php echo htmlspecialchars($smtpConfig['from_email']); ?>" 
                               placeholder="noreply@petersen.com.py" required>
                        <small class="form-text">Dirección de correo que aparecerá como remitente</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_from_name">Nombre del Remitente *</label>
                        <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" 
                               value="<?php echo htmlspecialchars($smtpConfig['from_name']); ?>" 
                               placeholder="Petersen" required>
                        <small class="form-text">Nombre que aparecerá como remitente</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Configuración de Destinatario</h3>
                    
                    <div class="form-group">
                        <label for="contact_recipient_email">Email de Destino para Formularios *</label>
                        <input type="email" class="form-control" id="contact_recipient_email" name="contact_recipient_email" 
                               value="<?php echo htmlspecialchars($smtpConfig['recipient_email']); ?>" 
                               placeholder="contacto@petersen.com.py" required>
                        <small class="form-text">Los mensajes del formulario de contacto se enviarán a esta dirección</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2>Configuración de WhatsApp</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="form_action" value="whatsapp">
                
                <p class="mb-4">Configura los números de WhatsApp para cada área. Los botones de WhatsApp en el sitio redirigirán a estos números según el área seleccionada.</p>
                
                <div class="form-group">
                    <label for="whatsapp_ventas">WhatsApp - Ventas</label>
                    <input type="text" class="form-control" id="whatsapp_ventas" name="whatsapp_ventas" 
                           value="<?php echo htmlspecialchars($whatsappConfig['ventas']); ?>" 
                           placeholder="595981234567">
                    <small class="form-text">Formato: código de país + número sin espacios ni símbolos (ej: 595981234567)</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_soporte">WhatsApp - Soporte Técnico</label>
                    <input type="text" class="form-control" id="whatsapp_soporte" name="whatsapp_soporte" 
                           value="<?php echo htmlspecialchars($whatsappConfig['soporte']); ?>" 
                           placeholder="595981234567">
                    <small class="form-text">Formato: código de país + número sin espacios ni símbolos</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_rrhh">WhatsApp - Recursos Humanos</label>
                    <input type="text" class="form-control" id="whatsapp_rrhh" name="whatsapp_rrhh" 
                           value="<?php echo htmlspecialchars($whatsappConfig['rrhh']); ?>" 
                           placeholder="595981234567">
                    <small class="form-text">Formato: código de país + número sin espacios ni símbolos</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_administracion">WhatsApp - Administración</label>
                    <input type="text" class="form-control" id="whatsapp_administracion" name="whatsapp_administracion" 
                           value="<?php echo htmlspecialchars($whatsappConfig['administracion']); ?>" 
                           placeholder="595981234567">
                    <small class="form-text">Formato: código de país + número sin espacios ni símbolos</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_general">WhatsApp - General (Otro)</label>
                    <input type="text" class="form-control" id="whatsapp_general" name="whatsapp_general" 
                           value="<?php echo htmlspecialchars($whatsappConfig['general']); ?>" 
                           placeholder="595981234567">
                    <small class="form-text">Número por defecto cuando el área es "Otro" o no está especificada</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración de WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2>Redes Sociales</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="form_action" value="social">
                
                <p class="mb-4">Configura las URLs de las redes sociales que aparecerán en el footer del sitio.</p>
                
                <div class="form-group">
                    <label for="social_facebook">Facebook</label>
                    <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                           value="<?php echo htmlspecialchars($socialConfig['facebook']); ?>" 
                           placeholder="https://facebook.com/tupagina">
                    <small class="form-text">URL completa de tu página de Facebook</small>
                </div>
                
                <div class="form-group">
                    <label for="social_instagram">Instagram</label>
                    <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                           value="<?php echo htmlspecialchars($socialConfig['instagram']); ?>" 
                           placeholder="https://instagram.com/tuusuario">
                    <small class="form-text">URL completa de tu perfil de Instagram</small>
                </div>
                
                <div class="form-group">
                    <label for="social_youtube">YouTube</label>
                    <input type="url" class="form-control" id="social_youtube" name="social_youtube" 
                           value="<?php echo htmlspecialchars($socialConfig['youtube']); ?>" 
                           placeholder="https://youtube.com/@tucanal">
                    <small class="form-text">URL completa de tu canal de YouTube</small>
                </div>
                
                <div class="form-group">
                    <label for="social_linkedin">LinkedIn</label>
                    <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" 
                           value="<?php echo htmlspecialchars($socialConfig['linkedin']); ?>" 
                           placeholder="https://linkedin.com/company/tuempresa">
                    <small class="form-text">URL completa de tu página de LinkedIn</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Redes Sociales
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2>Información de Ayuda</h2>
        </div>
        <div class="card-body">
            <h4>Configuración para Gmail</h4>
            <ul>
                <li><strong>Servidor:</strong> smtp.gmail.com</li>
                <li><strong>Puerto:</strong> 587 (TLS) o 465 (SSL)</li>
                <li><strong>Usuario:</strong> tu-email@gmail.com</li>
                <li><strong>Contraseña:</strong> Usa una "Contraseña de aplicación" (no tu contraseña normal)</li>
                <li><strong>Cómo crear contraseña de aplicación:</strong> 
                    <ol>
                        <li>Ve a tu cuenta de Google</li>
                        <li>Seguridad → Verificación en dos pasos (debe estar activada)</li>
                        <li>Contraseñas de aplicaciones → Generar nueva</li>
                    </ol>
                </li>
            </ul>

            <h4 class="mt-4">Configuración para Office 365 / Outlook</h4>
            <ul>
                <li><strong>Servidor:</strong> smtp.office365.com</li>
                <li><strong>Puerto:</strong> 587 (TLS)</li>
                <li><strong>Usuario:</strong> tu-email@tudominio.com</li>
                <li><strong>Contraseña:</strong> Tu contraseña de Office 365</li>
            </ul>

            <h4 class="mt-4">Otros Proveedores Comunes</h4>
            <ul>
                <li><strong>Yahoo:</strong> smtp.mail.yahoo.com (Puerto 587)</li>
                <li><strong>Hotmail:</strong> smtp.live.com (Puerto 587)</li>
                <li><strong>SendGrid:</strong> smtp.sendgrid.net (Puerto 587)</li>
            </ul>
        </div>
    </div>
</div>

<style>
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e0e0e0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    font-size: 1.2rem;
    color: #2c3e5c;
    margin-bottom: 1rem;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-row .form-group {
    flex: 1;
}

.col-md-8 {
    flex: 0 0 66.666667%;
}

.col-md-4 {
    flex: 0 0 33.333333%;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.card.mt-4 {
    margin-top: 2rem;
}

.card-body h4 {
    color: #2c3e5c;
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
}

.card-body ul {
    margin-left: 1.5rem;
}

.card-body ol {
    margin-left: 1.5rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>
