# 🏢 Sitio Web Petersen

**Plataforma de soluciones inteligentes, confiables y profesionales desde 1930**

## 📋 Descripción del Proyecto

Sitio web corporativo de Petersen con sistema de gestión de contenidos (CMS) integrado. El proyecto combina un frontend público desarrollado en PHP con un backend administrativo completo para gestionar contenido, usuarios y medios.

---

## 🎯 Características Principales

### **Frontend Público**
- ✅ Sitio web corporativo responsive
- ✅ 14 páginas principales (Home, Quiénes Somos, Servicios, Sucursales, Recursos, Aliados, Contacto, Blog, 6 Divisiones)
- ✅ Blog dinámico con posts desde base de datos
- ✅ Galerías de imágenes con lightbox
- ✅ Integración con redes sociales
- ✅ Formularios de contacto
- ✅ Integración con WhatsApp y Tienda Online

### **CMS Administrativo**
- ✅ Sistema de autenticación seguro
- ✅ Gestión de usuarios con roles (Administrador/Editor)
- ✅ Gestión completa de blog posts
- ✅ Editor WYSIWYG (Quill.js) para contenido rico
- ✅ Biblioteca de medios (imágenes, videos, documentos)
- ✅ Sistema de logging de seguridad
- ✅ Protección CSRF en todos los formularios
- ✅ Rate limiting en login
- ✅ Validación de contraseñas fuertes

---

## 🛠️ Stack Tecnológico

### **Frontend**
- **HTML5** - Estructura semántica
- **CSS3** - Estilos personalizados
- **JavaScript (Vanilla)** - Interactividad
- **PHP 7.4+** - Backend del sitio público

### **Backend (CMS)**
- **PHP 7.4+** - Lógica del servidor
- **SQLite** - Base de datos
- **PDO** - Acceso a base de datos
- **Quill.js** - Editor WYSIWYG

### **Seguridad**
- **Password Hashing** - bcrypt
- **CSRF Protection** - Tokens de seguridad
- **Rate Limiting** - Protección contra fuerza bruta
- **Session Management** - Timeout y regeneración
- **Security Headers** - XSS, Clickjacking, etc.

---

## 📁 Estructura del Proyecto

```
/opt/homebrew/var/www/petersen/
│
├── assets/                      # Recursos estáticos
│   ├── css/
│   │   └── styles.css          # Estilos principales (~3000 líneas)
│   ├── js/
│   │   └── main.js             # JavaScript principal (~1000 líneas)
│   ├── images/                 # Imágenes del sitio
│   │   ├── logos/
│   │   ├── rubros/
│   │   ├── aliados/
│   │   ├── blog/
│   │   └── aplicaciones/
│   ├── media/                  # Archivos subidos por CMS
│   │   ├── images/
│   │   ├── videos/
│   │   ├── documents/
│   │   └── other/
│   └── video/
│
├── cms/                        # Sistema de gestión de contenidos
│   ├── includes/               # Archivos del núcleo
│   │   ├── config.php         # Configuración general
│   │   ├── security.php       # Clase de seguridad
│   │   ├── database.php       # Conexión y esquema BD
│   │   ├── auth.php           # Autenticación
│   │   ├── user.php           # Modelo de usuarios
│   │   ├── blog.php           # Modelo de blog
│   │   ├── media.php          # Modelo de medios
│   │   ├── upload.php         # Gestión de uploads
│   │   ├── header.php         # Header del CMS
│   │   └── footer.php         # Footer del CMS
│   │
│   ├── assets/
│   │   └── css/
│   │       └── admin.css      # Estilos del CMS
│   │
│   ├── database/
│   │   └── petersen_cms.db    # Base de datos SQLite
│   │
│   ├── index.php              # Dashboard
│   ├── login.php              # Página de login
│   ├── logout.php             # Cerrar sesión
│   ├── users.php              # Gestión de usuarios
│   ├── blog.php               # Gestión de blog
│   ├── media.php              # Gestión de medios
│   ├── upload-image.php       # Endpoint de upload
│   ├── migrate-posts.php      # Script de migración
│   ├── .htaccess              # Configuración Apache
│   └── README.md              # Documentación del CMS
│
├── includes/                   # Includes del sitio público
│   ├── header.php
│   └── footer.php
│
├── logs/                       # Logs del sistema
│   ├── security.log           # Eventos de seguridad
│   └── php_errors.log         # Errores PHP
│
├── index.php                   # Homepage
├── quienes-somos.php
├── servicios.php
├── sucursales.php
├── recursos.php
├── aliados.php
├── contacto.php
├── blog.php                    # Listado de posts
├── blog-post.php               # Post individual
├── division-bosque-y-jardin.php
├── division-metalurgica.php
├── division-industrial.php
├── division-ferreteria.php
├── division-mecanica.php
├── division-construccion.php
│
├── README.md                   # Este archivo
├── SECURITY_AUDIT_REPORT.md   # Reporte de auditoría
└── SECURITY_POLICIES.md        # Políticas de seguridad
```

---

## 🗄️ Esquema de Base de Datos

### **Tabla: users**
```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL,           -- 'administrador' o 'editor'
    status VARCHAR(20) DEFAULT 'active', -- 'active' o 'inactive'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);
```

### **Tabla: blog_posts**
```sql
CREATE TABLE blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    featured_image VARCHAR(500),
    author_id INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'draft',      -- 'draft' o 'published'
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

### **Tabla: media**
```sql
CREATE TABLE media (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,          -- 'image', 'video', 'document', etc.
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
);
```

---

## 🚀 Instalación y Configuración

### **Requisitos**
- PHP 7.4 o superior
- Apache 2.4+
- SQLite3
- Extensiones PHP: PDO, PDO_SQLite, GD, fileinfo

### **Instalación**

1. **Clonar/Copiar el proyecto**
```bash
# El proyecto ya está en:
/opt/homebrew/var/www/petersen/
```

2. **Configurar permisos**
```bash
cd /opt/homebrew/var/www/petersen

# Permisos de archivos
find . -type f -exec chmod 644 {} \;

# Permisos de directorios
find . -type d -exec chmod 755 {} \;

# Permisos especiales para uploads y logs
chmod 750 assets/media/
chmod 750 assets/images/blog/
chmod 750 logs/
chmod 750 cms/database/

# Base de datos
chmod 600 cms/database/petersen_cms.db
```

3. **Configurar Apache**

Asegúrate de que el `DocumentRoot` apunte a `/opt/homebrew/var/www/petersen`

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "/opt/homebrew/var/www/petersen"
    
    <Directory "/opt/homebrew/var/www/petersen">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

4. **Reiniciar Apache**
```bash
sudo brew services restart httpd
```

5. **Acceder al CMS**

Abre tu navegador en: `http://localhost/petersen/cms`

**Credenciales iniciales:**
- Las credenciales se generan automáticamente en el primer acceso
- Busca el archivo: `/cms/CREDENTIALS_ADMIN.txt`
- **IMPORTANTE:** Cambia la contraseña inmediatamente y elimina este archivo

---

## 🔐 Seguridad

### **Características de Seguridad Implementadas**

✅ **Autenticación y Sesiones**
- Password hashing con bcrypt
- Sesiones con HTTPOnly y SameSite
- Timeout de sesión (1 hora)
- Regeneración de ID de sesión

✅ **Protección contra Ataques**
- CSRF tokens en todos los formularios
- Rate limiting en login (5 intentos / 15 minutos)
- Prepared statements (anti SQL injection)
- Validación de tipos de archivo
- Sanitización de inputs/outputs

✅ **Headers de Seguridad**
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy
- Referrer-Policy

✅ **Logging y Auditoría**
- Registro de eventos de seguridad
- Logs de login exitosos/fallidos
- Logs de acciones críticas (CRUD)

### **Configuración para Producción**

**IMPORTANTE:** Antes de poner en producción:

1. **Cambiar entorno en `cms/includes/config.php`:**
```php
define('ENVIRONMENT', 'production'); // Cambiar de 'development'
```

2. **Habilitar HTTPS y cookie_secure:**
```php
ini_set('session.cookie_secure', 1); // Cambiar de 0 a 1
```

3. **Configurar certificado SSL**

4. **Mover base de datos fuera del webroot**

5. **Revisar el archivo:** `SECURITY_POLICIES.md`

---

## 📖 Uso del CMS

### **Acceso al CMS**
```
URL: http://localhost/petersen/cms
```

### **Roles de Usuario**

**Administrador:**
- Acceso completo al sistema
- Gestión de usuarios
- Gestión de blog
- Gestión de medios
- Ver estadísticas

**Editor:**
- Gestión de blog
- Gestión de medios
- Sin acceso a usuarios

### **Gestión de Blog**

1. **Crear Post:**
   - Click en "Blog" → "+ Nuevo Post"
   - Completar título, excerpt, contenido
   - Subir imagen destacada
   - Seleccionar estado (Borrador/Publicado)
   - Guardar

2. **Editar Post:**
   - Click en "Editar" en el post deseado
   - Modificar campos
   - Guardar cambios

3. **Eliminar Post:**
   - Click en "Eliminar"
   - Confirmar acción

### **Gestión de Medios**

1. **Subir Archivos:**
   - Click en "Medios" → "+ Subir Archivos"
   - Seleccionar uno o varios archivos
   - Subir

2. **Organizar:**
   - Filtrar por tipo (Imágenes, Videos, Documentos)
   - Buscar por nombre
   - Editar metadatos (título, alt text, descripción)

3. **Usar en Posts:**
   - Copiar URL del archivo
   - Insertar en el editor Quill

---

## 🎨 Diseño y Estilos

### **Colores Corporativos**
```css
--azul-primario: #2c3e5c
--naranja: #f26522
--verde-whatsapp: #25d366
```

### **Tipografía**
- **Familia:** Raleway (Google Fonts)
- **Pesos:** 300, 400, 500, 600, 700

### **Breakpoints Responsive**
```css
Mobile: < 768px
Tablet: 768px - 1024px
Desktop: > 1024px
```

---

## 📊 Estadísticas y Métricas

El CMS proporciona estadísticas en tiempo real:

- **Dashboard:**
  - Total de posts
  - Posts publicados
  - Borradores
  - Total de usuarios

- **Blog:**
  - Posts por estado
  - Posts por autor
  - Últimas publicaciones

- **Medios:**
  - Total de archivos
  - Archivos por tipo
  - Espacio utilizado

---

## 🔧 Mantenimiento

### **Backups**

**Base de Datos:**
```bash
# Backup manual
sqlite3 cms/database/petersen_cms.db .dump > backup_$(date +%Y%m%d).sql

# Restaurar
sqlite3 cms/database/petersen_cms.db < backup_20260107.sql
```

**Archivos:**
```bash
# Backup de medios
tar -czf media_backup_$(date +%Y%m%d).tar.gz assets/media/

# Backup completo
tar -czf petersen_backup_$(date +%Y%m%d).tar.gz \
    --exclude='logs/*' \
    --exclude='cms/database/*.db-journal' \
    .
```

### **Logs**

**Ubicación de logs:**
- Seguridad: `/logs/security.log`
- Errores PHP: `/logs/php_errors.log`

**Revisar logs:**
```bash
# Últimos eventos de seguridad
tail -f logs/security.log

# Errores PHP
tail -f logs/php_errors.log
```

### **Limpieza**

```bash
# Limpiar logs antiguos (más de 30 días)
find logs/ -name "*.log" -mtime +30 -delete

# Limpiar sesiones antiguas
find /tmp -name "sess_*" -mtime +1 -delete
```

---

## 🐛 Troubleshooting

### **Problema: No puedo acceder al CMS**
```
Solución:
1. Verificar que Apache esté corriendo
2. Verificar permisos de archivos
3. Revisar logs de Apache
```

### **Problema: Error al subir imágenes**
```
Solución:
1. Verificar permisos de assets/media/
2. Verificar límite de upload en php.ini
3. Revisar logs de seguridad
```

### **Problema: Sesión expira muy rápido**
```
Solución:
1. Ajustar timeout en cms/includes/config.php
2. Verificar configuración de sesiones en php.ini
```

### **Problema: Olvidé la contraseña de administrador**
```
Solución:
1. Acceder a la base de datos
2. Generar nuevo hash de contraseña
3. Actualizar registro del usuario
```

---

## 📝 Changelog

### **Versión 1.0.0** (Enero 2026)
- ✅ Sitio web público completo
- ✅ CMS con autenticación
- ✅ Gestión de blog
- ✅ Gestión de medios
- ✅ Sistema de seguridad completo
- ✅ Migración de posts hardcodeados
- ✅ Editor Quill.js integrado

---

## 👥 Equipo

**Desarrollo:** Boris Dedoff  
**Cliente:** Petersen S.A.  
**Año:** 2026

---

## 📄 Licencia

Proyecto propietario de Petersen S.A.  
Todos los derechos reservados © 2026

---

## 📞 Soporte

Para soporte técnico o consultas:
- **Email:** admin@petersen.com.py
- **Teléfono:** +595 21 XXX XXXX
- **Sitio Web:** https://petersen.com.py

---

## 🔗 Enlaces Útiles

- [Documentación del CMS](cms/README.md)
- [Reporte de Auditoría de Seguridad](SECURITY_AUDIT_REPORT.md)
- [Políticas de Seguridad](SECURITY_POLICIES.md)
- [Quill.js Documentation](https://quilljs.com/docs/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

---

**Última actualización:** 7 de Enero, 2026
