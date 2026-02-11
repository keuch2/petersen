#!/bin/bash

# Script de despliegue para Petersen.com.py
# Uso: ./deploy.sh

echo "🚀 Iniciando despliegue a producción..."

# Configuración
SERVER="root@181.40.91.194"
PORT="2250"
REMOTE_DIR="/var/www/petersen/public"
LOCAL_DIR="/opt/homebrew/var/www/petersen"
export SSHPASS='2026.WEBPetersen'

# Opciones SSH reutilizables
SSH_OPTS="-o StrictHostKeyChecking=no -p ${PORT}"

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar que estamos en la rama correcta
echo -e "${YELLOW}📋 Verificando rama actual...${NC}"
CURRENT_BRANCH=$(git branch --show-current)
echo "Rama actual: $CURRENT_BRANCH"

# Verificar que no hay cambios sin commitear
if [[ -n $(git status -s) ]]; then
    echo -e "${RED}❌ Error: Hay cambios sin commitear${NC}"
    echo "Por favor, commitea tus cambios antes de desplegar:"
    git status -s
    exit 1
fi

# Push a GitHub
echo -e "${YELLOW}📤 Subiendo cambios a GitHub...${NC}"
git push origin main || { echo -e "${RED}❌ Error al subir a GitHub${NC}"; exit 1; }

# Paso 1: Backup + configurar Git en el servidor (una sola conexión SSH)
echo -e "${YELLOW}💾 Creando backup y configurando Git en el servidor...${NC}"
sshpass -e ssh ${SSH_OPTS} ${SERVER} "
    cd ${REMOTE_DIR} && \
    tar -czf ../backup-\$(date +%Y%m%d-%H%M%S).tar.gz . 2>/dev/null; \
    if [ ! -d .git ]; then \
        git init && \
        git config user.email 'deploy@petersen.com.py' && \
        git config user.name 'Petersen Deploy' && \
        git branch -m main; \
    fi
" || echo -e "${YELLOW}⚠️  Backup/Git config: continuando...${NC}"

# Paso 2: Sincronizar archivos con rsync
echo -e "${YELLOW}📦 Sincronizando archivos con el servidor...${NC}"
rsync -avz --delete \
    --exclude='node_modules' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='logs/' \
    --exclude='deploy.sh' \
    --exclude='DEPLOYMENT.md' \
    --exclude='mockups/' \
    --exclude='www_petersen_com_py18-06-2025/' \
    -e "sshpass -e ssh ${SSH_OPTS}" \
    ${LOCAL_DIR}/ ${SERVER}:${REMOTE_DIR}/ || { echo -e "${RED}❌ Error en rsync${NC}"; exit 1; }

# Paso 3: Commit en servidor + permisos + verificación (una sola conexión SSH)
echo -e "${YELLOW}📝 Finalizando despliegue en el servidor...${NC}"
COMMIT_MSG="Deploy: $(git log -1 --pretty=format:'%h - %s')"
sshpass -e ssh ${SSH_OPTS} ${SERVER} "
    cd ${REMOTE_DIR} && \
    git add -A && \
    (git diff-index --quiet HEAD 2>/dev/null || git commit -m '${COMMIT_MSG}') && \
    chown -R www-data:www-data ${REMOTE_DIR} && \
    chmod -R 755 ${REMOTE_DIR} && \
    echo 'DEPLOY_OK'
" && echo -e "${GREEN}✅ Servidor actualizado correctamente${NC}" \
  || echo -e "${YELLOW}⚠️  Algunos pasos finales pueden no haberse completado${NC}"

echo ""
echo "📊 Resumen del despliegue:"
echo "  - Rama: $CURRENT_BRANCH"
echo "  - Commit: $(git log -1 --pretty=format:'%h - %s')"
echo "  - Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
echo -e "${GREEN}🌐 Sitio disponible en: https://petersen.com.py${NC}"
echo -e "${GREEN}🎉 Despliegue completado!${NC}"
