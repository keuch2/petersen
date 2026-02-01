<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/user.php';

$userModel = new User();
$stats = $userModel->getStats();
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Resumen general del sistema</p>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'no_permission'): ?>
    <div class="alert alert-danger">
        No tienes permisos para acceder a esa sección.
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Usuarios</h3>
        <div class="stat-value"><?php echo $stats['total']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Administradores</h3>
        <div class="stat-value"><?php echo $stats['admins']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Editores</h3>
        <div class="stat-value"><?php echo $stats['editors']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Usuarios Activos</h3>
        <div class="stat-value"><?php echo $stats['active']; ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Información del Sistema</h2>
    </div>
    <div class="card-body">
        <p><strong>Versión:</strong> 1.0.0</p>
        <p><strong>Base de datos:</strong> SQLite</p>
        <p><strong>Tu rol:</strong> <?php echo ucfirst($currentUser['role']); ?></p>
        <p><strong>Último acceso:</strong> <?php echo $currentUser['last_login'] ? date('d/m/Y H:i', strtotime($currentUser['last_login'])) : 'Primer acceso'; ?></p>
    </div>
</div>

<?php if ($auth->isAdmin()): ?>
<div class="card">
    <div class="card-header">
        <h2>Acciones Rápidas</h2>
    </div>
    <div class="card-body">
        <a href="users.php" class="btn btn-primary">Gestionar Usuarios</a>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
