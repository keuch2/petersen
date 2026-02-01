<?php
require_once __DIR__ . '/database.php';

class Media {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($fileType = null, $limit = null, $offset = 0) {
        try {
            $sql = "
                SELECT 
                    m.*,
                    u.full_name as uploader_name,
                    u.username as uploader_username
                FROM media m
                LEFT JOIN users u ON m.uploaded_by = u.id
            ";
            
            if ($fileType) {
                $sql .= " WHERE m.file_type = :file_type";
            }
            
            $sql .= " ORDER BY m.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($fileType) {
                $stmt->bindValue(':file_type', $fileType, PDO::PARAM_STR);
            }
            
            if ($limit) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al obtener medios: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.*,
                    u.full_name as uploader_name,
                    u.username as uploader_username
                FROM media m
                LEFT JOIN users u ON m.uploaded_by = u.id
                WHERE m.id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error al obtener medio: ' . $e->getMessage());
            return null;
        }
    }
    
    public function search($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.*,
                    u.full_name as uploader_name,
                    u.username as uploader_username
                FROM media m
                LEFT JOIN users u ON m.uploaded_by = u.id
                WHERE m.original_filename LIKE :query
                   OR m.title LIKE :query
                   OR m.description LIKE :query
                ORDER BY m.created_at DESC
            ");
            $stmt->execute(['query' => '%' . $query . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al buscar medios: ' . $e->getMessage());
            return [];
        }
    }
    
    public function upload($file, $userId, $metadata = []) {
        try {
            // Validar archivo
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                return ['success' => false, 'message' => 'No se seleccionó ningún archivo'];
            }
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Error al subir el archivo'];
            }
            
            // Detectar tipo de archivo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            $fileType = $this->getFileType($mimeType);
            
            // Validar tamaño (50MB máximo)
            $maxSize = 52428800; // 50MB
            if ($file['size'] > $maxSize) {
                return ['success' => false, 'message' => 'El archivo es muy grande. Tamaño máximo: 50MB'];
            }
            
            // Determinar directorio según tipo
            $uploadDir = $this->getUploadDirectory($fileType);
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid($fileType . '_') . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            $relativePath = str_replace(__DIR__ . '/../../', '', $filepath);
            
            // Mover archivo
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'message' => 'Error al guardar el archivo'];
            }
            
            // Obtener dimensiones si es imagen
            $width = null;
            $height = null;
            if ($fileType === 'image') {
                list($width, $height) = getimagesize($filepath);
            }
            
            // Guardar en base de datos
            $stmt = $this->db->prepare("
                INSERT INTO media (
                    filename, original_filename, filepath, file_type, mime_type, 
                    file_size, width, height, title, alt_text, description, uploaded_by
                )
                VALUES (
                    :filename, :original_filename, :filepath, :file_type, :mime_type,
                    :file_size, :width, :height, :title, :alt_text, :description, :uploaded_by
                )
            ");
            
            $success = $stmt->execute([
                'filename' => $filename,
                'original_filename' => $file['name'],
                'filepath' => $relativePath,
                'file_type' => $fileType,
                'mime_type' => $mimeType,
                'file_size' => $file['size'],
                'width' => $width,
                'height' => $height,
                'title' => $metadata['title'] ?? pathinfo($file['name'], PATHINFO_FILENAME),
                'alt_text' => $metadata['alt_text'] ?? '',
                'description' => $metadata['description'] ?? '',
                'uploaded_by' => $userId
            ]);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Archivo subido exitosamente',
                    'id' => $this->db->lastInsertId(),
                    'filename' => $filename,
                    'filepath' => $relativePath,
                    'url' => SITE_URL . '/' . $relativePath
                ];
            }
            
            return ['success' => false, 'message' => 'Error al registrar el archivo'];
        } catch (Exception $e) {
            error_log('Error en upload de media: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar el archivo'];
        }
    }
    
    public function update($id, $data) {
        try {
            $media = $this->getById($id);
            if (!$media) {
                return ['success' => false, 'message' => 'Archivo no encontrado'];
            }
            
            $fields = [];
            $params = ['id' => $id];
            
            if (isset($data['title'])) {
                $fields[] = "title = :title";
                $params['title'] = $data['title'];
            }
            
            if (isset($data['alt_text'])) {
                $fields[] = "alt_text = :alt_text";
                $params['alt_text'] = $data['alt_text'];
            }
            
            if (isset($data['description'])) {
                $fields[] = "description = :description";
                $params['description'] = $data['description'];
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }
            
            $sql = "UPDATE media SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);
            
            if ($success) {
                return ['success' => true, 'message' => 'Archivo actualizado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar archivo'];
        } catch (PDOException $e) {
            error_log('Error al actualizar media: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar archivo'];
        }
    }
    
    public function delete($id) {
        try {
            $media = $this->getById($id);
            if (!$media) {
                return ['success' => false, 'message' => 'Archivo no encontrado'];
            }
            
            // Eliminar archivo físico
            $fullPath = __DIR__ . '/../../' . $media['filepath'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            // Eliminar de base de datos
            $stmt = $this->db->prepare("DELETE FROM media WHERE id = :id");
            $success = $stmt->execute(['id' => $id]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Archivo eliminado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al eliminar archivo'];
        } catch (PDOException $e) {
            error_log('Error al eliminar media: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar archivo'];
        }
    }
    
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN file_type = 'image' THEN 1 ELSE 0 END) as images,
                    SUM(CASE WHEN file_type = 'video' THEN 1 ELSE 0 END) as videos,
                    SUM(CASE WHEN file_type = 'document' THEN 1 ELSE 0 END) as documents,
                    SUM(file_size) as total_size
                FROM media
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total' => 0, 'images' => 0, 'videos' => 0, 'documents' => 0, 'total_size' => 0];
        }
    }
    
    private function getFileType($mimeType) {
        if (strpos($mimeType, 'image/') === 0) {
            return 'image';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'video';
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return 'audio';
        } elseif (in_array($mimeType, ['application/pdf'])) {
            return 'document';
        } elseif (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        } elseif (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return 'document';
        } else {
            return 'other';
        }
    }
    
    private function getUploadDirectory($fileType) {
        $baseDir = __DIR__ . '/../../assets/media/';
        
        switch ($fileType) {
            case 'image':
                return $baseDir . 'images/';
            case 'video':
                return $baseDir . 'videos/';
            case 'audio':
                return $baseDir . 'audio/';
            case 'document':
                return $baseDir . 'documents/';
            default:
                return $baseDir . 'other/';
        }
    }
    
    public function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
