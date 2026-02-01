# Petersen.com.py - Documentación para Agentes IA

## Descripción del Proyecto

**Petersen** es un sitio web corporativo para una empresa distribuidora de productos industriales, ferretería, construcción y maquinaria en Paraguay. El sitio está desarrollado en PHP estático con un CMS personalizado.

### Información General
- **Dominio**: https://petersen.com.py
- **Servidor**: 181.40.91.194:2250 (SSH)
- **Ubicación**: /var/www/petersen/public
- **Servidor Web**: Apache 2.4.58 (Ubuntu)
- **SSL**: Certificado comercial SSL2BUY válido hasta julio 2026
- **Repositorio**: https://github.com/keuch2/petersen

### ⚠️ Arquitectura de Red Privada (VPN)

**IMPORTANTE**: El servidor está dentro de una **red privada VPN** con restricciones específicas de conectividad.

#### Características de la Red

**Conexiones que FUNCIONAN:**
- ✅ **SSH desde máquinas en la VPN** → Servidor (puerto 2250)
- ✅ **Acceso web público** → https://petersen.com.py (usuarios finales)
- ✅ **Máquinas en VPN** → GitHub (para push/pull)

**Conexiones que NO FUNCIONAN:**
- ❌ **Servidor** → GitHub (timeout SSL en puerto 443)
- ❌ **Servidor** → Internet HTTPS saliente (bloqueado por firewall)
- ❌ **Acceso web desde fuera de la VPN** → Servidor (solo para desarrollo)

#### Implicaciones Técnicas

1. **Git en el Servidor**:
   - El servidor **NO puede** hacer `git clone` o `git pull` desde GitHub directamente
   - Todos los timeouts SSL al intentar conectar a GitHub son **normales y esperados**
   - Por eso usamos el sistema de "puente" con rsync

2. **Certificados SSL**:
   - **No se puede usar Let's Encrypt** con validación HTTP (el servidor no es accesible desde internet para validación)
   - Por eso usamos un **certificado comercial SSL2BUY** que se renueva manualmente
   - La validación de Let's Encrypt fallaría con "Connection timeout" desde sus servidores

3. **Despliegue**:
   - El sistema de despliegue usa **SSH + rsync** en lugar de git pull
   - La máquina local actúa como **puente** entre GitHub y el servidor
   - Este es el **único método viable** dada la arquitectura de red

4. **Acceso al Sitio**:
   - Los **usuarios finales** acceden normalmente a https://petersen.com.py
   - Las **máquinas de desarrollo** dentro de la VPN acceden vía SSH
   - Intentar `curl https://petersen.com.py` desde fuera de la VPN puede dar timeout (esto es normal)

#### Diagrama de Conectividad

```
Internet (Usuarios Finales)
    ↓ HTTPS ✅
petersen.com.py (Accesible públicamente)
    ↓
[Firewall/VPN]
    ↓
Servidor (181.40.91.194)
    ↑ SSH ✅ (puerto 2250)
    ↓ HTTPS ❌ (bloqueado a GitHub)
    |
Máquina Local (Desarrollo, dentro VPN)
    ↑↓ HTTPS ✅
GitHub (keuch2/petersen)
```

#### Por Qué el Sistema "Puente" es Necesario

Dado que el servidor no puede conectarse a GitHub directamente:
- **No podemos** usar `git pull` en el servidor
- **No podemos** usar webhooks de GitHub
- **No podemos** usar Let's Encrypt con validación HTTP
- **SÍ podemos** usar SSH desde máquinas en la VPN
- **SÍ podemos** sincronizar archivos con rsync

Por eso implementamos el sistema donde:
1. La máquina local se conecta a GitHub (✅ funciona)
2. La máquina local se conecta al servidor vía SSH (✅ funciona)
3. La máquina local actúa como puente sincronizando código

**Esta arquitectura es permanente y no es un bug - es el diseño de la red.**

## Estructura del Sitio

### Tecnologías
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 8.x
- **Base de datos**: SQLite (para CMS)
- **Servidor web**: Apache con mod_rewrite
- **Control de versiones**: Git

### Tipografía y Colores
- **Tipografía**: Raleway (Google Fonts)
- **Colores principales**:
  - Azul primario: `#2c3e5c`
  - Naranja: `#f26522`
  - Verde WhatsApp: `#25d366`

### Estructura de Directorios

```
/var/www/petersen/public/
├── assets/
│   ├── css/
│   │   └── styles.css          # Estilos principales (~1700 líneas)
│   ├── js/
│   │   ├── main.js             # JavaScript principal (~600 líneas)
│   │   ├── contact-form.js     # Validación de formularios
│   │   ├── forms.js            # Manejo de formularios
│   │   └── job-application.js  # Formulario de empleo
│   ├── images/                 # Imágenes del sitio
│   │   ├── logos/              # Logos de marcas
│   │   ├── rubros/             # Imágenes de divisiones
│   │   ├── sucursales/         # Fotos de sucursales
│   │   ├── aliados/            # Logos de aliados
│   │   ├── blog/               # Imágenes de blog
│   │   └── recursos/           # Recursos descargables
│   └── video/
│       └── hero.mp4            # Video del hero
├── cms/                        # Sistema de administración
│   ├── database/
│   │   └── petersen_cms.db     # Base de datos SQLite
│   ├── includes/               # Clases PHP del CMS
│   ├── vendor/                 # Dependencias Composer
│   └── *.php                   # Páginas del CMS
├── catalogos/                  # PDFs de catálogos
├── includes/
│   ├── header.php              # Header del sitio
│   ├── footer.php              # Footer del sitio
│   ├── contact-handler.php     # Procesamiento de contacto
│   ├── form-handler.php        # Procesamiento de formularios
│   └── *.php                   # Otros handlers
├── mockups/                    # Mockups de diseño (14 archivos PNG)
├── index.php                   # Homepage
├── quienes-somos.php          # Página Quiénes Somos
├── servicios.php              # Página Servicios
├── sucursales.php             # Página Sucursales
├── recursos.php               # Página Recursos
├── aliados.php                # Página Aliados
├── contacto.php               # Página Contacto
├── blog.php                   # Página Blog
├── division-*.php             # Páginas de divisiones (6 archivos)
└── .htaccess                  # Configuración Apache
```

## Páginas del Sitio

### Páginas Principales
1. **Homepage** (`index.php`)
   - Hero con video de fondo
   - Secciones de divisiones
   - Marcas destacadas
   - Llamados a la acción

2. **Quiénes Somos** (`quienes-somos.php`)
   - Historia de la empresa
   - Misión, visión, valores
   - Equipo

3. **Servicios** (`servicios.php`)
   - Servicios ofrecidos
   - Soporte técnico
   - Asesoramiento

4. **Sucursales** (`sucursales.php`)
   - 8 sucursales en Paraguay
   - Mapas de ubicación
   - Información de contacto

5. **Recursos** (`recursos.php`)
   - Catálogos descargables (6 PDFs)
   - Documentación técnica

6. **Aliados** (`aliados.php`)
   - Marcas representadas
   - Logos de aliados comerciales

7. **Contacto** (`contacto.php`)
   - Formulario de contacto
   - Información de la empresa
   - Mapa de ubicación

8. **Blog** (`blog.php`)
   - Artículos y noticias
   - Posts individuales

### Páginas de Divisiones
- `division-forestal.php` - División Forestal
- `division-industrial.php` - División Industrial
- `division-construccion.php` - División Construcción
- `division-metalurgica.php` - División Metalúrgica
- `division-mecanica.php` - División Mecánica
- `division-ferreteria.php` - División Ferretería
- `division-bosque-y-jardin.php` - División Bosque y Jardín

## CMS (Sistema de Administración)

### Acceso
- **URL**: https://petersen.com.py/cms/
- **Autenticación**: Sistema de login con sesiones PHP
- **Base de datos**: SQLite (`cms/database/petersen_cms.db`)

### Funcionalidades del CMS
1. **Blog Management**
   - Crear/editar/eliminar posts
   - Subir imágenes
   - Editor de contenido

2. **Catálogos**
   - Gestión de PDFs
   - Tracking de descargas
   - Leads de catálogos

3. **Mensajes de Contacto**
   - Ver mensajes recibidos
   - Gestión de consultas

4. **Medios**
   - Subida de imágenes
   - Galería de medios

5. **Configuración del Sitio**
   - Opciones generales
   - Configuración de formularios

6. **Usuarios**
   - Gestión de usuarios del CMS
   - Permisos

### Dependencias del CMS
- **PHPMailer**: Para envío de emails
- **Composer**: Gestión de dependencias

## Sistema de Control de Versiones Git

### Arquitectura "Puente"

El servidor de producción **NO tiene conectividad directa a GitHub** debido a restricciones de firewall (timeout SSL). Por eso implementamos un sistema donde la máquina local actúa como "puente":

```
Máquina Local (Git) ←→ GitHub (backup/colaboración)
         ↓ (rsync + SSH + Git)
Servidor Producción (Git)
```

### Configuración Git

#### En Local
- **Repositorio**: /opt/homebrew/var/www/petersen
- **Remoto**: https://github.com/keuch2/petersen
- **Rama principal**: `main`

#### En Servidor
- **Repositorio**: /var/www/petersen/public
- **Usuario Git**: Petersen Deploy (deploy@petersen.com.py)
- **Rama principal**: `main`
- **Sin remoto**: El servidor no se conecta a GitHub

### Scripts de Despliegue

#### 1. `deploy.sh` - Despliegue Completo

**Ubicación**: `/opt/homebrew/var/www/petersen/deploy.sh`

**Funcionalidades**:
1. ✅ Verifica que no haya cambios sin commitear
2. ✅ Sube cambios a GitHub (`git push origin main`)
3. ✅ Crea backup automático en el servidor
4. ✅ Inicializa Git en el servidor (si no existe)
5. ✅ Sincroniza archivos con `rsync` (incluyendo `.git`)
6. ✅ Crea commit automático en el servidor
7. ✅ Ajusta permisos (www-data:www-data, 755)
8. ✅ Verifica que el sitio esté funcionando (HTTP 200)

**Archivos excluidos del sync**:
- `node_modules/`
- `.DS_Store`
- `*.log`
- `logs/`
- `deploy.sh`
- `DEPLOYMENT.md`
- `mockups/`
- `www_petersen_com_py18-06-2025/` (certificados SSL)

**Uso**:
```bash
cd /opt/homebrew/var/www/petersen
./deploy.sh
```

**Output esperado**:
```
🚀 Iniciando despliegue a producción...
📋 Verificando rama actual...
📤 Subiendo cambios a GitHub...
💾 Creando backup en el servidor...
🔧 Configurando Git en el servidor...
📦 Sincronizando archivos con el servidor...
📝 Commiteando cambios en el servidor...
🔐 Ajustando permisos...
🔍 Verificando sitio...
✅ Despliegue exitoso!
🌐 Sitio disponible en: https://petersen.com.py
```

#### 2. `git-status-server.sh` - Verificar Estado Git en Servidor

**Ubicación**: `/opt/homebrew/var/www/petersen/git-status-server.sh`

**Funcionalidades**:
- Verifica si Git está inicializado
- Muestra rama actual
- Muestra último commit
- Muestra estado del repositorio
- Muestra configuración Git

**Uso**:
```bash
cd /opt/homebrew/var/www/petersen
./git-status-server.sh
```

### Flujo de Trabajo Completo

#### Desarrollo y Despliegue

```bash
# 1. Navegar al proyecto
cd /opt/homebrew/var/www/petersen

# 2. Verificar estado
git status

# 3. Hacer cambios en los archivos
# ... editar archivos PHP, CSS, JS, etc. ...

# 4. Agregar cambios al staging
git add .

# 5. Commitear cambios
git commit -m "Descripción clara de los cambios"

# 6. Desplegar a producción (hace todo automáticamente)
./deploy.sh
```

#### Verificar Estado en Servidor

```bash
# Ver estado de Git en el servidor
./git-status-server.sh
```

#### Trabajar con Ramas (Opcional)

```bash
# Crear rama para feature
git checkout -b feature/nueva-funcionalidad

# Hacer cambios y commits
git add .
git commit -m "Implementar nueva funcionalidad"

# Volver a main y mergear
git checkout main
git merge feature/nueva-funcionalidad

# Desplegar
./deploy.sh
```

### Comandos Git Útiles

```bash
# Ver historial de commits
git log --oneline --graph

# Ver diferencias antes de commitear
git diff

# Ver diferencias de un archivo específico
git diff archivo.php

# Deshacer cambios en un archivo (antes de commit)
git checkout -- archivo.php

# Ver último commit
git log -1

# Ver archivos modificados
git status -s

# Ver ramas
git branch -a

# Cambiar entre ramas
git checkout nombre-rama
```

### Resolución de Problemas Git

#### Error: "Hay cambios sin commitear"
```bash
# Ver qué archivos están modificados
git status

# Opción 1: Commitear cambios
git add .
git commit -m "Descripción"

# Opción 2: Descartar cambios
git checkout -- archivo.php

# Opción 3: Guardar temporalmente
git stash
# ... hacer otras cosas ...
git stash pop
```

#### Error de permisos en el servidor
```bash
ssh -p 2250 root@181.40.91.194 "chown -R www-data:www-data /var/www/petersen/public && chmod -R 755 /var/www/petersen/public"
```

#### Verificar conectividad SSH
```bash
ssh -p 2250 root@181.40.91.194 "echo 'Conexión exitosa'"
```

## Configuración SSL/HTTPS

### Certificado SSL
- **Tipo**: Certificado comercial SSL2BUY EMEA
- **Dominio**: www.petersen.com.py
- **Válido desde**: 18 de junio de 2025
- **Válido hasta**: 18 de julio de 2026
- **Emisor**: SSL2BUY EMEA RSA Domain Validation Secure Server CA

### Ubicación de Certificados
```
/etc/ssl/petersen/
├── www_petersen_com_py.crt           # Certificado del dominio
├── www.petersen.com.py.key           # Clave privada
├── ca-bundle.crt                     # Cadena de certificados CA
├── SSL2BUYEMEARSADomainValidationSecureServerCA.crt
└── USERTrustRSACertificationAuthority.crt
```

### Configuración Apache

**VirtualHost HTTP (puerto 80)**:
- Archivo: `/etc/apache2/sites-available/petersen.conf`
- Función: Redirige automáticamente a HTTPS (301)

**VirtualHost HTTPS (puerto 443)**:
- Archivo: `/etc/apache2/sites-available/petersen-ssl.conf`
- Certificado SSL configurado
- DocumentRoot: `/var/www/petersen/public`

### Renovación de Certificado

El certificado expira en **julio 2026**. Para renovar:

1. Obtener nuevo certificado de SSL2BUY
2. Subir archivos al servidor:
   ```bash
   scp -P 2250 nuevo_certificado.crt root@181.40.91.194:/etc/ssl/petersen/
   scp -P 2250 nueva_clave.key root@181.40.91.194:/etc/ssl/petersen/
   ```
3. Reiniciar Apache:
   ```bash
   ssh -p 2250 root@181.40.91.194 "systemctl restart apache2"
   ```

## Información del Servidor

### Acceso SSH
- **Host**: 181.40.91.194
- **Puerto**: 2250
- **Usuario**: root
- **Comando**: `ssh -p 2250 root@181.40.91.194`

### Servicios
- **Apache**: `systemctl status apache2`
- **Logs Apache**: `/var/log/apache2/petersen_ssl_error.log`
- **Logs de acceso**: `/var/log/apache2/petersen_ssl_access.log`

### Permisos
- **Propietario**: www-data:www-data
- **Permisos directorios**: 755
- **Permisos archivos**: 644

### Backups
Los backups automáticos se crean en `/var/www/` con el formato:
```
backup-YYYYMMDD-HHMMSS.tar.gz
```

## Mantenimiento y Actualizaciones

### Actualizar Contenido del Sitio

1. **Editar archivos localmente**
2. **Commitear cambios**: `git commit -m "Descripción"`
3. **Desplegar**: `./deploy.sh`

### Actualizar CMS

1. Acceder a https://petersen.com.py/cms/
2. Usar el panel de administración
3. Los cambios se guardan en la base de datos SQLite

### Actualizar Catálogos

1. Subir PDFs a `/catalogos/`
2. Actualizar referencias en el CMS
3. Commitear y desplegar

### Actualizar Imágenes

1. Subir imágenes a `/assets/images/`
2. Optimizar imágenes antes de subir
3. Actualizar referencias en archivos PHP
4. Commitear y desplegar

## Mejores Prácticas

### Commits
- Usar mensajes descriptivos
- Commitear cambios relacionados juntos
- No commitear archivos sensibles (.env, passwords)

### Despliegue
- Siempre probar cambios localmente primero
- Verificar que el sitio funcione después del deploy
- Revisar logs si hay problemas

### Seguridad
- No commitear certificados SSL
- No commitear credenciales
- Mantener permisos correctos en el servidor
- Actualizar dependencias regularmente

### Performance
- Optimizar imágenes antes de subir
- Minificar CSS/JS en producción
- Usar caché de navegador
- Comprimir archivos con gzip

## Contacto y Soporte

Para problemas con el sitio o el sistema de despliegue:
- Revisar logs de Apache
- Verificar estado de Git con `./git-status-server.sh`
- Revisar backups en `/var/www/backup-*`

## Notas Importantes

1. **El servidor NO tiene acceso directo a GitHub** - Todo pasa por la máquina local
2. **Los certificados SSL deben renovarse manualmente** antes de julio 2026
3. **Los backups se crean automáticamente** en cada despliegue
4. **El CMS usa SQLite** - No requiere MySQL
5. **Apache corre como www-data** - Los permisos deben ser correctos
6. **El sitio usa mod_rewrite** - El .htaccess es importante

## Historial de Cambios Importantes

- **2026-01-28**: Configuración inicial de SSL con certificado comercial
- **2026-02-01**: Implementación de sistema Git con arquitectura "puente"
- **2026-02-01**: Creación de scripts de despliegue automatizado
- **2026-02-01**: Sincronización completa del repositorio con producción
