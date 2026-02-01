<?php
require_once __DIR__ . '/database.php';

class FormSettings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todas las configuraciones de formularios
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM form_settings ORDER BY form_name");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener configuración de un formulario específico
     */
    public function getByType($formType) {
        $stmt = $this->db->prepare("SELECT * FROM form_settings WHERE form_type = ?");
        $stmt->execute([$formType]);
        return $stmt->fetch();
    }
    
    /**
     * Actualizar configuración de un formulario
     */
    public function update($id, $emails, $whatsapp) {
        // Validar y sanitizar emails
        $emails = $this->sanitizeEmails($emails);
        
        // Validar y sanitizar WhatsApp
        $whatsapp = $this->sanitizeWhatsapp($whatsapp);
        
        $stmt = $this->db->prepare("
            UPDATE form_settings 
            SET emails = ?, whatsapp = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        
        return $stmt->execute([$emails, $whatsapp, $id]);
    }
    
    /**
     * Sanitizar emails (permite múltiples separados por coma)
     */
    private function sanitizeEmails($input) {
        if (empty($input)) {
            return '';
        }
        
        $emails = array_map('trim', explode(',', $input));
        $validEmails = array();
        
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }
        
        return implode(', ', $validEmails);
    }
    
    /**
     * Sanitizar número de WhatsApp
     */
    private function sanitizeWhatsapp($input) {
        if (empty($input)) {
            return '';
        }
        
        // Remover todo excepto números
        $number = preg_replace('/[^0-9]/', '', $input);
        
        // Validar que tenga al menos 10 dígitos
        if (strlen($number) >= 10) {
            return $number;
        }
        
        return '';
    }
    
    /**
     * Obtener emails de un formulario (para usar en el frontend)
     */
    public static function getFormEmails($formType) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT emails FROM form_settings WHERE form_type = ?");
        $stmt->execute([$formType]);
        $result = $stmt->fetch();
        
        return $result ? $result['emails'] : 'info@petersen.com.py';
    }
    
    /**
     * Obtener WhatsApp de un formulario (para usar en el frontend)
     */
    public static function getFormWhatsapp($formType) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT whatsapp FROM form_settings WHERE form_type = ?");
        $stmt->execute([$formType]);
        $result = $stmt->fetch();
        
        return $result ? $result['whatsapp'] : '';
    }
}
