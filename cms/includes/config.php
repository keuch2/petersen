<?php
// Configuración del CMS
define('DB_PATH', __DIR__ . '/../database/petersen_cms.db');

$_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $_scheme . '://' . $_host);
define('CMS_URL', SITE_URL . '/cms');
unset($_scheme, $_host);

// Configuración de seguridad
$_is_production = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost');
define('ENVIRONMENT', $_is_production ? 'production' : 'development');
unset($_is_production);

// Configuración de errores según entorno
if (ENVIRONMENT === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
}

// Configuración de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
ini_set('session.gc_maxlifetime', 3600); // 1 hora

session_start();

// Zona horaria
date_default_timezone_set('America/Asuncion');

// Cargar clase de seguridad
require_once __DIR__ . '/security.php';

// Establecer headers de seguridad
Security::setSecurityHeaders();

// Verificar timeout de sesión
if (isset($_SESSION['user_id'])) {
    if (!Security::checkSessionTimeout(3600)) {
        header('Location: ' . CMS_URL . '/login.php?timeout=1');
        exit;
    }
}
