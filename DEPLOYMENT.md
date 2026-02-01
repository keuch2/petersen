# Guía de Despliegue - Petersen.com.py

## Repositorio Git
- **Repositorio**: https://github.com/keuch2/petersen
- **Rama principal**: `main`
- **Rama de producción**: `stable`

## Flujo de Trabajo

### 1. Desarrollo Local
```bash
# Navegar al directorio del proyecto
cd /opt/homebrew/var/www/petersen

# Verificar estado
git status

# Crear una nueva rama para cambios
git checkout -b feature/nombre-del-cambio

# Hacer cambios y commitear
git add .
git commit -m "Descripción de los cambios"

# Subir cambios a GitHub
git push origin feature/nombre-del-cambio
```

### 2. Despliegue a Producción

#### Opción A: Despliegue Automático con Script (Recomendado)
```bash
# Asegurarse de estar en la rama main
git checkout main

# Mergear cambios si es necesario
git merge feature/nombre-del-cambio

# Ejecutar el script de despliegue
./deploy.sh
```

El script `deploy.sh` realiza automáticamente:
- ✅ Verifica que no haya cambios sin commitear
- ✅ Sube cambios a GitHub
- ✅ Crea backup automático en el servidor
- ✅ Sincroniza archivos con rsync
- ✅ Ajusta permisos correctamente
- ✅ Verifica que el sitio esté funcionando

#### Opción B: Despliegue Manual
```bash
# Asegurarse de estar en la rama stable
git checkout stable

# Mergear cambios desde main
git merge main

# Subir a GitHub
git push origin stable

# Conectar al servidor de producción
ssh -p 2250 root@181.40.91.194

# En el servidor, actualizar el código
cd /var/www/petersen/public
git pull origin stable
```

#### Opción B: Despliegue Automático con Git Hooks
Ver sección "Configuración de Git Hooks" más abajo.

### 3. Verificación Post-Despliegue
- Verificar el sitio en: https://petersen.com.py
- Revisar logs de Apache: `/var/log/apache2/petersen_ssl_error.log`

## Configuración del Servidor de Producción

### Información del Servidor
- **IP**: 181.40.91.194
- **Puerto SSH**: 2250
- **Usuario**: root
- **Directorio web**: /var/www/petersen/public
- **Servidor web**: Apache 2.4.58
- **SSL**: Certificado válido hasta julio 2026

### Estructura de Directorios
```
/var/www/petersen/
├── public/              # Document root (accesible vía web)
│   ├── assets/
│   ├── includes/
│   ├── cms/
│   ├── index.php
│   └── ...
└── .git/               # Repositorio Git
```

## Configuración de Git Hooks (Opcional)

Para automatizar el despliegue, se puede configurar un webhook de GitHub o un post-receive hook en el servidor.

### Post-Receive Hook en el Servidor
```bash
# En el servidor
cd /var/www/petersen/.git/hooks
nano post-receive
```

Contenido del hook:
```bash
#!/bin/bash
GIT_WORK_TREE=/var/www/petersen/public git checkout -f stable
echo "Código actualizado en producción: $(date)"
```

Dar permisos de ejecución:
```bash
chmod +x post-receive
```

## Comandos Útiles

### Ver historial de commits
```bash
git log --oneline --graph --all
```

### Ver diferencias antes de commitear
```bash
git diff
```

### Deshacer cambios locales
```bash
git checkout -- archivo.php
```

### Ver ramas
```bash
git branch -a
```

### Cambiar entre ramas
```bash
git checkout nombre-rama
```

## Resolución de Problemas

### Error: "Your local changes would be overwritten"
```bash
# Guardar cambios temporalmente
git stash

# Hacer pull
git pull

# Recuperar cambios
git stash pop
```

### Error de permisos en el servidor
```bash
# Ajustar permisos
sudo chown -R www-data:www-data /var/www/petersen/public
sudo chmod -R 755 /var/www/petersen/public
```

## Contacto y Soporte
Para problemas con el despliegue, contactar al equipo de desarrollo.
