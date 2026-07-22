<?php
require_once __DIR__ . '/database.php';

/**
 * La Feria Petersen: configuración de la landing y registros de recordatorio.
 *
 * La configuración vive en site_options (prefijo feria_) para reutilizar el
 * mismo almacenamiento clave/valor que SMTP, WhatsApp y redes sociales.
 */
class Feria {
    private $db;
    private $options;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/site-options.php';
        $this->options = new SiteOptions($db);
    }

    public function getConfig() {
        return [
            'activa'         => (bool)$this->options->get('feria_activa', 1),
            'titulo'         => $this->options->get('feria_titulo', 'La Feria Petersen'),
            'subtitulo'      => $this->options->get('feria_subtitulo', ''),
            'banner'         => $this->options->get('feria_banner', ''),
            'banner_alt'     => $this->options->get('feria_banner_alt', 'La Feria Petersen'),
            'fecha_inicio'   => $this->options->get('feria_fecha_inicio', ''),
            'fecha_fin'      => $this->options->get('feria_fecha_fin', ''),
            'lugar'          => $this->options->get('feria_lugar', ''),
            'texto'          => $this->options->get('feria_texto', ''),
            'form_titulo'    => $this->options->get('feria_form_titulo', 'Recibí el recordatorio'),
            'form_texto'     => $this->options->get('feria_form_texto', 'Dejanos tus datos y te avisamos por WhatsApp cuando la feria esté por comenzar.'),
            'countdown_texto'=> $this->options->get('feria_countdown_texto', 'Faltan'),
            'mensaje_final'  => $this->options->get('feria_mensaje_final', '¡La feria ya comenzó! Te esperamos.'),
        ];
    }

    public function updateConfig($config) {
        try {
            $this->db->beginTransaction();

            $this->options->set('feria_activa', !empty($config['activa']) ? 1 : 0);
            $this->options->set('feria_titulo', $config['titulo'] ?? '');
            $this->options->set('feria_subtitulo', $config['subtitulo'] ?? '');
            $this->options->set('feria_banner_alt', $config['banner_alt'] ?? '');
            $this->options->set('feria_fecha_inicio', $config['fecha_inicio'] ?? '');
            $this->options->set('feria_fecha_fin', $config['fecha_fin'] ?? '');
            $this->options->set('feria_lugar', $config['lugar'] ?? '');
            $this->options->set('feria_texto', $config['texto'] ?? '');
            $this->options->set('feria_form_titulo', $config['form_titulo'] ?? '');
            $this->options->set('feria_form_texto', $config['form_texto'] ?? '');
            $this->options->set('feria_countdown_texto', $config['countdown_texto'] ?? '');
            $this->options->set('feria_mensaje_final', $config['mensaje_final'] ?? '');

            // El banner solo se reemplaza cuando se sube uno nuevo.
            if (!empty($config['banner'])) {
                $this->options->set('feria_banner', $config['banner']);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating feria config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra una suscripción al recordatorio.
     * El teléfono normalizado actúa como clave única: reenviar el formulario
     * actualiza los datos en lugar de duplicar el registro.
     */
    public function createLead($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO feria_leads (nombre, telefono, email, ciudad, ip_address, user_agent)
                VALUES (:nombre, :telefono, :email, :ciudad, :ip_address, :user_agent)
                ON CONFLICT(telefono) DO UPDATE SET
                    nombre = excluded.nombre,
                    email = excluded.email,
                    ciudad = excluded.ciudad,
                    created_at = CURRENT_TIMESTAMP
            ");

            $result = $stmt->execute([
                'nombre'     => $data['nombre'],
                'telefono'   => $data['telefono'],
                'email'      => $data['email'] ?? '',
                'ciudad'     => $data['ciudad'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
            ]);

            return [
                'success' => $result,
                'id'      => $this->db->lastInsertId(),
                'message' => $result ? 'Registro guardado correctamente' : 'Error al guardar el registro'
            ];
        } catch (PDOException $e) {
            error_log('Error creating feria lead: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al guardar el registro'];
        }
    }

    public function getAllLeads() {
        try {
            $stmt = $this->db->query("SELECT * FROM feria_leads ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error getting feria leads: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteLead($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM feria_leads WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);

            return [
                'success' => $result,
                'message' => $result ? 'Registro eliminado correctamente' : 'Error al eliminar el registro'
            ];
        } catch (PDOException $e) {
            error_log('Error deleting feria lead: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el registro'];
        }
    }

    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    COUNT(DISTINCT ciudad) as ciudades,
                    SUM(CASE WHEN email != '' THEN 1 ELSE 0 END) as con_email,
                    MAX(created_at) as ultimo
                FROM feria_leads
            ");
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Error getting feria stats: ' . $e->getMessage());
            return ['total' => 0, 'ciudades' => 0, 'con_email' => 0, 'ultimo' => null];
        }
    }

    public function exportToCSV() {
        $leads = $this->getAllLeads();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="feria_registros_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM para que Excel reconozca UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // El $escape explícito evita la deprecación de fputcsv() en PHP 8.4
        fputcsv($output, ['ID', 'Nombre Completo', 'WhatsApp', 'Email', 'Ciudad', 'Fecha de Registro'], ',', '"', '\\');

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead['id'],
                $lead['nombre'],
                $lead['telefono'],
                $lead['email'],
                $lead['ciudad'],
                $lead['created_at']
            ], ',', '"', '\\');
        }

        fclose($output);
        return true;
    }

    /**
     * Normaliza un número paraguayo a formato internacional (595XXXXXXXXX).
     * Devuelve '' si no es un número plausible.
     */
    public static function normalizePhone($phone) {
        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return '';
        }

        // 0981234567 -> 595981234567
        if (strpos($digits, '0') === 0) {
            $digits = '595' . substr($digits, 1);
        } elseif (strpos($digits, '595') !== 0) {
            $digits = '595' . $digits;
        }

        // Un móvil paraguayo en formato internacional tiene 12 dígitos.
        if (strlen($digits) < 11 || strlen($digits) > 13) {
            return '';
        }

        return $digits;
    }
}
