# 🔐 POLÍTICAS DE SEGURIDAD
## Sitio Web Petersen - Guía de Mejores Prácticas

**Versión:** 1.0  
**Fecha:** 7 de Enero, 2026  
**Clasificación:** Confidencial - Uso Interno

---

## 📋 ÍNDICE

1. [Introducción](#introducción)
2. [Políticas de Contraseñas](#políticas-de-contraseñas)
3. [Gestión de Accesos](#gestión-de-accesos)
4. [Seguridad de Sesiones](#seguridad-de-sesiones)
5. [Gestión de Archivos](#gestión-de-archivos)
6. [Actualizaciones y Parches](#actualizaciones-y-parches)
7. [Backups y Recuperación](#backups-y-recuperación)
8. [Monitoreo y Auditoría](#monitoreo-y-auditoría)
9. [Respuesta a Incidentes](#respuesta-a-incidentes)
10. [Cumplimiento y Responsabilidades](#cumplimiento-y-responsabilidades)

---

## 1. INTRODUCCIÓN

### 1.1 Propósito
Este documento establece las políticas y procedimientos de seguridad que deben seguirse para proteger el sitio web de Petersen y su sistema de gestión de contenidos (CMS).

### 1.2 Alcance
Aplica a:
- Todos los usuarios del CMS (Administradores y Editores)
- Personal de TI responsable del mantenimiento
- Desarrolladores que trabajen en el proyecto
- Cualquier persona con acceso al servidor

### 1.3 Principios Fundamentales
- **Confidencialidad:** Proteger información sensible
- **Integridad:** Mantener datos precisos y completos
- **Disponibilidad:** Asegurar acceso cuando se necesite
- **Trazabilidad:** Registrar todas las acciones críticas

---

## 2. POLÍTICAS DE CONTRASEÑAS

### 2.1 Requisitos de Contraseñas

**OBLIGATORIO:**
- ✅ Mínimo 12 caracteres
- ✅ Al menos 1 letra mayúscula
- ✅ Al menos 1 letra minúscula
- ✅ Al menos 1 número
- ✅ Al menos 1 carácter especial (!@#$%^&*)

**PROHIBIDO:**
- ❌ Usar contraseñas por defecto
- ❌ Reutilizar contraseñas anteriores
- ❌ Compartir contraseñas entre usuarios
- ❌ Usar información personal (nombres, fechas)
- ❌ Usar palabras del diccionario

### 2.2 Gestión de Contraseñas

**Cambio de Contraseñas:**
- Cambiar contraseña inmediatamente después del primer login
- Cambiar cada 90 días (recomendado)
- Cambiar inmediatamente si se sospecha compromiso

**Almacenamiento:**
- Usar gestor de contraseñas (LastPass, 1Password, Bitwarden)
- NUNCA guardar en archivos de texto plano
- NUNCA enviar por email o mensajería

**Recuperación:**
- Solo administradores pueden resetear contraseñas
- Verificar identidad antes de resetear
- Generar contraseña temporal fuerte
- Forzar cambio en primer login

### 2.3 Ejemplos

**✅ Contraseñas Fuertes:**
```
P3t3rs3n2026!Secure
Cms@Admin#2026Strong
B10g$P0st&Manager99
```

**❌ Contraseñas Débiles:**
```
admin123
petersen2026
password
12345678
```

---

## 3. GESTIÓN DE ACCESOS

### 3.1 Principio de Mínimo Privilegio

**Regla de Oro:**
> Cada usuario debe tener SOLO los permisos necesarios para realizar su trabajo.

**Roles Definidos:**

**Administrador:**
- Gestión completa del sistema
- Creación/eliminación de usuarios
- Acceso a configuración
- Acceso a logs de seguridad

**Editor:**
- Gestión de contenido (blog, medios)
- SIN acceso a usuarios
- SIN acceso a configuración
- SIN acceso a logs

### 3.2 Creación de Usuarios

**Proceso:**
1. Solicitud formal por escrito
2. Aprobación del supervisor
3. Creación con rol apropiado
4. Envío de credenciales por canal seguro
5. Documentar en registro de usuarios

**Información Requerida:**
- Nombre completo
- Email corporativo
- Rol solicitado
- Justificación del acceso
- Fecha de inicio

### 3.3 Revisión de Accesos

**Frecuencia:** Trimestral

**Acciones:**
- Revisar lista de usuarios activos
- Verificar que roles sean apropiados
- Desactivar cuentas inactivas (>30 días)
- Eliminar cuentas de ex-empleados

### 3.4 Revocación de Accesos

**Inmediata en caso de:**
- Terminación de contrato
- Cambio de rol/departamento
- Sospecha de compromiso
- Violación de políticas

**Proceso:**
1. Desactivar cuenta inmediatamente
2. Cambiar contraseñas de cuentas compartidas
3. Revisar logs de actividad reciente
4. Documentar motivo de revocación

---

## 4. SEGURIDAD DE SESIONES

### 4.1 Configuración de Sesiones

**Parámetros Obligatorios:**
```php
session.cookie_httponly = 1      // Prevenir XSS
session.cookie_samesite = Strict // Prevenir CSRF
session.cookie_secure = 1        // Solo HTTPS (producción)
session.gc_maxlifetime = 3600    // 1 hora de timeout
```

### 4.2 Buenas Prácticas

**Al Iniciar Sesión:**
- ✅ Regenerar ID de sesión
- ✅ Registrar evento en log
- ✅ Verificar IP y User-Agent
- ✅ Implementar rate limiting

**Durante la Sesión:**
- ✅ Validar timeout en cada request
- ✅ Verificar CSRF tokens
- ✅ Mantener actividad del usuario

**Al Cerrar Sesión:**
- ✅ Destruir sesión completamente
- ✅ Limpiar cookies
- ✅ Registrar evento en log
- ✅ Redirigir a página de login

### 4.3 Protección contra Ataques

**Session Hijacking:**
- Usar HTTPS siempre
- Validar IP del cliente
- Regenerar ID periódicamente

**Session Fixation:**
- Regenerar ID después del login
- No aceptar IDs de sesión por URL

**CSRF (Cross-Site Request Forgery):**
- Tokens CSRF en todos los formularios
- Validar origen de requests
- Usar SameSite cookies

---

## 5. GESTIÓN DE ARCHIVOS

### 5.1 Subida de Archivos

**Validaciones Obligatorias:**

1. **Tipo de Archivo:**
   - Validar MIME type
   - Validar extensión
   - Verificar magic bytes
   - Usar whitelist (NO blacklist)

2. **Tamaño:**
   - Límite máximo: 50MB
   - Rechazar archivos vacíos
   - Validar antes de procesar

3. **Contenido:**
   - Escanear con antivirus (recomendado)
   - Validar estructura de archivos
   - Sanitizar nombres de archivo

**Tipos Permitidos:**
```
Imágenes: JPG, PNG, GIF, WEBP
Videos: MP4, AVI, MOV
Documentos: PDF, DOC, DOCX, XLS, XLSX
```

### 5.2 Almacenamiento

**Estructura de Directorios:**
```
assets/media/
├── images/      (Imágenes)
├── videos/      (Videos)
├── documents/   (Documentos)
└── other/       (Otros)
```

**Permisos:**
```bash
Directorios: 750 (rwxr-x---)
Archivos: 644 (rw-r--r--)
```

**Protección:**
- Deshabilitar ejecución de PHP en uploads
- Usar .htaccess restrictivo
- Renombrar archivos con nombres únicos
- Almacenar fuera del webroot (ideal)

### 5.3 Eliminación

**Proceso:**
- Eliminar archivo físico del servidor
- Eliminar registro de base de datos
- Registrar acción en log
- Verificar eliminación completa

**Archivos Sensibles:**
- Sobrescribir antes de eliminar
- Usar herramientas de borrado seguro
- Documentar eliminación

---

## 6. ACTUALIZACIONES Y PARCHES

### 6.1 Política de Actualizaciones

**Frecuencia:**
- **Críticas:** Inmediato (< 24 horas)
- **Altas:** Semanal
- **Medias:** Mensual
- **Bajas:** Trimestral

**Componentes a Actualizar:**
- PHP y extensiones
- Apache/Nginx
- Sistema operativo
- Bibliotecas de terceros (Quill.js)

### 6.2 Proceso de Actualización

**Pasos:**
1. Revisar changelog y notas de seguridad
2. Realizar backup completo
3. Probar en entorno de desarrollo
4. Programar ventana de mantenimiento
5. Aplicar actualización
6. Verificar funcionamiento
7. Monitorear por 24-48 horas
8. Documentar cambios

**Rollback:**
- Tener plan de rollback preparado
- Mantener backups accesibles
- Documentar procedimiento

### 6.3 Monitoreo de Vulnerabilidades

**Fuentes:**
- CVE Database
- PHP Security Advisories
- OWASP
- Vendor security bulletins

**Suscripciones:**
- Listas de correo de seguridad
- Alertas de GitHub (si aplica)
- Feeds RSS de seguridad

---

## 7. BACKUPS Y RECUPERACIÓN

### 7.1 Política de Backups

**Frecuencia:**
- **Base de Datos:** Diario
- **Archivos de Medios:** Semanal
- **Código Fuente:** Con cada cambio (Git)
- **Configuración:** Mensual

**Retención:**
- Diarios: 7 días
- Semanales: 4 semanas
- Mensuales: 12 meses
- Anuales: 3 años

### 7.2 Tipos de Backup

**Completo:**
- Todo el sitio y base de datos
- Primer día de cada mes
- Almacenar off-site

**Incremental:**
- Solo cambios desde último backup
- Diariamente
- Más rápido y eficiente

**Diferencial:**
- Cambios desde último backup completo
- Semanalmente
- Balance entre completo e incremental

### 7.3 Almacenamiento de Backups

**Ubicaciones:**
- **Primaria:** Servidor local
- **Secundaria:** Servidor remoto
- **Terciaria:** Cloud storage (encriptado)

**Seguridad:**
- Encriptar backups (AES-256)
- Proteger con contraseña
- Verificar integridad (checksums)
- Limitar acceso

### 7.4 Pruebas de Restauración

**Frecuencia:** Trimestral

**Proceso:**
1. Seleccionar backup aleatorio
2. Restaurar en entorno de prueba
3. Verificar integridad de datos
4. Probar funcionalidad
5. Documentar resultados
6. Corregir problemas encontrados

### 7.5 Plan de Recuperación ante Desastres (DRP)

**Escenarios:**
- Falla de hardware
- Ataque cibernético
- Corrupción de datos
- Desastre natural

**RTO (Recovery Time Objective):** 4 horas  
**RPO (Recovery Point Objective):** 24 horas

**Contactos de Emergencia:**
- Administrador de Sistemas
- Proveedor de Hosting
- Soporte Técnico

---

## 8. MONITOREO Y AUDITORÍA

### 8.1 Eventos a Registrar

**Autenticación:**
- Login exitoso/fallido
- Logout
- Cambio de contraseña
- Intentos de acceso no autorizado

**Acciones Críticas:**
- Creación/modificación/eliminación de usuarios
- Creación/modificación/eliminación de posts
- Subida/eliminación de archivos
- Cambios en configuración

**Seguridad:**
- Violaciones de CSRF
- Rate limiting activado
- Errores de validación
- Intentos de inyección

### 8.2 Formato de Logs

**Información Requerida:**
```
[Timestamp] EVENT: nombre_evento | USER: usuario | IP: dirección_ip | DETAILS: detalles | UA: user_agent
```

**Ejemplo:**
```
[2026-01-07 10:30:15] EVENT: LOGIN_SUCCESS | USER: admin | IP: 192.168.1.100 | DETAILS: {"role":"administrador"} | UA: Mozilla/5.0...
```

### 8.3 Revisión de Logs

**Frecuencia:**
- **Diaria:** Eventos críticos
- **Semanal:** Todos los eventos
- **Mensual:** Análisis de tendencias

**Alertas Automáticas:**
- Múltiples logins fallidos
- Acceso desde IP desconocida
- Cambios en usuarios administradores
- Errores críticos del sistema

### 8.4 Retención de Logs

**Períodos:**
- Logs de seguridad: 1 año
- Logs de aplicación: 6 meses
- Logs de acceso: 3 meses

**Archivado:**
- Comprimir logs antiguos
- Mover a almacenamiento frío
- Mantener accesibles para auditorías

---

## 9. RESPUESTA A INCIDENTES

### 9.1 Clasificación de Incidentes

**Crítico:**
- Compromiso de credenciales de administrador
- Acceso no autorizado a base de datos
- Defacement del sitio
- Pérdida de datos

**Alto:**
- Múltiples intentos de login fallidos
- Vulnerabilidad explotable descubierta
- Malware detectado

**Medio:**
- Comportamiento anómalo de usuario
- Errores de configuración
- Violaciones menores de política

**Bajo:**
- Intentos de acceso fallidos aislados
- Errores de aplicación no críticos

### 9.2 Procedimiento de Respuesta

**Fase 1: Detección e Identificación**
1. Detectar el incidente
2. Clasificar severidad
3. Documentar evidencia inicial
4. Notificar a responsables

**Fase 2: Contención**
1. Aislar sistemas afectados
2. Prevenir propagación
3. Preservar evidencia
4. Implementar controles temporales

**Fase 3: Erradicación**
1. Identificar causa raíz
2. Eliminar amenaza
3. Cerrar vulnerabilidades
4. Verificar limpieza completa

**Fase 4: Recuperación**
1. Restaurar desde backups limpios
2. Cambiar credenciales comprometidas
3. Aplicar parches de seguridad
4. Monitorear intensivamente

**Fase 5: Lecciones Aprendidas**
1. Documentar incidente completo
2. Analizar respuesta
3. Identificar mejoras
4. Actualizar procedimientos

### 9.3 Comunicación

**Interna:**
- Notificar a equipo de TI inmediatamente
- Informar a dirección según severidad
- Mantener comunicación constante

**Externa:**
- Evaluar necesidad de notificación
- Preparar comunicado si es necesario
- Coordinar con legal/PR

**Documentación:**
- Bitácora detallada del incidente
- Evidencia recolectada
- Acciones tomadas
- Resultados y conclusiones

---

## 10. CUMPLIMIENTO Y RESPONSABILIDADES

### 10.1 Responsabilidades por Rol

**Administrador de Sistemas:**
- Mantener seguridad del servidor
- Aplicar actualizaciones
- Gestionar backups
- Monitorear logs
- Responder a incidentes

**Administrador del CMS:**
- Gestionar usuarios
- Revisar actividad
- Aplicar políticas de acceso
- Reportar anomalías

**Editores:**
- Usar contraseñas fuertes
- Cerrar sesión al terminar
- Reportar actividad sospechosa
- Seguir políticas de contenido

**Desarrolladores:**
- Seguir prácticas de código seguro
- Documentar cambios
- Probar en desarrollo
- No hardcodear credenciales

### 10.2 Capacitación

**Frecuencia:** Anual (mínimo)

**Temas:**
- Políticas de seguridad
- Reconocimiento de phishing
- Gestión de contraseñas
- Uso seguro del CMS
- Respuesta a incidentes

**Evaluación:**
- Quiz de conocimientos
- Simulacros de phishing
- Ejercicios prácticos

### 10.3 Auditorías

**Internas:**
- Trimestral
- Revisar cumplimiento de políticas
- Verificar configuraciones
- Probar controles

**Externas:**
- Anual (recomendado)
- Penetration testing
- Revisión de código
- Evaluación de infraestructura

### 10.4 Sanciones

**Violaciones Menores:**
- Advertencia verbal
- Capacitación adicional
- Documentación en expediente

**Violaciones Graves:**
- Advertencia escrita
- Suspensión de acceso
- Medidas disciplinarias
- Terminación de contrato (casos extremos)

**Ejemplos de Violaciones Graves:**
- Compartir credenciales
- Acceso no autorizado
- Modificación no autorizada de datos
- Deshabilitación de controles de seguridad
- No reportar incidentes

---

## 📋 CHECKLIST DE SEGURIDAD DIARIA

**Para Administradores:**
- [ ] Revisar logs de seguridad
- [ ] Verificar backups completados
- [ ] Revisar intentos de login fallidos
- [ ] Verificar espacio en disco
- [ ] Revisar alertas del sistema

**Para Usuarios:**
- [ ] Usar contraseña fuerte
- [ ] Cerrar sesión al terminar
- [ ] No compartir credenciales
- [ ] Reportar actividad sospechosa
- [ ] Mantener software actualizado

---

## 📞 CONTACTOS DE SEGURIDAD

**Incidentes de Seguridad:**
- Email: security@petersen.com.py
- Teléfono: +595 21 XXX XXXX (24/7)

**Soporte Técnico:**
- Email: soporte@petersen.com.py
- Teléfono: +595 21 XXX XXXX

**Emergencias:**
- Contactar inmediatamente al Administrador de Sistemas

---

## 📚 REFERENCIAS

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)
- [ISO 27001](https://www.iso.org/isoiec-27001-information-security.html)

---

## 📝 HISTORIAL DE REVISIONES

| Versión | Fecha | Autor | Cambios |
|---------|-------|-------|---------|
| 1.0 | 2026-01-07 | Boris Dedoff | Versión inicial |

---

## ✍️ ACEPTACIÓN

**He leído y entendido las políticas de seguridad establecidas en este documento y me comprometo a cumplirlas.**

```
Nombre: _______________________________
Firma: ________________________________
Fecha: ________________________________
Rol: __________________________________
```

---

**ESTE DOCUMENTO ES CONFIDENCIAL Y DE USO INTERNO EXCLUSIVO**

**Última actualización:** 7 de Enero, 2026
