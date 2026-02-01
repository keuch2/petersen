<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/database.php';
require_once 'includes/catalog.php';
require_once 'includes/upload.php';

Security::setSecurityHeaders();

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$catalog = new Catalog($db);
$upload = new Upload();

$message = '';
$messageType = '';
$action = $_GET['action'] ?? 'list';
$catalogId = $_GET['id'] ?? null;

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'catalogs']);
    } else {
        $postAction = $_POST['action'] ?? '';
        
        if ($postAction === 'create' || $postAction === 'update') {
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Manejar subida de PDF
            if (!empty($_FILES['pdf_file']['name'])) {
                $uploadDir = __DIR__ . '/../assets/catalogs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $pdfFile = $_FILES['pdf_file'];
                $pdfName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $pdfFile['name']);
                $pdfPath = $uploadDir . $pdfName;
                
                if (move_uploaded_file($pdfFile['tmp_name'], $pdfPath)) {
                    $data['pdf_filename'] = $pdfName;
                    $data['pdf_path'] = $pdfPath;
                }
            } elseif ($postAction === 'update' && $catalogId) {
                // Mantener PDF existente
                $existing = $catalog->getById($catalogId);
                $data['pdf_filename'] = $existing['pdf_filename'];
                $data['pdf_path'] = $existing['pdf_path'];
            }
            
            // Manejar subida de imagen de portada
            if (!empty($_FILES['cover_image']['name'])) {
                $uploadDir = __DIR__ . '/../assets/catalogs/covers/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $imageFile = $_FILES['cover_image'];
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $imageFile['name']);
                $imagePath = $uploadDir . $imageName;
                
                if (move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
                    $data['cover_image'] = 'assets/catalogs/covers/' . $imageName;
                }
            } elseif ($postAction === 'update' && $catalogId) {
                // Mantener imagen existente
                $existing = $catalog->getById($catalogId);
                $data['cover_image'] = $existing['cover_image'];
            }
            
            if ($postAction === 'create') {
                $result = $catalog->create($data);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                
                if ($result['success']) {
                    Security::logSecurityEvent('CATALOG_CREATED', ['catalog_id' => $result['id']]);
                    header('Location: catalogs.php?message=created');
                    exit;
                }
            } else {
                $result = $catalog->update($catalogId, $data);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                
                if ($result['success']) {
                    Security::logSecurityEvent('CATALOG_UPDATED', ['catalog_id' => $catalogId]);
                    header('Location: catalogs.php?message=updated');
                    exit;
                }
            }
        }
        
        if ($postAction === 'delete' && $catalogId) {
            $result = $catalog->delete($catalogId);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                Security::logSecurityEvent('CATALOG_DELETED', ['catalog_id' => $catalogId]);
                header('Location: catalogs.php?message=deleted');
                exit;
            }
        }
    }
}

// Mensajes de URL
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'created':
            $message = 'Catálogo creado correctamente';
            $messageType = 'success';
            break;
        case 'updated':
            $message = 'Catálogo actualizado correctamente';
            $messageType = 'success';
            break;
        case 'deleted':
            $message = 'Catálogo eliminado correctamente';
            $messageType = 'success';
            break;
    }
}

$catalogs = $catalog->getAll();
$stats = $catalog->getStats();
$currentCatalog = $catalogId ? $catalog->getById($catalogId) : null;

$pageTitle = 'Catálogos';
require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <h1>Catálogos</h1>
        <p>Gestión de catálogos PDF y leads de descarga</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd;">
                    <i class="fas fa-file-pdf" style="color: #2196f3;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Total Catálogos</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['active']; ?></div>
                    <div class="stat-label">Activos</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="fas fa-users" style="color: #ff9800;"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $stats['total_leads']; ?></div>
                    <div class="stat-label">Total Leads</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Listado de Catálogos</h2>
                <a href="?action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Catálogo
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($catalogs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-pdf"></i>
                        <p>No hay catálogos creados</p>
                        <a href="?action=create" class="btn btn-primary">Crear primer catálogo</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Portada</th>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Leads</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($catalogs as $cat): ?>
                                    <tr>
                                        <td>
                                            <?php if ($cat['cover_image']): ?>
                                                <img src="../<?php echo htmlspecialchars($cat['cover_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($cat['title']); ?>" 
                                                     style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                    <i class="fas fa-file-pdf" style="font-size: 24px; color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($cat['title']); ?></strong>
                                            <?php if ($cat['description']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($cat['description'], 0, 50)); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($cat['category'] ?: '-'); ?></td>
                                        <td>
                                            <a href="catalog-leads.php?catalog_id=<?php echo $cat['id']; ?>" class="badge badge-info">
                                                <?php echo $cat['lead_count']; ?> leads
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($cat['status'] === 'active'): ?>
                                                <span class="badge badge-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y', strtotime($cat['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?action=edit&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-info" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="catalog-leads.php?catalog_id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-success" title="Ver leads">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este catálogo?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete">
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

    <?php elseif ($action === 'create' || $action === 'edit'): ?>
        <div class="card">
            <div class="card-header">
                <h2><?php echo $action === 'create' ? 'Nuevo Catálogo' : 'Editar Catálogo'; ?></h2>
                <a href="catalogs.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                    
                    <div class="form-group">
                        <label for="title">Título del Catálogo *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($currentCatalog['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($currentCatalog['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Categoría</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               value="<?php echo htmlspecialchars($currentCatalog['category'] ?? ''); ?>" 
                               placeholder="Ej: Bosque & Jardín, Industrial, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="pdf_file">Archivo PDF <?php echo $action === 'create' ? '*' : '(dejar vacío para mantener actual)'; ?></label>
                        <?php if ($action === 'edit' && !empty($currentCatalog['pdf_filename'])): ?>
                            <div class="mb-2">
                                <small class="text-muted">Archivo actual: <?php echo htmlspecialchars($currentCatalog['pdf_filename']); ?></small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" <?php echo $action === 'create' ? 'required' : ''; ?>>
                        <small class="form-text">Formato: PDF. Tamaño máximo: 10MB</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cover_image">Imagen de Portada (opcional)</label>
                        <?php if ($action === 'edit' && !empty($currentCatalog['cover_image'])): ?>
                            <div class="mb-2">
                                <img src="../<?php echo htmlspecialchars($currentCatalog['cover_image']); ?>" 
                                     alt="Portada actual" 
                                     style="max-width: 200px; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                        <small class="form-text">Formato: JPG, PNG. Tamaño recomendado: 300x400px</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" <?php echo ($currentCatalog['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactive" <?php echo ($currentCatalog['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $action === 'create' ? 'Crear Catálogo' : 'Actualizar Catálogo'; ?>
                        </button>
                        <a href="catalogs.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
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
    text-decoration: none;
    display: inline-block;
}

.badge-info {
    background: #e3f2fd;
    color: #2196f3;
}

.badge-success {
    background: #e8f5e9;
    color: #4caf50;
}

.badge-secondary {
    background: #f5f5f5;
    color: #666;
}
</style>

<?php require_once 'includes/footer.php'; ?>
