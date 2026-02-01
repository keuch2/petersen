<?php
require_once __DIR__ . '/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE (username = :username OR email = :username) 
                AND status = 'active'
            ");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Actualizar último login
                $updateStmt = $this->db->prepare("
                    UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id
                ");
                $updateStmt->execute(['id' => $user['id']]);
                
                // Guardar en sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Error en login: ' . $e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'administrador';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . CMS_URL . '/login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: ' . CMS_URL . '/index.php?error=no_permission');
            exit;
        }
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
}
