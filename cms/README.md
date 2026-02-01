# CMS Petersen

Sistema de Gestión de Contenidos para el sitio web de Petersen.

## 🚀 Características

- **Base de datos**: SQLite (sin necesidad de servidor MySQL)
- **Autenticación**: Sistema de login seguro con sesiones
- **Roles de usuario**: Administrador y Editor
- **Gestión de usuarios**: CRUD completo (solo para administradores)

## 👥 Roles y Permisos

### Administrador
- Acceso completo al sistema
- Crear, editar y eliminar usuarios
- Gestionar todo el contenido

### Editor
- Acceso al dashboard
- Gestionar contenido (próximamente)
- **NO** puede gestionar usuarios

## 🔐 Credenciales por Defecto

**Usuario**: `admin`  
**Contraseña**: `admin123`

⚠️ **IMPORTANTE**: Cambia estas credenciales después del primer acceso.

## 📁 Estructura de Archivos

```
cms/
├── assets/
│   ├── css/
│   │   └── admin.css          # Estilos del CMS
│   └── js/                     # JavaScript (futuro)
├── database/
│   └── petersen_cms.db        # Base de datos SQLite
├── includes/
│   ├── config.php             # Configuración general
│   ├── database.php           # Conexión y setup de BD
│   ├── auth.php               # Sistema de autenticación
│   ├── user.php               # Modelo de usuarios
│   ├── header.php             # Header del admin
│   └── footer.php             # Footer del admin
├── index.php                  # Dashboard principal
├── login.php                  # Página de login
├── logout.php                 # Cerrar sesión
├── users.php                  # Gestión de usuarios
├── .htaccess                  # Protección de archivos
└── README.md                  # Este archivo
```

## 🗄️ Base de Datos

### Tabla: users

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | ID único (autoincremental) |
| username | VARCHAR(50) | Nombre de usuario (único) |
| email | VARCHAR(100) | Email (único) |
| password | VARCHAR(255) | Contraseña hasheada |
| full_name | VARCHAR(100) | Nombre completo |
| role | VARCHAR(20) | Rol: 'administrador' o 'editor' |
| status | VARCHAR(20) | Estado: 'active' o 'inactive' |
| created_at | DATETIME | Fecha de creación |
| updated_at | DATETIME | Última actualización |
| last_login | DATETIME | Último acceso |

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Sesiones seguras con `httponly` cookies
- Protección contra SQL injection (PDO con prepared statements)
- Validación de permisos en cada página
- Protección de archivos sensibles vía `.htaccess`

## 📝 Uso

### Acceder al CMS

1. Navega a: `http://localhost:8080/cms/`
2. Ingresa las credenciales por defecto
3. Cambia tu contraseña en el primer acceso

### Crear un Usuario

1. Ve a **Usuarios** en el menú lateral
2. Click en **+ Nuevo Usuario**
3. Completa el formulario
4. Selecciona el rol apropiado
5. Click en **Crear Usuario**

### Editar un Usuario

1. En la lista de usuarios, click en **Editar**
2. Modifica los campos necesarios
3. Deja la contraseña vacía si no deseas cambiarla
4. Click en **Guardar Cambios**

### Eliminar un Usuario

1. En la lista de usuarios, click en **Eliminar**
2. Confirma la acción
3. El usuario será eliminado permanentemente

⚠️ **Nota**: No puedes eliminar tu propio usuario ni el último administrador del sistema.

## 🔄 Próximas Funcionalidades

- [ ] Gestión de contenido de páginas
- [ ] Gestión de imágenes y galería
- [ ] Gestión de productos/servicios
- [ ] Gestión de blog/noticias
- [ ] Gestión de sucursales
- [ ] Gestión de marcas y aliados
- [ ] Sistema de respaldos
- [ ] Logs de actividad

## 🛠️ Requisitos Técnicos

- PHP 7.4 o superior
- Extensión PDO SQLite habilitada
- Apache con mod_rewrite (opcional)
- Permisos de escritura en directorio `database/`

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.
