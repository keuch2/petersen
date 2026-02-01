# Sistema de Gestión de Formularios - Petersen CMS

## Descripción

Sistema completo para gestionar los destinatarios de emails y números de WhatsApp de todos los formularios del sitio desde el panel de administración del CMS.

## Acceso al Panel de Administración

1. Ingresar al CMS: `http://localhost/petersen/cms/`
2. En el menú lateral, buscar **"Formularios"** (icono de email)
3. Click para acceder a la configuración

## Formularios Disponibles

### 1. Formulario de Contacto
- **Ubicación**: Página de Contacto (`contacto.php`)
- **Tipo**: `contacto`
- **Configuración**:
  - Emails destinatarios (uno o varios)
  - Número de WhatsApp (opcional)
- **Email por defecto**: `info@petersen.com.py`
- **WhatsApp por defecto**: `595986357950`

### 2. Trabaje con Nosotros
- **Ubicación**: Modal en página de contacto
- **Tipo**: `trabajo`
- **Configuración**:
  - Emails destinatarios
  - Número de WhatsApp (opcional)
- **Email por defecto**: `rrhh@petersen.com.py`

### 3. Solicitud de Cotización
- **Ubicación**: Formularios de cotización de productos
- **Tipo**: `cotizacion`
- **Configuración**:
  - Emails destinatarios
  - Número de WhatsApp (opcional)
- **Email por defecto**: `ventas@petersen.com.py`
- **WhatsApp por defecto**: `595986357950`

### 4. Descarga de Catálogo
- **Ubicación**: Formulario de descarga de catálogos
- **Tipo**: `catalogo`
- **Configuración**:
  - Emails destinatarios
- **Email por defecto**: `marketing@petersen.com.py`

## Cómo Configurar

### Emails

**Formato**: `email@ejemplo.com` o `email1@ejemplo.com, email2@ejemplo.com`

- Puede ingresar **uno o varios emails** separados por coma
- Todos los emails ingresados recibirán las notificaciones
- Los emails se validan automáticamente al guardar
- Emails inválidos serán ignorados

**Ejemplos**:
```
info@petersen.com.py
ventas@petersen.com.py, info@petersen.com.py
```

### WhatsApp

**Formato**: `595986357950` (código de país + número, sin el símbolo +)

- Ingresar el número completo con código de país
- **Sin espacios, guiones ni el símbolo +**
- Mínimo 10 dígitos
- Se valida automáticamente al guardar
- Use el botón "Probar WhatsApp" para verificar

**Ejemplos válidos**:
```
595986357950
595971234567
```

**Ejemplos inválidos**:
```
+595986357950  ❌ (tiene el símbolo +)
0986-357-950   ❌ (tiene guiones y falta código de país)
986357950      ❌ (falta código de país)
```

## Uso en el Código

### Obtener Emails de un Formulario

```php
// Incluir la clase
require_once 'cms/includes/form-settings.php';

// Obtener emails del formulario de contacto
$emails = FormSettings::getFormEmails('contacto');

// Obtener emails del formulario de trabajo
$emails = FormSettings::getFormEmails('trabajo');

// Obtener emails del formulario de cotización
$emails = FormSettings::getFormEmails('cotizacion');

// Obtener emails de descarga de catálogo
$emails = FormSettings::getFormEmails('catalogo');
```

### Obtener WhatsApp de un Formulario

```php
// Obtener WhatsApp del formulario de contacto
$whatsapp = FormSettings::getFormWhatsapp('contacto');

// Verificar si hay WhatsApp configurado
if (!empty($whatsapp)) {
    echo '<a href="https://wa.me/' . $whatsapp . '">Contactar por WhatsApp</a>';
}
```

### Ejemplo de Implementación Completa

```php
<?php
require_once 'cms/includes/form-settings.php';

// Obtener configuración
$contactEmails = FormSettings::getFormEmails('contacto');
$contactWhatsapp = FormSettings::getFormWhatsapp('contacto');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
    
    // Enviar email a múltiples destinatarios
    $to = $contactEmails;
    $subject = 'Nuevo contacto desde el sitio web';
    $message = "Nombre: {$nombre}\nEmail: {$email}\n\nMensaje:\n{$mensaje}";
    
    mail($to, $subject, $message);
}
?>

<!-- Botón de WhatsApp si está configurado -->
<?php if (!empty($contactWhatsapp)): ?>
    <a href="https://wa.me/<?php echo $contactWhatsapp; ?>" 
       class="btn-whatsapp" 
       target="_blank">
        <i class="fab fa-whatsapp"></i>
        Contactar por WhatsApp
    </a>
<?php endif; ?>
```

## Base de Datos

### Tabla: `form_settings`

```sql
CREATE TABLE form_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    form_type VARCHAR(50) UNIQUE NOT NULL,
    form_name VARCHAR(100) NOT NULL,
    emails TEXT NOT NULL,
    whatsapp VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Campos:
- `form_type`: Identificador único del formulario (contacto, trabajo, cotizacion, catalogo)
- `form_name`: Nombre descriptivo del formulario
- `emails`: Emails destinatarios separados por coma
- `whatsapp`: Número de WhatsApp (solo números, con código de país)

## Archivos del Sistema

```
petersen/
├── cms/
│   ├── form-settings.php              # Página de administración
│   ├── includes/
│   │   ├── form-settings.php          # Clase de gestión
│   │   ├── database.php               # Tabla form_settings
│   │   └── header.php                 # Menú con enlace a Formularios
│   └── FORMULARIOS-README.md          # Esta documentación
└── includes/
    └── contact-handler.php            # Usa FormSettings::getFormEmails()
```

## Seguridad

- ✅ Validación automática de emails
- ✅ Sanitización de números de WhatsApp
- ✅ Protección CSRF con tokens
- ✅ Verificación de autenticación
- ✅ Escape de outputs

## Actualización de Configuración

Para cambiar los destinatarios:

1. Ir a **CMS → Formularios**
2. Modificar los campos deseados en cada tarjeta
3. Click en **"Guardar Cambios"** en cada formulario
4. ✅ Los cambios se aplican inmediatamente

**No es necesario editar código** - Todo se gestiona desde el panel de administración.

## Valores por Defecto

Si no se configura ningún email, el sistema usará automáticamente `info@petersen.com.py`.

**Emails por defecto**:
- Contacto: `info@petersen.com.py`
- Trabajo: `rrhh@petersen.com.py`
- Cotización: `ventas@petersen.com.py`
- Catálogo: `marketing@petersen.com.py`

## Solución de Problemas

### No recibo los emails
1. Verificar que los emails estén correctamente escritos en el CMS
2. Verificar la configuración SMTP en "Opciones del Sitio"
3. Revisar la carpeta de spam
4. Verificar los logs del servidor

### El botón de WhatsApp no aparece
1. Verificar que el número esté configurado en el CMS
2. Verificar que el formato sea correcto (sin + ni espacios)
3. Guardar los cambios y refrescar la página
4. Verificar que el código esté usando `FormSettings::getFormWhatsapp()`

### Los cambios no se aplican
1. Asegurarse de hacer click en "Guardar Cambios"
2. Limpiar caché del navegador
3. Verificar que la base de datos se actualizó correctamente

## Agregar un Nuevo Formulario

Para agregar un nuevo tipo de formulario:

1. Agregar un INSERT en `cms/includes/database.php` en la sección de valores por defecto:
```php
INSERT INTO form_settings (form_type, form_name, emails, whatsapp) VALUES
('nuevo_tipo', 'Nombre del Formulario', 'email@ejemplo.com', '');
```

2. Usar en el código:
```php
$emails = FormSettings::getFormEmails('nuevo_tipo');
$whatsapp = FormSettings::getFormWhatsapp('nuevo_tipo');
```

## Soporte Técnico

Para soporte técnico con el sistema de formularios, contactar al equipo de desarrollo.
