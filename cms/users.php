<?php
$pageTitle = 'Gestión de Usuarios';
require_once 'includes/header.php';
require_once 'includes/user.php';

// Solo administradores pueden acceder
$auth->requireAdmin();

$userModel = new User();
$users = $userModel->getAll();

$message = '';
$messageType = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $message = 'Token de seguridad inválido';
        $messageType = 'danger';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'users']);
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            $passwordValidation = Security::validatePassword($_POST['password'] ?? '');
            if (!$passwordValidation['valid']) {
                $message = 'Contraseña débil: ' . implode(', ', $passwordValidation['errors']);
                $messageType = 'danger';
            } else {
                $result = $userModel->create([
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
                    'role' => $_POST['role'] ?? 'editor',
                    'status' => $_POST['status'] ?? 'active'
                ]);
                
                if ($result['success']) {
                    Security::logSecurityEvent('USER_CREATED', ['username' => $_POST['username']]);
                }
                
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                
                if ($result['success']) {
                    $users = $userModel->getAll();
                }
            }
        }
        
        if ($action === 'update') {
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'role' => $_POST['role'] ?? '',
            'status' => $_POST['status'] ?? ''
        ];
        
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
            $result = $userModel->update($_POST['user_id'], $data);
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                $users = $userModel->getAll();
            }
        }
        
        if ($action === 'delete') {
            $result = $userModel->delete($_POST['user_id']);
            
            if ($result['success']) {
                Security::logSecurityEvent('USER_DELETED', ['user_id' => $_POST['user_id']]);
            }
            
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
            
            if ($result['success']) {
                $users = $userModel->getAll();
            }
        }
    }
}
?>

<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <p>Administra los usuarios del sistema</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Usuarios del Sistema</h2>
        <button class="btn btn-success btn-sm" onclick="openCreateModal()">
            + Nuevo Usuario
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $user['role'] === 'administrador' ? 'primary' : 'warning'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                <?php echo $user['status'] === 'active' ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary btn-sm" onclick='openEditModal(<?php echo json_encode($user); ?>)'>
                                    Editar
                                </button>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                    Eliminar
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Crear Nuevo Usuario</h3>
            <button class="modal-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="create_username">Usuario *</label>
                    <input type="text" id="create_username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_email">Email *</label>
                    <input type="email" id="create_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_full_name">Nombre Completo *</label>
                    <input type="text" id="create_full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="create_password">Contraseña *</label>
                    <input type="password" id="create_password" name="password" class="form-control" required minlength="6">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="create_role">Rol *</label>
                        <select id="create_role" name="role" class="form-control" required>
                            <option value="editor">Editor</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="create_status">Estado *</label>
                        <select id="create_status" name="status" class="form-control" required>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
                <button type="submit" class="btn btn-success">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Usuario</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_username">Usuario *</label>
                    <input type="text" id="edit_username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email *</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_full_name">Nombre Completo *</label>
                    <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" id="edit_password" name="password" class="form-control" minlength="6">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_role">Rol *</label>
                        <select id="edit_role" name="role" class="form-control" required>
                            <option value="editor">Editor</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Estado *</label>
                        <select id="edit_status" name="status" class="form-control" required>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
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
    <input type="hidden" id="delete_user_id" name="user_id">
</form>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
}

function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
    document.getElementById('edit_password').value = '';
    
    document.getElementById('editModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function confirmDelete(userId, username) {
    if (confirm('¿Estás seguro de que deseas eliminar al usuario "' + username + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete_user_id').value = userId;
        document.getElementById('deleteForm').submit();
    }
}

// Cerrar modal al hacer click fuera
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
