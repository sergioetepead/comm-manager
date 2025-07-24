#!/bin/bash
echo "=== Deploy Automatizado do Sistema ==="
echo

echo "[1/4] Building frontend..."
cd frontend
npm run build
if [ $? -ne 0 ]; then
    echo "Erro no build do frontend!"
    exit 1
fi
cd ..

echo "[2/4] Restarting infra container..."
cd ../comm-manager-infra
docker-compose restart apache
if [ $? -ne 0 ]; then
    echo "Erro ao reiniciar container!"
    exit 1
fi

echo "[3/4] Waiting for container..."
sleep 5

echo "[4/4] Testing deployment..."
curl -s http://localhost:8080/admin/ > /dev/null
if [ $? -ne 0 ]; then
    echo "Aviso: Interface pode não estar respondendo ainda"
fi

echo
echo "=== Deploy Concluído! ==="
echo "Interface: http://localhost:8080/admin/"
echo "API: http://localhost:8080/api/"
echo