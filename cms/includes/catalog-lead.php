<?php
require_once __DIR__ . '/database.php';

class CatalogLead {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO catalog_leads (catalog_id, name, email, phone, company, city, ip_address, user_agent)
                VALUES (:catalog_id, :name, :email, :phone, :company, :city, :ip_address, :user_agent)
            ");
            
            $result = $stmt->execute([
                'catalog_id' => $data['catalog_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'company' => $data['company'] ?? '',
                'city' => $data['city'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            return [
                'success' => $result,
                'id' => $this->db->lastInsertId(),
                'message' => $result ? 'Lead registrado correctamente' : 'Error al registrar el lead'
            ];
        } catch (PDOException $e) {
            error_log('Error creating catalog lead: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar el lead'];
        }
    }
    
    public function getByCatalog($catalogId) {
        try {
            $stmt = $this->db->prepare("
                SELECT cl.*, c.title as catalog_title
                FROM catalog_leads cl
                LEFT JOIN catalogs c ON cl.catalog_id = c.id
                WHERE cl.catalog_id = :catalog_id
                ORDER BY cl.downloaded_at DESC
            ");
            $stmt->execute(['catalog_id' => $catalogId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error getting catalog leads: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT cl.*, c.title as catalog_title
                FROM catalog_leads cl
                LEFT JOIN catalogs c ON cl.catalog_id = c.id
                ORDER BY cl.downloaded_at DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error getting all catalog leads: ' . $e->getMessage());
            return [];
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM catalog_leads WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);
            
            return [
                'success' => $result,
                'message' => $result ? 'Lead eliminado correctamente' : 'Error al eliminar el lead'
            ];
        } catch (PDOException $e) {
            error_log('Error deleting catalog lead: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el lead'];
        }
    }
    
    public function exportToCSV($catalogId) {
        $leads = $this->getByCatalog($catalogId);
        
        if (empty($leads)) {
            return false;
        }
        
        // Crear nombre de archivo
        $catalogTitle = $leads[0]['catalog_title'] ?? 'catalogo';
        $filename = 'leads_' . sanitizeFilename($catalogTitle) . '_' . date('Y-m-d') . '.csv';
        
        // Headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Abrir output stream
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers del CSV
        fputcsv($output, [
            'ID',
            'Nombre',
            'Email',
            'Teléfono',
            'Empresa',
            'Ciudad',
            'Fecha de Descarga',
            'IP',
            'User Agent'
        ]);
        
        // Datos
        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead['id'],
                $lead['name'],
                $lead['email'],
                $lead['phone'],
                $lead['company'],
                $lead['city'],
                $lead['downloaded_at'],
                $lead['ip_address'],
                $lead['user_agent']
            ]);
        }
        
        fclose($output);
        return true;
    }
    
    public function getStatsByCatalog($catalogId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_downloads,
                    COUNT(DISTINCT email) as unique_emails,
                    MIN(downloaded_at) as first_download,
                    MAX(downloaded_at) as last_download
                FROM catalog_leads
                WHERE catalog_id = :catalog_id
            ");
            $stmt->execute(['catalog_id' => $catalogId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error getting catalog lead stats: ' . $e->getMessage());
            return [
                'total_downloads' => 0,
                'unique_emails' => 0,
                'first_download' => null,
                'last_download' => null
            ];
        }
    }
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    $filename = preg_replace('/_+/', '_', $filename);
    return strtolower($filename);
}
