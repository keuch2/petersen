<?php
require_once __DIR__ . '/config.php';

class Upload {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 5242880; // 5MB
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../../assets/images/blog/';
        
        // Crear directorio si no existe
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function uploadImage($file) {
        try {
            // Validar que se subió un archivo
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                return ['success' => false, 'message' => 'No se seleccionó ningún archivo'];
            }
            
            // Validar errores de subida
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Error al subir el archivo'];
            }
            
            // Validar tipo de archivo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $this->allowedTypes)) {
                return ['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG, GIF y WEBP'];
            }
            
            // Validar tamaño
            if ($file['size'] > $this->maxSize) {
                return ['success' => false, 'message' => 'El archivo es muy grande. Tamaño máximo: 5MB'];
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('blog_') . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Optimizar imagen
                $this->optimizeImage($filepath, $mimeType);
                
                return [
                    'success' => true,
                    'message' => 'Imagen subida exitosamente',
                    'filename' => $filename,
                    'path' => 'assets/images/blog/' . $filename,
                    'url' => SITE_URL . '/assets/images/blog/' . $filename
                ];
            }
            
            return ['success' => false, 'message' => 'Error al guardar el archivo'];
        } catch (Exception $e) {
            error_log('Error en upload: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la imagen'];
        }
    }
    
    private function optimizeImage($filepath, $mimeType) {
        try {
            $maxWidth = 1200;
            $maxHeight = 800;
            $quality = 85;
            
            // Obtener dimensiones originales
            list($width, $height) = getimagesize($filepath);
            
            // Calcular nuevas dimensiones manteniendo proporción
            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = round($width * $ratio);
                $newHeight = round($height * $ratio);
            } else {
                return; // No necesita optimización
            }
            
            // Crear imagen según tipo
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $source = imagecreatefromjpeg($filepath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($filepath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($filepath);
                    break;
                case 'image/webp':
                    $source = imagecreatefromwebp($filepath);
                    break;
                default:
                    return;
            }
            
            // Crear nueva imagen redimensionada
            $destination = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preservar transparencia para PNG y GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
            }
            
            imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Guardar imagen optimizada
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($destination, $filepath, $quality);
                    break;
                case 'image/png':
                    imagepng($destination, $filepath, 9);
                    break;
                case 'image/gif':
                    imagegif($destination, $filepath);
                    break;
                case 'image/webp':
                    imagewebp($destination, $filepath, $quality);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($destination);
        } catch (Exception $e) {
            error_log('Error al optimizar imagen: ' . $e->getMessage());
        }
    }
    
    public function deleteImage($filename) {
        $filepath = $this->uploadDir . basename($filename);
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
}
