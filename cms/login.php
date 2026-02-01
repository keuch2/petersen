<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

// Si ya está logueado, redirigir al dashboard
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::validateCSRFToken($csrfToken)) {
        $error = 'Token de seguridad inválido. Por favor, intenta nuevamente.';
        Security::logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'login']);
    } else {
        $rateLimit = Security::checkRateLimit('login', 5, 900);
        
        if (!$rateLimit['allowed']) {
            $minutes = ceil($rateLimit['remaining_time'] / 60);
            $error = "Demasiados intentos de login. Por favor, intenta en $minutes minutos.";
            Security::logSecurityEvent('RATE_LIMIT_EXCEEDED', ['action' => 'login']);
        } else {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($auth->login($username, $password)) {
                Security::resetRateLimit('login');
                Security::regenerateSession();
                Security::logSecurityEvent('LOGIN_SUCCESS', ['username' => $username]);
                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos';
                Security::logSecurityEvent('LOGIN_FAILED', ['username' => $username]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMS Petersen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo CMS_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <h1>Petersen CMS</h1>
                <p>Sistema de Gestión de Contenidos</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Usuario o Email</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
            </form>
            
            <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px; font-size: 12px;">
                <strong>Credenciales por defecto:</strong><br>
                Usuario: <code>admin</code><br>
                Contraseña: <code>admin123</code>
            </div>
        </div>
    </div>
</body>
</html>
