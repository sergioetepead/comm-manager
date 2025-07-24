@echo off
echo === Deploy Automatizado do Sistema ===
echo.

echo [1/4] Building frontend...
cd frontend
call npm run build
if %ERRORLEVEL% neq 0 (
    echo Erro no build do frontend!
    exit /b 1
)
cd ..

echo [2/4] Restarting infra container...
cd ..\comm-manager-infra
docker-compose restart apache
if %ERRORLEVEL% neq 0 (
    echo Erro ao reiniciar container!
    exit /b 1
)

echo [3/4] Waiting for container...
timeout /t 5

echo [4/4] Testing deployment...
curl -s http://localhost:8080/admin/ > nul
if %ERRORLEVEL% neq 0 (
    echo Aviso: Interface pode nao estar respondendo ainda
)

echo.
echo === Deploy Concluido! ===
echo Interface: http://localhost:8080/admin/
echo API: http://localhost:8080/api/
echo.