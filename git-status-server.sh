#!/bin/bash

# Script para verificar el estado de Git en el servidor de producción
# Uso: ./git-status-server.sh

# Configuración
SERVER="root@181.40.91.194"
PORT="2250"
REMOTE_DIR="/var/www/petersen/public"
SSHPASS="2026.WEBPetersen"
SSH_CMD="sshpass -p $SSHPASS ssh -o StrictHostKeyChecking=no -p $PORT $SERVER"

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔍 Verificando estado de Git en el servidor de producción...${NC}"
echo ""

# Verificar si Git está inicializado
echo -e "${YELLOW}📂 Verificando repositorio Git:${NC}"
$SSH_CMD "cd $REMOTE_DIR && \
    if [ -d .git ]; then \
        echo '✅ Repositorio Git inicializado'; \
    else \
        echo '❌ Git NO está inicializado'; \
    fi"

echo ""

# Mostrar rama actual
echo -e "${YELLOW}🌿 Rama actual:${NC}"
$SSH_CMD "cd $REMOTE_DIR && git branch --show-current 2>/dev/null || echo 'N/A'"

echo ""

# Mostrar último commit
echo -e "${YELLOW}📝 Último commit:${NC}"
$SSH_CMD "cd $REMOTE_DIR && git log -1 --oneline 2>/dev/null || echo 'Sin commits'"

echo ""

# Mostrar estado
echo -e "${YELLOW}📊 Estado del repositorio:${NC}"
$SSH_CMD "cd $REMOTE_DIR && git status -s 2>/dev/null || echo 'Git no disponible'"

echo ""

# Mostrar configuración
echo -e "${YELLOW}⚙️  Configuración Git:${NC}"
$SSH_CMD "cd $REMOTE_DIR && \
    echo 'Usuario: '$(git config user.name 2>/dev/null || echo 'No configurado') && \
    echo 'Email: '$(git config user.email 2>/dev/null || echo 'No configurado')"

echo ""
echo -e "${GREEN}✅ Verificación completada${NC}"
