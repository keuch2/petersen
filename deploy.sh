#!/bin/bash

# Script de despliegue para Petersen.com.py
# Uso: ./deploy.sh

set -e

echo "🚀 Iniciando despliegue a producción..."

# Configuración
SERVER="root@181.40.91.194"
PORT="2250"
REMOTE_DIR="/var/www/petersen/public"
LOCAL_DIR="/opt/homebrew/var/www/petersen"
SSHPASS="2026.WEBPetersen"

# Comandos SSH y SCP con contraseña automática
SSH_CMD="sshpass -p $SSHPASS ssh -o StrictHostKeyChecking=no -p $PORT $SERVER"
RSYNC_SSH="sshpass -p $SSHPASS ssh -o StrictHostKeyChecking=no -p $PORT"

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
git push origin main

# Crear backup en el servidor
echo -e "${YELLOW}💾 Creando backup en el servidor...${NC}"
$SSH_CMD "cd $REMOTE_DIR && tar -czf ../backup-\$(date +%Y%m%d-%H%M%S).tar.gz ." || true

# Inicializar Git en el servidor si no existe
echo -e "${YELLOW}🔧 Configurando Git en el servidor...${NC}"
$SSH_CMD "cd $REMOTE_DIR && \
    if [ ! -d .git ]; then \
        git init && \
        git config user.email 'deploy@petersen.com.py' && \
        git config user.name 'Petersen Deploy' && \
        git branch -m main; \
    fi" || echo "Git ya está configurado"

# Subir archivos al servidor usando rsync (incluyendo .git)
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
    -e "$RSYNC_SSH" \
    $LOCAL_DIR/ $SERVER:$REMOTE_DIR/

# Commitear cambios en el servidor
echo -e "${YELLOW}📝 Commiteando cambios en el servidor...${NC}"
COMMIT_MSG="Deploy: $(git log -1 --pretty=format:'%h - %s')"
$SSH_CMD "cd $REMOTE_DIR && \
    git add -A && \
    (git diff-index --quiet HEAD || git commit -m '$COMMIT_MSG')" || echo "Sin cambios para commitear"

# Ajustar permisos
echo -e "${YELLOW}🔐 Ajustando permisos...${NC}"
$SSH_CMD "chown -R www-data:www-data $REMOTE_DIR && chmod -R 755 $REMOTE_DIR"

# Verificar sitio (desde el servidor mismo, ya que estamos en VPN)
echo -e "${YELLOW}🔍 Verificando sitio...${NC}"
$SSH_CMD "curl -s -o /dev/null -w '%{http_code}' http://localhost/" | grep -q "200" \
    && echo -e "${GREEN}✅ Sitio respondiendo correctamente${NC}" \
    || echo -e "${YELLOW}⚠️  No se pudo verificar el sitio (puede estar funcionando correctamente)${NC}"

echo ""
echo "📊 Resumen del despliegue:"
echo "  - Rama: $CURRENT_BRANCH"
echo "  - Commit: $(git log -1 --pretty=format:'%h - %s')"
echo "  - Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
echo -e "${GREEN}🌐 Sitio disponible en: https://petersen.com.py${NC}"
echo -e "${GREEN}🎉 Despliegue completado!${NC}"
