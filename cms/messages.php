<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/database.php';
require_once 'includes/contact-message.php';

Security::setSecurityHeaders();

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$contactMessage = new ContactMessage($db);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'messages']);
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'mark_read') {
            $contactMessage->markAsRead($_POST['message_id']);
            $message = 'Mensaje marcado como leído';
            $messageType = 'success';
        }
        
        if ($action === 'mark_replied') {
            $contactMessage->markAsReplied($_POST['message_id']);
            $message = 'Mensaje marcado como respondido';
            $messageType = 'success';
        }
        
        if ($action === 'delete') {
            $result = $contactMessage->delete($_POST['message_id']);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                Security::logSecurityEvent('MESSAGE_DELETED', ['message_id' => $_POST['message_id']]);
            }
        }
    }
}

$filter = $_GET['filter'] ?? 'all';
$status = $filter === 'all' ? null : $filter;
$messages = $contactMessage->getAll($status);
$stats = $contactMessage->getStats();

$pageTitle = 'Mensajes de Contacto';
require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>Mensajes de Contacto</h1>
        <p>Gestión de mensajes recibidos desde el formulario de contacto</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd;">
                <i class="fas fa-envelope" style="color: #2196f3;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0;">
                <i class="fas fa-envelope-open" style="color: #ff9800;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['unread']; ?></div>
                <div class="stat-label">Sin Leer</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9;">
                <i class="fas fa-check-circle" style="color: #4caf50;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['read']; ?></div>
                <div class="stat-label">Leídos</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f3e5f5;">
                <i class="fas fa-reply" style="color: #9c27b0;"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['replied']; ?></div>
                <div class="stat-label">Respondidos</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="filter-tabs">
                <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    Todos (<?php echo $stats['total']; ?>)
                </a>
                <a href="?filter=unread" class="filter-tab <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                    Sin Leer (<?php echo $stats['unread']; ?>)
                </a>
                <a href="?filter=read" class="filter-tab <?php echo $filter === 'read' ? 'active' : ''; ?>">
                    Leídos (<?php echo $stats['read']; ?>)
                </a>
                <a href="?filter=replied" class="filter-tab <?php echo $filter === 'replied' ? 'active' : ''; ?>">
                    Respondidos (<?php echo $stats['replied']; ?>)
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No hay mensajes para mostrar</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">Estado</th>
                                <th width="15%">Nombre</th>
                                <th width="15%">Email</th>
                                <th width="10%">Teléfono</th>
                                <th width="10%">Empresa</th>
                                <th width="10%">Área</th>
                                <th width="20%">Mensaje</th>
                                <th width="10%">Fecha</th>
                                <th width="5%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="message-row <?php echo $msg['status']; ?>">
                                    <td>
                                        <?php if ($msg['status'] === 'unread'): ?>
                                            <span class="badge badge-warning">Sin leer</span>
                                        <?php elseif ($msg['status'] === 'read'): ?>
                                            <span class="badge badge-info">Leído</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Respondido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($msg['nombre']); ?></strong>
                                        <?php if ($msg['ciudad']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($msg['ciudad']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>">
                                            <?php echo htmlspecialchars($msg['email']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($msg['telefono'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($msg['empresa'] ?: '-'); ?></td>
                                    <td>
                                        <?php 
                                        $areas = [
                                            'ventas' => 'Ventas',
                                            'soporte' => 'Soporte',
                                            'rrhh' => 'RRHH',
                                            'administracion' => 'Administración',
                                            'otro' => 'Otro'
                                        ];
                                        echo $areas[$msg['area']] ?? $msg['area'];
                                        ?>
                                    </td>
                                    <td>
                                        <div class="message-preview" title="<?php echo htmlspecialchars($msg['mensaje']); ?>">
                                            <?php echo htmlspecialchars(substr($msg['mensaje'], 0, 50)); ?>
                                            <?php if (strlen($msg['mensaje']) > 50): ?>...<?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <?php 
                                            $date = new DateTime($msg['created_at']);
                                            echo $date->format('d/m/Y H:i');
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewMessage(<?php echo $msg['id']; ?>)" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if ($msg['status'] === 'unread'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Marcar como leído">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($msg['status'] === 'read'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="mark_replied">
                                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Marcar como respondido">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este mensaje?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

<!-- Modal para ver mensaje completo -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detalles del Mensaje</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="messageDetails">
            <!-- Se llenará con JavaScript -->
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e5c;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.3s;
}

.filter-tab:hover {
    background: #e9ecef;
    color: #495057;
}

.filter-tab.active {
    background: #2c3e5c;
    color: white;
}

.message-row.unread {
    background: #fff3e0;
    font-weight: 500;
}

.message-preview {
    cursor: help;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-warning {
    background: #fff3e0;
    color: #ff9800;
}

.badge-info {
    background: #e3f2fd;
    color: #2196f3;
}

.badge-success {
    background: #e8f5e9;
    color: #4caf50;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 700px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.close {
    font-size: 2rem;
    font-weight: 300;
    color: #999;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.detail-row {
    margin-bottom: 1rem;
}

.detail-label {
    font-weight: 600;
    color: #2c3e5c;
    margin-bottom: 0.25rem;
}

.detail-value {
    color: #495057;
}
</style>

<script>
const messages = <?php echo json_encode($messages); ?>;

function viewMessage(id) {
    const message = messages.find(m => m.id == id);
    if (!message) return;
    
    const details = `
        <div class="detail-row">
            <div class="detail-label">Nombre:</div>
            <div class="detail-value">${escapeHtml(message.nombre)}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email:</div>
            <div class="detail-value"><a href="mailto:${escapeHtml(message.email)}">${escapeHtml(message.email)}</a></div>
        </div>
        ${message.telefono ? `
        <div class="detail-row">
            <div class="detail-label">Teléfono:</div>
            <div class="detail-value">${escapeHtml(message.telefono)}</div>
        </div>` : ''}
        ${message.empresa ? `
        <div class="detail-row">
            <div class="detail-label">Empresa:</div>
            <div class="detail-value">${escapeHtml(message.empresa)}</div>
        </div>` : ''}
        ${message.ciudad ? `
        <div class="detail-row">
            <div class="detail-label">Ciudad:</div>
            <div class="detail-value">${escapeHtml(message.ciudad)}</div>
        </div>` : ''}
        <div class="detail-row">
            <div class="detail-label">Área:</div>
            <div class="detail-value">${escapeHtml(message.area)}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Mensaje:</div>
            <div class="detail-value" style="white-space: pre-wrap;">${escapeHtml(message.mensaje)}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Fecha:</div>
            <div class="detail-value">${new Date(message.created_at).toLocaleString('es-PY')}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">IP:</div>
            <div class="detail-value">${escapeHtml(message.ip_address)}</div>
        </div>
    `;
    
    document.getElementById('messageDetails').innerHTML = details;
    document.getElementById('messageModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.onclick = function(event) {
    const modal = document.getElementById('messageModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
