<?php
$pageTitle = 'Gestión de Blog';
require_once 'includes/header.php';
require_once 'includes/blog.php';

$blogModel = new Blog();
$posts = $blogModel->getAll();

$message = '';
$messageType = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'blog']);
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            $result = $blogModel->create([
                'title' => $_POST['title'] ?? '',
                'excerpt' => $_POST['excerpt'] ?? '',
                'content' => $_POST['content'] ?? '',
                'featured_image' => $_POST['featured_image'] ?? null,
                'author_id' => $_SESSION['user_id'],
                'status' => $_POST['status'] ?? 'draft'
            ]);
            
            if ($result['success']) {
                Security::logSecurityEvent('BLOG_POST_CREATED', ['title' => $_POST['title']]);
            }
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        
            if ($result['success']) {
                $posts = $blogModel->getAll();
            }
        }
        
        if ($action === 'update') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'excerpt' => $_POST['excerpt'] ?? '',
                'content' => $_POST['content'] ?? '',
                'status' => $_POST['status'] ?? 'draft'
            ];
            
            if (!empty($_POST['featured_image'])) {
                $data['featured_image'] = $_POST['featured_image'];
            }
            
            $result = $blogModel->update($_POST['post_id'], $data);
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                $posts = $blogModel->getAll();
            }
        }
        
        if ($action === 'delete') {
            $result = $blogModel->delete($_POST['post_id']);
            
            if ($result['success']) {
                Security::logSecurityEvent('BLOG_POST_DELETED', ['post_id' => $_POST['post_id']]);
            }
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                $posts = $blogModel->getAll();
            }
        }
    }
}

$stats = $blogModel->getStats();
?>

<div class="page-header">
    <h1>Gestión de Blog</h1>
    <p>Administra las publicaciones del blog</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 30px;">
    <div class="stat-card">
        <h3>Total Posts</h3>
        <div class="stat-value"><?php echo $stats['total']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Publicados</h3>
        <div class="stat-value"><?php echo $stats['published']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Borradores</h3>
        <div class="stat-value"><?php echo $stats['drafts']; ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Posts del Blog</h2>
        <button class="btn btn-success btn-sm" onclick="openCreateModal()">
            + Nuevo Post
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            No hay posts aún. ¡Crea el primero!
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <?php if ($post['featured_image']): ?>
                                <img src="<?php echo SITE_URL . '/' . htmlspecialchars($post['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 40px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #6c757d;">
                                    Sin imagen
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                            <br>
                            <small style="color: #6c757d;"><?php echo htmlspecialchars($post['slug']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo $post['status'] === 'published' ? 'Publicado' : 'Borrador'; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $date = $post['status'] === 'published' && $post['published_at'] 
                                ? $post['published_at'] 
                                : $post['created_at'];
                            echo date('d/m/Y', strtotime($date)); 
                            ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary btn-sm" onclick='openEditModal(<?php echo json_encode($post); ?>)'>
                                    Editar
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title']); ?>')">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear Post -->
<div id="createModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3>Crear Nuevo Post</h3>
            <button class="modal-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form method="POST" action="" id="createPostForm">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" id="create_featured_image" name="featured_image">
            <div class="modal-body">
                <div class="form-group">
                    <label for="create_title">Título *</label>
                    <input type="text" id="create_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_featured_image_upload">Imagen Destacada</label>
                    <input type="file" id="create_featured_image_upload" class="form-control" accept="image/*" onchange="uploadFeaturedImage(this, 'create')">
                    <div id="create_image_preview" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label for="create_excerpt">Introducción Corta</label>
                    <textarea id="create_excerpt" name="excerpt" class="form-control" rows="3" placeholder="Resumen breve del post..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="create_content">Contenido *</label>
                    <div class="editor-wrapper">
                        <div id="create_content_editor"></div>
                    </div>
                    <textarea id="create_content" name="content" style="display: none;"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="create_status">Estado *</label>
                    <select id="create_status" name="status" class="form-control" required>
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
                <button type="submit" class="btn btn-success">Crear Post</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Post -->
<div id="editModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3>Editar Post</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_post_id" name="post_id">
            <input type="hidden" id="edit_featured_image" name="featured_image">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_title">Título *</label>
                    <input type="text" id="edit_title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_featured_image_upload">Imagen Destacada</label>
                    <input type="file" id="edit_featured_image_upload" class="form-control" accept="image/*" onchange="uploadFeaturedImage(this, 'edit')">
                    <div id="edit_image_preview" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label for="edit_excerpt">Introducción Corta</label>
                    <textarea id="edit_excerpt" name="excerpt" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_content">Contenido *</label>
                    <div class="editor-wrapper">
                        <div id="edit_content_editor"></div>
                    </div>
                    <textarea id="edit_content" name="content" style="display: none;"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_status">Estado *</label>
                    <select id="edit_status" name="status" class="form-control" required>
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                    </select>
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
    <input type="hidden" id="delete_post_id" name="post_id">
</form>

<!-- Quill Editor (Self-hosted) -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
.ql-editor {
    min-height: 300px;
    font-family: Raleway, Arial, sans-serif;
    font-size: 16px;
}
.ql-container {
    font-family: Raleway, Arial, sans-serif;
}
.editor-wrapper {
    background: white;
    border-radius: 5px;
}
</style>

<script>
let createQuill = null;
let editQuill = null;

// Configuración de Quill
const quillConfig = {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
        ]
    }
};

function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
    
    // Inicializar Quill para crear
    setTimeout(() => {
        if (!createQuill) {
            createQuill = new Quill('#create_content_editor', quillConfig);
            
            // Handler personalizado para imágenes
            createQuill.getModule('toolbar').addHandler('image', function() {
                selectLocalImage(createQuill);
            });
        }
        createQuill.setContents([]);
    }, 100);
}

function openEditModal(post) {
    document.getElementById('edit_post_id').value = post.id;
    document.getElementById('edit_title').value = post.title;
    document.getElementById('edit_excerpt').value = post.excerpt || '';
    document.getElementById('edit_status').value = post.status;
    document.getElementById('edit_featured_image').value = post.featured_image || '';
    
    // Mostrar imagen actual
    if (post.featured_image) {
        document.getElementById('edit_image_preview').innerHTML = 
            '<img src="<?php echo SITE_URL; ?>/' + post.featured_image + '" style="max-width: 200px; border-radius: 5px;">';
    } else {
        document.getElementById('edit_image_preview').innerHTML = '';
    }
    
    document.getElementById('editModal').classList.add('active');
    
    // Inicializar Quill para editar
    setTimeout(() => {
        if (!editQuill) {
            editQuill = new Quill('#edit_content_editor', quillConfig);
            
            // Handler personalizado para imágenes
            editQuill.getModule('toolbar').addHandler('image', function() {
                selectLocalImage(editQuill);
            });
        }
        
        // Cargar contenido existente
        const delta = editQuill.clipboard.convert(post.content || '');
        editQuill.setContents(delta);
    }, 100);
}

function closeModal(modalId) {
    // Guardar contenido antes de cerrar
    if (modalId === 'createModal' && createQuill) {
        document.getElementById('create_content').value = createQuill.root.innerHTML;
    } else if (modalId === 'editModal' && editQuill) {
        document.getElementById('edit_content').value = editQuill.root.innerHTML;
    }
    
    document.getElementById(modalId).classList.remove('active');
}

// Función para subir imágenes al editor
function selectLocalImage(quill) {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = () => {
        const file = input.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);

            fetch('<?php echo CMS_URL; ?>/upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const range = quill.getSelection();
                    quill.insertEmbed(range.index, 'image', result.url);
                } else {
                    alert('Error al subir imagen: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error al subir imagen');
            });
        }
    };
}

// Guardar contenido en campos ocultos antes de enviar formularios
document.addEventListener('DOMContentLoaded', function() {
    const createForm = document.querySelector('#createModal form');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            if (createQuill) {
                document.getElementById('create_content').value = createQuill.root.innerHTML;
            }
        });
    }
    
    const editForm = document.querySelector('#editModal form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (editQuill) {
                document.getElementById('edit_content').value = editQuill.root.innerHTML;
            }
        });
    }
});

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function confirmDelete(postId, title) {
    if (confirm('¿Estás seguro de que deseas eliminar el post "' + title + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete_post_id').value = postId;
        document.getElementById('deleteForm').submit();
    }
}

function uploadFeaturedImage(input, mode) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('file', input.files[0]);
        
        const previewDiv = document.getElementById(mode + '_image_preview');
        previewDiv.innerHTML = '<p>Subiendo imagen...</p>';
        
        fetch('<?php echo CMS_URL; ?>/upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById(mode + '_featured_image').value = result.path;
                previewDiv.innerHTML = '<img src="' + result.url + '" style="max-width: 200px; border-radius: 5px;"><br><small style="color: green;">✓ Imagen subida exitosamente</small>';
            } else {
                previewDiv.innerHTML = '<p style="color: red;">Error: ' + result.message + '</p>';
            }
        })
        .catch(error => {
            previewDiv.innerHTML = '<p style="color: red;">Error al subir imagen</p>';
        });
    }
}

// Cerrar modal al hacer click fuera
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            const modalId = this.id;
            closeModal(modalId);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
