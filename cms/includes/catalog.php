<?php
require_once __DIR__ . '/database.php';

class Catalog {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO catalogs (title, description, pdf_filename, pdf_path, cover_image, category, status)
                VALUES (:title, :description, :pdf_filename, :pdf_path, :cover_image, :category, :status)
            ");
            
            $result = $stmt->execute([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'pdf_filename' => $data['pdf_filename'] ?? '',
                'pdf_path' => $data['pdf_path'] ?? '',
                'cover_image' => $data['cover_image'] ?? '',
                'category' => $data['category'] ?? '',
                'status' => $data['status'] ?? 'active'
            ]);
            
            return [
                'success' => $result,
                'id' => $this->db->lastInsertId(),
                'message' => $result ? 'Catálogo creado correctamente' : 'Error al crear el catálogo'
            ];
        } catch (PDOException $e) {
            error_log('Error creating catalog: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear el catálogo'];
        }
    }
    
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE catalogs 
                SET title = :title,
                    description = :description,
                    pdf_filename = :pdf_filename,
                    pdf_path = :pdf_path,
                    cover_image = :cover_image,
                    category = :category,
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            $result = $stmt->execute([
                'id' => $id,
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'pdf_filename' => $data['pdf_filename'] ?? '',
                'pdf_path' => $data['pdf_path'] ?? '',
                'cover_image' => $data['cover_image'] ?? '',
                'category' => $data['category'] ?? '',
                'status' => $data['status'] ?? 'active'
            ]);
            
            return [
                'success' => $result,
                'message' => $result ? 'Catálogo actualizado correctamente' : 'Error al actualizar el catálogo'
            ];
        } catch (PDOException $e) {
            error_log('Error updating catalog: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar el catálogo'];
        }
    }
    
    public function delete($id) {
        try {
            // Obtener información del catálogo para eliminar archivos
            $catalog = $this->getById($id);
            
            if ($catalog) {
                // Eliminar archivo PDF si existe
                if (!empty($catalog['pdf_path']) && file_exists($catalog['pdf_path'])) {
                    unlink($catalog['pdf_path']);
                }
                
                // Eliminar imagen de portada si existe
                if (!empty($catalog['cover_image']) && file_exists($catalog['cover_image'])) {
                    unlink($catalog['cover_image']);
                }
            }
            
            $stmt = $this->db->prepare("DELETE FROM catalogs WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);
            
            return [
                'success' => $result,
                'message' => $result ? 'Catálogo eliminado correctamente' : 'Error al eliminar el catálogo'
            ];
        } catch (PDOException $e) {
            error_log('Error deleting catalog: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el catálogo'];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM catalogs WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error getting catalog: ' . $e->getMessage());
            return null;
        }
    }
    
    public function getAll($status = null) {
        try {
            if ($status) {
                $stmt = $this->db->prepare("
                    SELECT c.*, 
                           (SELECT COUNT(*) FROM catalog_leads WHERE catalog_id = c.id) as lead_count
                    FROM catalogs c
                    WHERE c.status = :status
                    ORDER BY c.created_at DESC
                ");
                $stmt->execute(['status' => $status]);
            } else {
                $stmt = $this->db->query("
                    SELECT c.*, 
                           (SELECT COUNT(*) FROM catalog_leads WHERE catalog_id = c.id) as lead_count
                    FROM catalogs c
                    ORDER BY c.created_at DESC
                ");
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error getting catalogs: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getActive() {
        return $this->getAll('active');
    }
    
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                    (SELECT COUNT(*) FROM catalog_leads) as total_leads
                FROM catalogs
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error getting catalog stats: ' . $e->getMessage());
            return ['total' => 0, 'active' => 0, 'inactive' => 0, 'total_leads' => 0];
        }
    }
}
