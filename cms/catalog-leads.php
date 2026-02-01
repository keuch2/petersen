<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/database.php';
require_once 'includes/catalog.php';
require_once 'includes/catalog-lead.php';

Security::setSecurityHeaders();

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$catalogModel = new Catalog($db);
$catalogLead = new CatalogLead($db);

$catalogId = $_GET['catalog_id'] ?? null;
$action = $_GET['action'] ?? 'list';

// Exportar a CSV
if ($action === 'export' && $catalogId) {
    $catalogLead->exportToCSV($catalogId);
    exit;
}

$message = '';
$messageType = '';

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
    } else {
        $postAction = $_POST['action'] ?? '';
        
        if ($postAction === 'delete') {
            $leadId = $_POST['lead_id'] ?? null;
            $result = $catalogLead->delete($leadId);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                Security::logSecurityEvent('CATALOG_LEAD_DELETED', ['lead_id' => $leadId]);
            }
        }
    }
}

$currentCatalog = $catalogId ? $catalogModel->getById($catalogId) : null;
$leads = $catalogId ? $catalogLead->getByCatalog($catalogId) : $catalogLead->getAll();
$stats = $catalogId ? $catalogLead->getStatsByCatalog($catalogId) : null;

$pageTitle = 'Clientes de Catálogos';
require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>Clientes de Catálogos</h1>
        <?php if ($currentCatalog): ?>
            <p>Leads del catálogo: <strong><?php echo htmlspecialchars($currentCatalog['title']); ?></strong></p>
        <?php else: ?>
            <p>Todos los leads de catálogos</p>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($stats): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd;">
                    <i class="fas fa-download" style="color: #2196f3;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['total_downloads']; ?></div>
                    <div class="stat-label">Total Descargas</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="fas fa-users" style="color: #4caf50;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['unique_emails']; ?></div>
                    <div class="stat-label">Emails Únicos</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="fas fa-calendar" style="color: #ff9800;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        <?php 
                        if ($stats['last_download']) {
                            $date = new DateTime($stats['last_download']);
                            echo $date->format('d/m/Y');
                        } else {
                            echo '-';
                        }
                        ?>
                    </div>
                    <div class="stat-label">Última Descarga</div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Listado de Clientes</h2>
            <div class="header-actions">
                <?php if ($catalogId): ?>
                    <a href="?catalog_id=<?php echo $catalogId; ?>&action=export" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                    </a>
                    <a href="catalogs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Catálogos
                    </a>
                <?php else: ?>
                    <a href="catalogs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Ir a Catálogos
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($leads)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No hay leads registrados</p>
                    <?php if (!$catalogId): ?>
                        <a href="catalogs.php" class="btn btn-primary">Ver Catálogos</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php if (!$catalogId): ?>
                                    <th>Catálogo</th>
                                <?php endif; ?>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Empresa</th>
                                <th>Ciudad</th>
                                <th>Fecha de Descarga</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?php echo $lead['id']; ?></td>
                                    <?php if (!$catalogId): ?>
                                        <td>
                                            <a href="?catalog_id=<?php echo $lead['catalog_id']; ?>">
                                                <?php echo htmlspecialchars($lead['catalog_title']); ?>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                    <td><strong><?php echo htmlspecialchars($lead['name']); ?></strong></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
                                            <?php echo htmlspecialchars($lead['email']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($lead['phone'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($lead['company'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($lead['city'] ?: '-'); ?></td>
                                    <td>
                                        <small>
                                            <?php 
                                            $date = new DateTime($lead['downloaded_at']);
                                            echo $date->format('d/m/Y H:i');
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewLead(<?php echo $lead['id']; ?>)" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este lead?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
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

<!-- Modal para ver lead completo -->
<div id="leadModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detalles del Lead</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="leadDetails">
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

.header-actions {
    display: flex;
    gap: 0.5rem;
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
const leads = <?php echo json_encode($leads); ?>;

function viewLead(id) {
    const lead = leads.find(l => l.id == id);
    if (!lead) return;
    
    const details = `
        <div class="detail-row">
            <div class="detail-label">ID:</div>
            <div class="detail-value">${lead.id}</div>
        </div>
        ${!<?php echo $catalogId ? 'true' : 'false'; ?> ? `
        <div class="detail-row">
            <div class="detail-label">Catálogo:</div>
            <div class="detail-value">${escapeHtml(lead.catalog_title)}</div>
        </div>` : ''}
        <div class="detail-row">
            <div class="detail-label">Nombre:</div>
            <div class="detail-value">${escapeHtml(lead.name)}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email:</div>
            <div class="detail-value"><a href="mailto:${escapeHtml(lead.email)}">${escapeHtml(lead.email)}</a></div>
        </div>
        ${lead.phone ? `
        <div class="detail-row">
            <div class="detail-label">Teléfono:</div>
            <div class="detail-value">${escapeHtml(lead.phone)}</div>
        </div>` : ''}
        ${lead.company ? `
        <div class="detail-row">
            <div class="detail-label">Empresa:</div>
            <div class="detail-value">${escapeHtml(lead.company)}</div>
        </div>` : ''}
        ${lead.city ? `
        <div class="detail-row">
            <div class="detail-label">Ciudad:</div>
            <div class="detail-value">${escapeHtml(lead.city)}</div>
        </div>` : ''}
        <div class="detail-row">
            <div class="detail-label">Fecha de Descarga:</div>
            <div class="detail-value">${new Date(lead.downloaded_at).toLocaleString('es-PY')}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">IP:</div>
            <div class="detail-value">${escapeHtml(lead.ip_address)}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">User Agent:</div>
            <div class="detail-value" style="word-break: break-all; font-size: 0.85em;">${escapeHtml(lead.user_agent)}</div>
        </div>
    `;
    
    document.getElementById('leadDetails').innerHTML = details;
    document.getElementById('leadModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('leadModal').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.onclick = function(event) {
    const modal = document.getElementById('leadModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
