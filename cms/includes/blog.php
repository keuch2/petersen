<?php
require_once __DIR__ . '/database.php';

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($status = null) {
        try {
            $sql = "
                SELECT 
                    bp.*,
                    u.full_name as author_name,
                    u.username as author_username
                FROM blog_posts bp
                LEFT JOIN users u ON bp.author_id = u.id
            ";
            
            if ($status) {
                $sql .= " WHERE bp.status = :status";
            }
            
            $sql .= " ORDER BY bp.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($status) {
                $stmt->execute(['status' => $status]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al obtener posts: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    bp.*,
                    u.full_name as author_name,
                    u.username as author_username
                FROM blog_posts bp
                LEFT JOIN users u ON bp.author_id = u.id
                WHERE bp.id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error al obtener post: ' . $e->getMessage());
            return null;
        }
    }
    
    public function getBySlug($slug) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    bp.*,
                    u.full_name as author_name,
                    u.username as author_username
                FROM blog_posts bp
                LEFT JOIN users u ON bp.author_id = u.id
                WHERE bp.slug = :slug AND bp.status = 'published'
            ");
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error al obtener post por slug: ' . $e->getMessage());
            return null;
        }
    }
    
    public function create($data) {
        try {
            // Validar datos
            if (empty($data['title']) || empty($data['content'])) {
                return ['success' => false, 'message' => 'Título y contenido son obligatorios'];
            }
            
            // Generar slug único
            $slug = $this->generateSlug($data['title']);
            
            // Crear post
            $stmt = $this->db->prepare("
                INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author_id, status, published_at)
                VALUES (:title, :slug, :excerpt, :content, :featured_image, :author_id, :status, :published_at)
            ");
            
            $publishedAt = null;
            if ($data['status'] === 'published') {
                $publishedAt = date('Y-m-d H:i:s');
            }
            
            $success = $stmt->execute([
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'] ?? '',
                'content' => $data['content'],
                'featured_image' => $data['featured_image'] ?? null,
                'author_id' => $data['author_id'],
                'status' => $data['status'] ?? 'draft',
                'published_at' => $publishedAt
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Post creado exitosamente', 'id' => $this->db->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Error al crear post'];
        } catch (PDOException $e) {
            error_log('Error al crear post: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear post: ' . $e->getMessage()];
        }
    }
    
    public function update($id, $data) {
        try {
            $post = $this->getById($id);
            if (!$post) {
                return ['success' => false, 'message' => 'Post no encontrado'];
            }
            
            $fields = [];
            $params = ['id' => $id];
            
            if (isset($data['title'])) {
                $fields[] = "title = :title";
                $params['title'] = $data['title'];
                
                // Regenerar slug si cambió el título
                $newSlug = $this->generateSlug($data['title'], $id);
                $fields[] = "slug = :slug";
                $params['slug'] = $newSlug;
            }
            
            if (isset($data['excerpt'])) {
                $fields[] = "excerpt = :excerpt";
                $params['excerpt'] = $data['excerpt'];
            }
            
            if (isset($data['content'])) {
                $fields[] = "content = :content";
                $params['content'] = $data['content'];
            }
            
            if (isset($data['featured_image'])) {
                $fields[] = "featured_image = :featured_image";
                $params['featured_image'] = $data['featured_image'];
            }
            
            if (isset($data['status'])) {
                $fields[] = "status = :status";
                $params['status'] = $data['status'];
                
                // Si se publica por primera vez, establecer fecha
                if ($data['status'] === 'published' && empty($post['published_at'])) {
                    $fields[] = "published_at = :published_at";
                    $params['published_at'] = date('Y-m-d H:i:s');
                }
            }
            
            $fields[] = "updated_at = CURRENT_TIMESTAMP";
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }
            
            $sql = "UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);
            
            if ($success) {
                return ['success' => true, 'message' => 'Post actualizado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar post'];
        } catch (PDOException $e) {
            error_log('Error al actualizar post: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar post: ' . $e->getMessage()];
        }
    }
    
    public function delete($id) {
        try {
            $post = $this->getById($id);
            if (!$post) {
                return ['success' => false, 'message' => 'Post no encontrado'];
            }
            
            // Eliminar imagen destacada si existe
            if ($post['featured_image'] && file_exists(__DIR__ . '/../../' . $post['featured_image'])) {
                unlink(__DIR__ . '/../../' . $post['featured_image']);
            }
            
            $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = :id");
            $success = $stmt->execute(['id' => $id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Post eliminado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al eliminar post'];
        } catch (PDOException $e) {
            error_log('Error al eliminar post: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar post: ' . $e->getMessage()];
        }
    }
    
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as drafts
                FROM blog_posts
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total' => 0, 'published' => 0, 'drafts' => 0];
        }
    }
    
    private function generateSlug($title, $excludeId = null) {
        // Convertir a minúsculas y reemplazar espacios
        $slug = strtolower(trim($title));
        
        // Reemplazar caracteres especiales
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar si el slug ya existe
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM blog_posts WHERE slug = :slug" . ($excludeId ? " AND id != :id" : ""));
            $params = ['slug' => $slug];
            if ($excludeId) {
                $params['id'] = $excludeId;
            }
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
