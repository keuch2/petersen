<?php
class SiteOptions {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function get($optionName, $default = null) {
        try {
            $stmt = $this->db->prepare("SELECT option_value FROM site_options WHERE option_name = :name");
            $stmt->execute(['name' => $optionName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['option_value'] : $default;
        } catch (PDOException $e) {
            error_log('Error getting option: ' . $e->getMessage());
            return $default;
        }
    }
    
    public function set($optionName, $optionValue) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO site_options (option_name, option_value, updated_at) 
                VALUES (:name, :value, CURRENT_TIMESTAMP)
                ON CONFLICT(option_name) 
                DO UPDATE SET option_value = :value, updated_at = CURRENT_TIMESTAMP
            ");
            
            return $stmt->execute([
                'name' => $optionName,
                'value' => $optionValue
            ]);
        } catch (PDOException $e) {
            error_log('Error setting option: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM site_options ORDER BY option_name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting all options: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getSMTPConfig() {
        return [
            'enabled' => (bool)$this->get('smtp_enabled', 0),
            'host' => $this->get('smtp_host', ''),
            'port' => (int)$this->get('smtp_port', 587),
            'username' => $this->get('smtp_username', ''),
            'password' => $this->get('smtp_password', ''),
            'encryption' => $this->get('smtp_encryption', 'tls'),
            'from_email' => $this->get('smtp_from_email', ''),
            'from_name' => $this->get('smtp_from_name', 'Petersen'),
            'recipient_email' => $this->get('contact_recipient_email', '')
        ];
    }
    
    public function getWhatsAppConfig() {
        return [
            'ventas' => $this->get('whatsapp_ventas', ''),
            'soporte' => $this->get('whatsapp_soporte', ''),
            'rrhh' => $this->get('whatsapp_rrhh', ''),
            'administracion' => $this->get('whatsapp_administracion', ''),
            'general' => $this->get('whatsapp_general', '')
        ];
    }
    
    public function updateWhatsAppConfig($config) {
        try {
            $this->db->beginTransaction();
            
            $this->set('whatsapp_ventas', $config['ventas'] ?? '');
            $this->set('whatsapp_soporte', $config['soporte'] ?? '');
            $this->set('whatsapp_rrhh', $config['rrhh'] ?? '');
            $this->set('whatsapp_administracion', $config['administracion'] ?? '');
            $this->set('whatsapp_general', $config['general'] ?? '');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating WhatsApp config: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getSocialMediaConfig() {
        return [
            'facebook' => $this->get('social_facebook', ''),
            'instagram' => $this->get('social_instagram', ''),
            'youtube' => $this->get('social_youtube', ''),
            'linkedin' => $this->get('social_linkedin', '')
        ];
    }
    
    public function updateSocialMediaConfig($config) {
        try {
            $this->db->beginTransaction();
            
            $this->set('social_facebook', $config['facebook'] ?? '');
            $this->set('social_instagram', $config['instagram'] ?? '');
            $this->set('social_youtube', $config['youtube'] ?? '');
            $this->set('social_linkedin', $config['linkedin'] ?? '');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating social media config: ' . $e->getMessage());
            return false;
        }
    }
    
    public function updateSMTPConfig($config) {
        try {
            $this->db->beginTransaction();
            
            $this->set('smtp_enabled', $config['enabled'] ?? 0);
            $this->set('smtp_host', $config['host'] ?? '');
            $this->set('smtp_port', $config['port'] ?? 587);
            $this->set('smtp_username', $config['username'] ?? '');
            
            if (!empty($config['password'])) {
                $this->set('smtp_password', $config['password']);
            }
            
            $this->set('smtp_encryption', $config['encryption'] ?? 'tls');
            $this->set('smtp_from_email', $config['from_email'] ?? '');
            $this->set('smtp_from_name', $config['from_name'] ?? 'Petersen');
            $this->set('contact_recipient_email', $config['recipient_email'] ?? '');
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating SMTP config: ' . $e->getMessage());
            return false;
        }
    }
}
