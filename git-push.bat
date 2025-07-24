@echo off
echo Fazendo push para o repositorio GitHub...
cd /d "D:\Etep\ETEPEAD\comm-manager"
git add .
git commit -m "Update: %date% %time%"
git push origin main
echo Push concluido!
pause