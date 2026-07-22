<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/database.php';
require_once 'includes/feria.php';

Security::setSecurityHeaders();

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$feria = new Feria($db);

// Exportar registros a CSV
if (($_GET['action'] ?? '') === 'export') {
    $feria->exportToCSV();
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'feria_admin']);
    } else {
        $action = $_POST['form_action'] ?? '';

        if ($action === 'config') {
            $data = [
                'activa'          => isset($_POST['feria_activa']) ? 1 : 0,
                'titulo'          => trim($_POST['feria_titulo'] ?? ''),
                'subtitulo'       => trim($_POST['feria_subtitulo'] ?? ''),
                'banner_alt'      => trim($_POST['feria_banner_alt'] ?? ''),
                'fecha_inicio'    => trim($_POST['feria_fecha_inicio'] ?? ''),
                'fecha_fin'       => trim($_POST['feria_fecha_fin'] ?? ''),
                'lugar'           => trim($_POST['feria_lugar'] ?? ''),
                'texto'           => trim($_POST['feria_texto'] ?? ''),
                'form_titulo'     => trim($_POST['feria_form_titulo'] ?? ''),
                'form_texto'      => trim($_POST['feria_form_texto'] ?? ''),
                'countdown_texto' => trim($_POST['feria_countdown_texto'] ?? ''),
                'mensaje_final'   => trim($_POST['feria_mensaje_final'] ?? ''),
            ];

            // Subida del banner (opcional: si no se sube, se mantiene el actual)
            if (!empty($_FILES['feria_banner']['name'])) {
                $validation = Security::validateFileUpload(
                    $_FILES['feria_banner'],
                    ['image/jpeg', 'image/png', 'image/webp'],
                    5 * 1024 * 1024
                );

                if (!$validation['valid']) {
                    $message = 'Error en el banner: ' . implode(', ', $validation['errors']);
                    $messageType = 'danger';
                } else {
                    $uploadDir = __DIR__ . '/../assets/images/feria/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $bannerName = time() . '_' . Security::sanitizeFilename($_FILES['feria_banner']['name']);
                    if (move_uploaded_file($_FILES['feria_banner']['tmp_name'], $uploadDir . $bannerName)) {
                        $data['banner'] = 'assets/images/feria/' . $bannerName;
                    } else {
                        $message = 'No se pudo guardar el banner';
                        $messageType = 'danger';
                    }
                }
            }

            if ($messageType !== 'danger') {
                if ($feria->updateConfig($data)) {
                    $message = 'Configuración de la feria guardada correctamente';
                    $messageType = 'success';
                    Security::logSecurityEvent('FERIA_CONFIG_UPDATED', ['user_id' => $_SESSION['user_id']]);
                } else {
                    $message = 'Error al guardar la configuración';
                    $messageType = 'danger';
                }
            }
        } elseif ($action === 'delete_lead') {
            $result = $feria->deleteLead($_POST['lead_id'] ?? null);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        }
    }
}

$config = $feria->getConfig();
$leads = $feria->getAllLeads();
$stats = $feria->getStats();

// Los <input type="datetime-local"> requieren el formato Y-m-dTH:i
$inputInicio = $config['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($config['fecha_inicio'])) : '';
$inputFin = $config['fecha_fin'] ? date('Y-m-d\TH:i', strtotime($config['fecha_fin'])) : '';

$pageTitle = 'La Feria Petersen';
require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>La Feria Petersen</h1>
        <p>Configurá el banner, la cuenta regresiva y el contenido de la landing <a href="../laferia" target="_blank">/laferia</a></p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd;">
                <i class="fas fa-users" style="color: #2196f3;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['total'] ?: 0; ?></div>
                <div class="stat-label">Registros Totales</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9;">
                <i class="fas fa-envelope" style="color: #4caf50;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['con_email'] ?: 0; ?></div>
                <div class="stat-label">Con Email</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0;">
                <i class="fas fa-map-marker-alt" style="color: #ff9800;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['ciudades'] ?: 0; ?></div>
                <div class="stat-label">Ciudades</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fce4ec;">
                <i class="fas fa-clock" style="color: #e91e63;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">
                    <?php echo $stats['ultimo'] ? date('d/m/Y', strtotime($stats['ultimo'])) : '-'; ?>
                </div>
                <div class="stat-label">Último Registro</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Configuración de la Landing</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="form_action" value="config">

                <div class="form-section">
                    <h3>Banner Hero</h3>

                    <?php if ($config['banner']): ?>
                        <div class="form-group">
                            <label>Banner actual</label>
                            <div>
                                <img src="../<?php echo htmlspecialchars($config['banner']); ?>"
                                     alt="Banner actual de la feria"
                                     style="max-width: 100%; max-height: 220px; border-radius: 6px; border: 1px solid #e0e0e0;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="feria_banner">Subir nuevo banner</label>
                        <input type="file" class="form-control" id="feria_banner" name="feria_banner" accept="image/jpeg,image/png,image/webp">
                        <small class="form-text">JPG, PNG o WebP. Máximo 5 MB. Recomendado: 1920×800 px. Dejalo vacío para mantener el banner actual.</small>
                    </div>

                    <div class="form-group">
                        <label for="feria_banner_alt">Texto alternativo del banner</label>
                        <input type="text" class="form-control" id="feria_banner_alt" name="feria_banner_alt"
                               value="<?php echo htmlspecialchars($config['banner_alt']); ?>"
                               placeholder="La Feria Petersen 2026">
                        <small class="form-text">Describe la imagen para accesibilidad y buscadores</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Encabezado</h3>

                    <div class="form-group">
                        <label for="feria_titulo">Título *</label>
                        <input type="text" class="form-control" id="feria_titulo" name="feria_titulo"
                               value="<?php echo htmlspecialchars($config['titulo']); ?>"
                               placeholder="La Feria Petersen" required>
                    </div>

                    <div class="form-group">
                        <label for="feria_subtitulo">Subtítulo</label>
                        <input type="text" class="form-control" id="feria_subtitulo" name="feria_subtitulo"
                               value="<?php echo htmlspecialchars($config['subtitulo']); ?>"
                               placeholder="Tres días de ofertas y demostraciones en vivo">
                    </div>

                    <div class="form-group">
                        <label for="feria_lugar">Lugar</label>
                        <input type="text" class="form-control" id="feria_lugar" name="feria_lugar"
                               value="<?php echo htmlspecialchars($config['lugar']); ?>"
                               placeholder="Casa Central Petersen, Asunción">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Cuenta Regresiva</h3>

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="feria_fecha_inicio">Fecha y hora de inicio *</label>
                            <input type="datetime-local" class="form-control" id="feria_fecha_inicio" name="feria_fecha_inicio"
                                   value="<?php echo htmlspecialchars($inputInicio); ?>">
                            <small class="form-text">La cuenta regresiva apunta a esta fecha. Si se deja vacía, no se muestra.</small>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="feria_fecha_fin">Fecha de cierre</label>
                            <input type="datetime-local" class="form-control" id="feria_fecha_fin" name="feria_fecha_fin"
                                   value="<?php echo htmlspecialchars($inputFin); ?>">
                            <small class="form-text">Opcional, para mostrar el rango</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feria_countdown_texto">Texto sobre el contador</label>
                        <input type="text" class="form-control" id="feria_countdown_texto" name="feria_countdown_texto"
                               value="<?php echo htmlspecialchars($config['countdown_texto']); ?>"
                               placeholder="Faltan">
                    </div>

                    <div class="form-group">
                        <label for="feria_mensaje_final">Mensaje cuando la feria comienza</label>
                        <input type="text" class="form-control" id="feria_mensaje_final" name="feria_mensaje_final"
                               value="<?php echo htmlspecialchars($config['mensaje_final']); ?>"
                               placeholder="¡La feria ya comenzó! Te esperamos.">
                        <small class="form-text">Reemplaza al contador una vez alcanzada la fecha de inicio</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Texto sobre la Feria</h3>

                    <div class="form-group">
                        <label for="feria_texto">Descripción</label>
                        <textarea class="form-control" id="feria_texto" name="feria_texto" rows="8"
                                  placeholder="Contá de qué se trata la feria, qué marcas participan y qué van a encontrar los visitantes."><?php echo htmlspecialchars($config['texto']); ?></textarea>
                        <small class="form-text">Separá los párrafos con una línea en blanco</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Formulario de Recordatorio</h3>

                    <div class="form-group">
                        <label for="feria_form_titulo">Título del formulario</label>
                        <input type="text" class="form-control" id="feria_form_titulo" name="feria_form_titulo"
                               value="<?php echo htmlspecialchars($config['form_titulo']); ?>"
                               placeholder="Recibí el recordatorio">
                    </div>

                    <div class="form-group">
                        <label for="feria_form_texto">Texto del formulario</label>
                        <textarea class="form-control" id="feria_form_texto" name="feria_form_texto" rows="3"><?php echo htmlspecialchars($config['form_texto']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="feria_activa" value="1" <?php echo $config['activa'] ? 'checked' : ''; ?>>
                            <span>Aceptar nuevos registros</span>
                        </label>
                        <small class="form-text">Si se desactiva, la landing sigue visible pero el formulario deja de recibir registros</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                    <a href="../laferia" target="_blank" class="btn btn-secondary">Ver landing</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2>Registros de Recordatorio</h2>
            <div class="header-actions">
                <?php if (!empty($leads)): ?>
                    <a href="?action=export" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($leads)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Todavía no hay registros</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Ciudad</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?php echo $lead['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($lead['nombre']); ?></strong></td>
                                    <td>
                                        <a href="https://wa.me/<?php echo htmlspecialchars($lead['telefono']); ?>" target="_blank" rel="noopener">
                                            <?php echo htmlspecialchars($lead['telefono']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($lead['email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
                                                <?php echo htmlspecialchars($lead['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($lead['ciudad'] ?: '-'); ?></td>
                                    <td><small><?php echo date('d/m/Y H:i', strtotime($lead['created_at'])); ?></small></td>
                                    <td>
                                        <form method="POST" action="" style="display:inline;"
                                              onsubmit="return confirm('¿Eliminar este registro?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                            <input type="hidden" name="form_action" value="delete_lead">
                                            <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
</style>

<?php require_once 'includes/footer.php'; ?>
