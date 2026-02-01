<?php
class Security {
    
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function regenerateCSRFToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    public static function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 900) {
        $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }
        
        $data = &$_SESSION['rate_limit'][$key];
        
        if (time() - $data['first_attempt'] > $timeWindow) {
            $data['attempts'] = 0;
            $data['first_attempt'] = time();
        }
        
        $data['attempts']++;
        
        if ($data['attempts'] > $maxAttempts) {
            $remainingTime = $timeWindow - (time() - $data['first_attempt']);
            return [
                'allowed' => false,
                'remaining_time' => $remainingTime
            ];
        }
        
        return ['allowed' => true];
    }
    
    public static function resetRateLimit($action) {
        $key = $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        unset($_SESSION['rate_limit'][$key]);
    }
    
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 12) {
            $errors[] = 'La contraseña debe tener al menos 12 caracteres';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Debe contener al menos una letra mayúscula';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Debe contener al menos una letra minúscula';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Debe contener al menos un número';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Debe contener al menos un carácter especial';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public static function checkSessionTimeout($timeout = 3600) {
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            
            if ($elapsed > $timeout) {
                session_unset();
                session_destroy();
                return false;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public static function regenerateSession() {
        session_regenerate_id(true);
    }
    
    public static function sanitizeFilename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filename = preg_replace('/\.+/', '.', $filename);
        return $filename;
    }
    
    public static function validateFileUpload($file, $allowedTypes, $maxSize) {
        $errors = [];
        
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = 'No se seleccionó ningún archivo';
            return ['valid' => false, 'errors' => $errors];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error al subir el archivo';
            return ['valid' => false, 'errors' => $errors];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Tipo de archivo no permitido';
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = 'El archivo excede el tamaño máximo permitido';
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'application/pdf' => ['pdf'],
            'video/mp4' => ['mp4'],
            'video/mpeg' => ['mpeg', 'mpg'],
        ];
        
        $validExtension = false;
        foreach ($allowedExtensions as $mime => $exts) {
            if ($mimeType === $mime && in_array($extension, $exts)) {
                $validExtension = true;
                break;
            }
        }
        
        if (!$validExtension) {
            $errors[] = 'La extensión del archivo no coincide con su tipo';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime_type' => $mimeType
        ];
    }
    
    public static function logSecurityEvent($event, $details = []) {
        $logFile = __DIR__ . '/../../logs/security.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0750, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user = $_SESSION['username'] ?? 'anonymous';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] EVENT: %s | USER: %s | IP: %s | DETAILS: %s | UA: %s\n",
            $timestamp,
            $event,
            $user,
            $ip,
            json_encode($details),
            $userAgent
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function setSecurityHeaders() {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' cdn.quilljs.com unpkg.com; " .
               "style-src 'self' 'unsafe-inline' cdn.quilljs.com fonts.googleapis.com unpkg.com; " .
               "font-src 'self' fonts.gstatic.com; " .
               "img-src 'self' data: https://*.tile.openstreetmap.org https://raw.githubusercontent.com https://cdnjs.cloudflare.com https://img.youtube.com; " .
               "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com; " .
               "connect-src 'self';";
        header("Content-Security-Policy: $csp");
    }
}
