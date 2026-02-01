<?php
class ContactMessage {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO contact_messages (
                    nombre, email, telefono, empresa, ciudad, area, mensaje,
                    ip_address, user_agent, status, created_at
                ) VALUES (
                    :nombre, :email, :telefono, :empresa, :ciudad, :area, :mensaje,
                    :ip_address, :user_agent, 'unread', CURRENT_TIMESTAMP
                )
            ");
            
            $result = $stmt->execute([
                'nombre' => $data['nombre'] ?? '',
                'email' => $data['email'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'empresa' => $data['empresa'] ?? '',
                'ciudad' => $data['ciudad'] ?? '',
                'area' => $data['area'] ?? '',
                'mensaje' => $data['mensaje'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Mensaje guardado correctamente',
                    'id' => $this->db->lastInsertId()
                ];
            }
            
            return ['success' => false, 'message' => 'Error al guardar el mensaje'];
        } catch (PDOException $e) {
            error_log('Error creating contact message: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al guardar el mensaje'];
        }
    }
    
    public function getAll($status = null, $limit = 100, $offset = 0) {
        try {
            $sql = "SELECT * FROM contact_messages";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = :status";
                $params['status'] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting messages: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contact_messages WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting message: ' . $e->getMessage());
            return null;
        }
    }
    
    public function markAsRead($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE contact_messages 
                SET status = 'read', read_at = CURRENT_TIMESTAMP 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log('Error marking message as read: ' . $e->getMessage());
            return false;
        }
    }
    
    public function markAsReplied($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE contact_messages 
                SET status = 'replied', replied_at = CURRENT_TIMESTAMP 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log('Error marking message as replied: ' . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE id = :id");
            
            if ($stmt->execute(['id' => $id])) {
                return ['success' => true, 'message' => 'Mensaje eliminado correctamente'];
            }
            
            return ['success' => false, 'message' => 'Error al eliminar el mensaje'];
        } catch (PDOException $e) {
            error_log('Error deleting message: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el mensaje'];
        }
    }
    
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) as unread,
                    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read,
                    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied
                FROM contact_messages
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting stats: ' . $e->getMessage());
            return ['total' => 0, 'unread' => 0, 'read' => 0, 'replied' => 0];
        }
    }
    
    public function markAsSent($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE contact_messages 
                SET sent_via_email = 1 
                WHERE id = :id
            ");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log('Error marking message as sent: ' . $e->getMessage());
            return false;
        }
    }
}
