<?php
require_once __DIR__ . '/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT id, username, email, full_name, role, status, created_at, last_login
                FROM users
                ORDER BY created_at DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al obtener usuarios: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, role, status, created_at, last_login
                FROM users
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error al obtener usuario: ' . $e->getMessage());
            return null;
        }
    }
    
    public function create($data) {
        try {
            // Validar datos
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
            }
            
            // Verificar si el usuario ya existe
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM users 
                WHERE username = :username OR email = :email
            ");
            $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email']
            ]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'El usuario o email ya existe'];
            }
            
            // Crear usuario
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password, full_name, role, status)
                VALUES (:username, :email, :password, :full_name, :role, :status)
            ");
            
            $success = $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'full_name' => $data['full_name'],
                'role' => $data['role'] ?? 'editor',
                'status' => $data['status'] ?? 'active'
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Usuario creado exitosamente', 'id' => $this->db->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Error al crear usuario'];
        } catch (PDOException $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear usuario: ' . $e->getMessage()];
        }
    }
    
    public function update($id, $data) {
        try {
            // Verificar que el usuario existe
            $user = $this->getById($id);
            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }
            
            // Construir query dinámicamente
            $fields = [];
            $params = ['id' => $id];
            
            if (!empty($data['username'])) {
                $fields[] = "username = :username";
                $params['username'] = $data['username'];
            }
            
            if (!empty($data['email'])) {
                $fields[] = "email = :email";
                $params['email'] = $data['email'];
            }
            
            if (!empty($data['full_name'])) {
                $fields[] = "full_name = :full_name";
                $params['full_name'] = $data['full_name'];
            }
            
            if (!empty($data['role'])) {
                $fields[] = "role = :role";
                $params['role'] = $data['role'];
            }
            
            if (!empty($data['status'])) {
                $fields[] = "status = :status";
                $params['status'] = $data['status'];
            }
            
            if (!empty($data['password'])) {
                $fields[] = "password = :password";
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $fields[] = "updated_at = CURRENT_TIMESTAMP";
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }
            
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);
            
            if ($success) {
                return ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar usuario'];
        } catch (PDOException $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()];
        }
    }
    
    public function delete($id) {
        try {
            // No permitir eliminar el último administrador
            $stmt = $this->db->prepare("
                SELECT role FROM users WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();
            
            if ($user && $user['role'] === 'administrador') {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'administrador'");
                $result = $stmt->fetch();
                
                if ($result['count'] <= 1) {
                    return ['success' => false, 'message' => 'No se puede eliminar el último administrador'];
                }
            }
            
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $success = $stmt->execute(['id' => $id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al eliminar usuario'];
        } catch (PDOException $e) {
            error_log('Error al eliminar usuario: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar usuario: ' . $e->getMessage()];
        }
    }
    
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN role = 'administrador' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN role = 'editor' THEN 1 ELSE 0 END) as editors,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
                FROM users
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total' => 0, 'admins' => 0, 'editors' => 0, 'active' => 0];
        }
    }
}
