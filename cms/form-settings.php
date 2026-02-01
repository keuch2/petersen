<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/form-settings.php';

// Verificar autenticación
$auth = new Auth();
$auth->requireLogin();

$formSettings = new FormSettings();
$message = '';
$messageType = '';

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Token de seguridad inválido';
        $messageType = 'error';
    } else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $emails = filter_input(INPUT_POST, 'emails', FILTER_SANITIZE_STRING);
        $whatsapp = filter_input(INPUT_POST, 'whatsapp', FILTER_SANITIZE_STRING);
        
        if ($formSettings->update($id, $emails, $whatsapp)) {
            $message = 'Configuración actualizada correctamente';
            $messageType = 'success';
        } else {
            $message = 'Error al actualizar la configuración';
            $messageType = 'error';
        }
    }
}

// Generar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$forms = $formSettings->getAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Configuración de Formularios
                </h1>
                <p class="text-muted">Configure los destinatarios de emails y números de WhatsApp para cada formulario del sitio</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($forms as $form): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card form-settings-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-<?php 
                                        echo match($form['form_type']) {
                                            'contacto' => 'comments',
                                            'trabajo' => 'briefcase',
                                            'cotizacion' => 'file-invoice',
                                            'catalogo' => 'book',
                                            default => 'envelope'
                                        };
                                    ?>"></i>
                                    <?php echo htmlspecialchars($form['form_name']); ?>
                                </h5>
                                <small class="text-muted">Tipo: <?php echo htmlspecialchars($form['form_type']); ?></small>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $form['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="emails_<?php echo $form['id']; ?>" class="form-label">
                                            <i class="fas fa-envelope"></i>
                                            Emails Destinatarios *
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="emails_<?php echo $form['id']; ?>" 
                                            name="emails" 
                                            value="<?php echo htmlspecialchars($form['emails']); ?>"
                                            required
                                            placeholder="email1@ejemplo.com, email2@ejemplo.com"
                                        >
                                        <small class="form-text text-muted">
                                            Ingrese uno o más emails separados por coma
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="whatsapp_<?php echo $form['id']; ?>" class="form-label">
                                            <i class="fab fa-whatsapp"></i>
                                            Número de WhatsApp
                                        </label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="whatsapp_<?php echo $form['id']; ?>" 
                                            name="whatsapp" 
                                            value="<?php echo htmlspecialchars($form['whatsapp']); ?>"
                                            placeholder="595986357950"
                                        >
                                        <small class="form-text text-muted">
                                            Número con código de país sin el símbolo +. Ejemplo: 595986357950
                                        </small>
                                        <?php if (!empty($form['whatsapp'])): ?>
                                            <div class="mt-2">
                                                <a href="https://wa.me/<?php echo htmlspecialchars($form['whatsapp']); ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fab fa-whatsapp"></i>
                                                    Probar WhatsApp
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i>
                                            Actualizado: <?php echo date('d/m/Y H:i', strtotime($form['updated_at'])); ?>
                                        </small>
                                        <button type="submit" name="update_settings" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Información adicional -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i>
                        Información
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-envelope"></i> Emails</h6>
                            <ul class="small">
                                <li>Puede ingresar <strong>múltiples emails</strong> separados por coma</li>
                                <li>Todos los emails recibirán las notificaciones</li>
                                <li>Los emails se validan automáticamente al guardar</li>
                                <li>Emails inválidos serán ignorados</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fab fa-whatsapp"></i> WhatsApp</h6>
                            <ul class="small">
                                <li>Formato: <code>595986357950</code> (código país + número)</li>
                                <li><strong>Sin espacios, guiones ni el símbolo +</strong></li>
                                <li>Mínimo 10 dígitos</li>
                                <li>Use el botón "Probar WhatsApp" para verificar</li>
                            </ul>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6><i class="fas fa-code"></i> Uso en el Código</h6>
                    <div class="bg-light p-3 rounded">
                        <code class="small">
                            // Obtener emails de un formulario<br>
                            $emails = FormSettings::getFormEmails('contacto');<br><br>
                            // Obtener WhatsApp de un formulario<br>
                            $whatsapp = FormSettings::getFormWhatsapp('contacto');
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-settings-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.form-settings-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.form-settings-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

.form-settings-card .card-header h5 i {
    margin-right: 8px;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-label i {
    margin-right: 5px;
    color: #667eea;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.alert i {
    margin-right: 8px;
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.page-header h1 {
    color: #495057;
    font-weight: 600;
}

.page-header h1 i {
    color: #667eea;
    margin-right: 10px;
}
</style>

<?php include 'includes/footer.php'; ?>
