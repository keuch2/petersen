<?php
$pageTitle = 'Gestión de Medios';
require_once 'includes/header.php';
require_once 'includes/media.php';

$mediaModel = new Media();

$message = '';
$messageType = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'media']);
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'upload' && isset($_FILES['files'])) {
            $uploadedCount = 0;
            $errorCount = 0;
            
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            if (empty($tmpName)) continue;
            
            $file = [
                'name' => $_FILES['files']['name'][$key],
                'type' => $_FILES['files']['type'][$key],
                'tmp_name' => $tmpName,
                'error' => $_FILES['files']['error'][$key],
                'size' => $_FILES['files']['size'][$key]
            ];
            
            $metadata = [
                'title' => $_POST['title'][$key] ?? '',
                'alt_text' => $_POST['alt_text'][$key] ?? '',
                'description' => $_POST['description'][$key] ?? ''
            ];
            
            $result = $mediaModel->upload($file, $_SESSION['user_id'], $metadata);
            
                if ($result['success']) {
                    $uploadedCount++;
                } else {
                    $errorCount++;
                }
            }
            
            if ($uploadedCount > 0) {
                $message = "$uploadedCount archivo(s) subido(s) exitosamente";
                $messageType = 'success';
                Security::logSecurityEvent('MEDIA_UPLOADED', ['count' => $uploadedCount]);
            }
            if ($errorCount > 0) {
                $message .= ($message ? '. ' : '') . "$errorCount archivo(s) con errores";
                $messageType = $uploadedCount > 0 ? 'warning' : 'danger';
            }
        }
        
        if ($action === 'update') {
            $result = $mediaModel->update($_POST['media_id'], [
                'title' => $_POST['title'] ?? '',
                'alt_text' => $_POST['alt_text'] ?? '',
                'description' => $_POST['description'] ?? ''
            ]);
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        }
    
        if ($action === 'delete') {
            $result = $mediaModel->delete($_POST['media_id']);
            
            if ($result['success']) {
                Security::logSecurityEvent('MEDIA_DELETED', ['media_id' => $_POST['media_id']]);
            }
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        }
    }
}

// Obtener filtros
$filterType = $_GET['type'] ?? null;
$searchQuery = $_GET['search'] ?? '';

// Obtener medios
if ($searchQuery) {
    $mediaFiles = $mediaModel->search($searchQuery);
} else {
    $mediaFiles = $mediaModel->getAll($filterType);
}

$stats = $mediaModel->getStats();
?>

<div class="page-header">
    <h1>Gestión de Medios</h1>
    <p>Administra imágenes, videos, documentos y otros archivos</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 30px;">
    <div class="stat-card">
        <h3>Total Archivos</h3>
        <div class="stat-value"><?php echo $stats['total']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Imágenes</h3>
        <div class="stat-value"><?php echo $stats['images']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Videos</h3>
        <div class="stat-value"><?php echo $stats['videos']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Documentos</h3>
        <div class="stat-value"><?php echo $stats['documents']; ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <h2>Biblioteca de Medios</h2>
            
            <!-- Filtros -->
            <div style="display: flex; gap: 10px;">
                <a href="media.php" class="btn btn-sm <?php echo !$filterType ? 'btn-primary' : 'btn-secondary'; ?>">Todos</a>
                <a href="media.php?type=image" class="btn btn-sm <?php echo $filterType === 'image' ? 'btn-primary' : 'btn-secondary'; ?>">Imágenes</a>
                <a href="media.php?type=video" class="btn btn-sm <?php echo $filterType === 'video' ? 'btn-primary' : 'btn-secondary'; ?>">Videos</a>
                <a href="media.php?type=document" class="btn btn-sm <?php echo $filterType === 'document' ? 'btn-primary' : 'btn-secondary'; ?>">Documentos</a>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- Búsqueda -->
            <form method="GET" action="" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Buscar archivos..." value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control" style="width: 250px;">
                <button type="submit" class="btn btn-secondary btn-sm">Buscar</button>
                <?php if ($searchQuery): ?>
                    <a href="media.php" class="btn btn-secondary btn-sm">Limpiar</a>
                <?php endif; ?>
            </form>
            
            <button class="btn btn-success btn-sm" onclick="openUploadModal()">
                + Subir Archivos
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($mediaFiles)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <svg width="64" height="64" fill="#ccc" viewBox="0 0 16 16" style="margin-bottom: 20px;">
                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                </svg>
                <h3 style="color: #666; margin-bottom: 10px;">No hay archivos</h3>
                <p style="color: #999;">Sube tu primer archivo para comenzar</p>
            </div>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach ($mediaFiles as $media): ?>
                <div class="media-item" data-id="<?php echo $media['id']; ?>">
                    <div class="media-preview">
                        <?php if ($media['file_type'] === 'image'): ?>
                            <img src="<?php echo SITE_URL . '/' . htmlspecialchars($media['filepath']); ?>" alt="<?php echo htmlspecialchars($media['alt_text'] ?: $media['title']); ?>">
                        <?php elseif ($media['file_type'] === 'video'): ?>
                            <div class="media-icon">
                                <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 12V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm6.79-6.907A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                                </svg>
                            </div>
                        <?php elseif ($media['file_type'] === 'document'): ?>
                            <div class="media-icon">
                                <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="media-icon">
                                <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
                                    <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="media-info">
                        <div class="media-title" title="<?php echo htmlspecialchars($media['title']); ?>">
                            <?php echo htmlspecialchars($media['title']); ?>
                        </div>
                        <div class="media-meta">
                            <?php echo $mediaModel->formatFileSize($media['file_size']); ?>
                            <?php if ($media['width'] && $media['height']): ?>
                                • <?php echo $media['width']; ?>x<?php echo $media['height']; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="media-actions">
                        <button class="btn btn-primary btn-sm" onclick='openEditModal(<?php echo json_encode($media); ?>)'>Editar</button>
                        <button class="btn btn-secondary btn-sm" onclick='copyUrl("<?php echo SITE_URL . '/' . htmlspecialchars($media['filepath']); ?>")'>Copiar URL</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $media['id']; ?>, '<?php echo htmlspecialchars($media['title']); ?>')">Eliminar</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Subir Archivos -->
<div id="uploadModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Subir Archivos</h3>
            <button class="modal-close" onclick="closeModal('uploadModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
            <input type="hidden" name="action" value="upload">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Seleccionar Archivos</label>
                    <input type="file" name="files[]" id="fileInput" class="form-control" multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx" required>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Formatos permitidos: Imágenes, Videos, PDF, Word, Excel. Tamaño máximo: 50MB por archivo
                    </small>
                </div>
                
                <div id="filePreview" style="margin-top: 20px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('uploadModal')">Cancelar</button>
                <button type="submit" class="btn btn-success">Subir Archivos</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Medio -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Archivo</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" id="edit_media_id" name="media_id">
            <div class="modal-body">
                <div id="edit_preview" style="margin-bottom: 20px; text-align: center;"></div>
                
                <div class="form-group">
                    <label for="edit_title">Título</label>
                    <input type="text" id="edit_title" name="title" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_alt_text">Texto Alternativo (Alt)</label>
                    <input type="text" id="edit_alt_text" name="alt_text" class="form-control">
                    <small style="color: #666;">Para imágenes, describe el contenido para accesibilidad</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Descripción</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>URL del Archivo</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="edit_url" class="form-control" readonly>
                        <button type="button" class="btn btn-secondary" onclick="copyUrlFromInput('edit_url')">Copiar</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Form oculto para eliminar -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
    <input type="hidden" id="delete_media_id" name="media_id">
</form>

<style>
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.media-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
    background: white;
}

.media-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.media-preview {
    width: 100%;
    height: 180px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.media-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-icon {
    color: #999;
}

.media-info {
    padding: 12px;
}

.media-title {
    font-weight: 600;
    font-size: 14px;
    color: #2c3e5c;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-meta {
    font-size: 12px;
    color: #999;
}

.media-actions {
    padding: 12px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.media-actions .btn {
    flex: 1;
    min-width: 60px;
}

#filePreview {
    display: grid;
    gap: 15px;
}

.file-preview-item {
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    background: #f9f9f9;
}
</style>

<script>
function openUploadModal() {
    document.getElementById('uploadModal').classList.add('active');
}

function openEditModal(media) {
    document.getElementById('edit_media_id').value = media.id;
    document.getElementById('edit_title').value = media.title || '';
    document.getElementById('edit_alt_text').value = media.alt_text || '';
    document.getElementById('edit_description').value = media.description || '';
    document.getElementById('edit_url').value = '<?php echo SITE_URL; ?>/' + media.filepath;
    
    // Mostrar preview
    let preview = '';
    if (media.file_type === 'image') {
        preview = '<img src="<?php echo SITE_URL; ?>/' + media.filepath + '" style="max-width: 300px; max-height: 200px; border-radius: 5px;">';
    } else {
        preview = '<p><strong>Archivo:</strong> ' + media.original_filename + '</p>';
    }
    document.getElementById('edit_preview').innerHTML = preview;
    
    document.getElementById('editModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    if (modalId === 'uploadModal') {
        document.getElementById('uploadForm').reset();
        document.getElementById('filePreview').innerHTML = '';
    }
}

function confirmDelete(mediaId, title) {
    if (confirm('¿Estás seguro de que deseas eliminar "' + title + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete_media_id').value = mediaId;
        document.getElementById('deleteForm').submit();
    }
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('URL copiada al portapapeles');
    });
}

function copyUrlFromInput(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        alert('URL copiada al portapapeles');
    });
}

// Preview de archivos antes de subir
document.getElementById('fileInput')?.addEventListener('change', function(e) {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        item.innerHTML = `
            <strong>${file.name}</strong> (${formatFileSize(file.size)})
            <input type="hidden" name="title[${index}]" value="${file.name.replace(/\.[^/.]+$/, '')}">
            <input type="hidden" name="alt_text[${index}]" value="">
            <input type="hidden" name="description[${index}]" value="">
        `;
        preview.appendChild(item);
    });
});

function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// Cerrar modal al hacer click fuera
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
