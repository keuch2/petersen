<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        try {
            // Crear directorio si no existe
            $dbDir = dirname(DB_PATH);
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->db = new PDO('sqlite:' . DB_PATH);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->initDatabase();
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->db;
    }
    
    private function initDatabase() {
        // Crear tabla de usuarios
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'editor',
                status VARCHAR(20) NOT NULL DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_login DATETIME
            )
        ");
        
        // Crear índices
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_role ON users(role)");
        
        // Crear tabla de blog posts
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                excerpt TEXT,
                content TEXT NOT NULL,
                featured_image VARCHAR(255),
                author_id INTEGER NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'draft',
                published_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id)
            )
        ");
        
        // Crear índices para blog
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_blog_slug ON blog_posts(slug)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_blog_status ON blog_posts(status)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_blog_author ON blog_posts(author_id)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_blog_published ON blog_posts(published_at)");
        
        // Crear tabla de medios
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS media (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename VARCHAR(255) NOT NULL,
                original_filename VARCHAR(255) NOT NULL,
                filepath VARCHAR(500) NOT NULL,
                file_type VARCHAR(50) NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                file_size INTEGER NOT NULL,
                width INTEGER,
                height INTEGER,
                title VARCHAR(255),
                alt_text VARCHAR(255),
                description TEXT,
                uploaded_by INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (uploaded_by) REFERENCES users(id)
            )
        ");
        
        // Crear índices para medios
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_media_type ON media(file_type)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_media_mime ON media(mime_type)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_media_uploaded ON media(uploaded_by)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_media_created ON media(created_at)");
        
        // Crear tabla de configuración de formularios
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS form_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                form_type VARCHAR(50) UNIQUE NOT NULL,
                form_name VARCHAR(100) NOT NULL,
                emails TEXT NOT NULL,
                whatsapp VARCHAR(20),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Crear índice para form_settings
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_form_type ON form_settings(form_type)");
        
        // Insertar configuraciones por defecto si no existen
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM form_settings");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            $this->db->exec("
                INSERT INTO form_settings (form_type, form_name, emails, whatsapp) VALUES
                ('contacto', 'Formulario de Contacto', 'info@petersen.com.py', '595986357950'),
                ('trabajo', 'Trabaje con Nosotros', 'rrhh@petersen.com.py', ''),
                ('cotizacion', 'Solicitud de Cotización', 'ventas@petersen.com.py', '595986357950'),
                ('catalogo', 'Descarga de Catálogo', 'marketing@petersen.com.py', '')
            ");
        }
        
        // Crear usuario administrador por defecto si no existe
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'administrador'");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            // Generar contraseña aleatoria fuerte
            $randomPassword = bin2hex(random_bytes(8)) . '!A1';
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            
            $this->db->exec("
                INSERT INTO users (username, email, password, full_name, role, status)
                VALUES ('admin', 'admin@petersen.com.py', '$hashedPassword', 'Administrador', 'administrador', 'active')
            ");
            
            // Guardar contraseña en archivo temporal (eliminar después del primer login)
            $credentialsFile = __DIR__ . '/../CREDENTIALS_ADMIN.txt';
            file_put_contents($credentialsFile, "CREDENCIALES DE ADMINISTRADOR\n\nUsuario: admin\nContraseña: $randomPassword\n\n⚠️ IMPORTANTE: Cambia esta contraseña inmediatamente después del primer login.\n⚠️ Elimina este archivo después de leer las credenciales.\n");
            chmod($credentialsFile, 0600);
        }
    }
}
