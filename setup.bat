@echo off
echo.
echo ==========================================
echo CONFIGURANDO PROYECTO...
echo ==========================================
echo.

echo 1. Instalando dependencias (composer install)...
call composer install

echo.
echo 2. Creando directorios en 'writable'...
if not exist "writable" mkdir "writable"
if not exist "writable\cache" mkdir "writable\cache"
if not exist "writable\logs" mkdir "writable\logs"
if not exist "writable\session" mkdir "writable\session"
if not exist "writable\uploads" mkdir "writable\uploads"
if not exist "writable\debugbar" mkdir "writable\debugbar"

echo.
if not exist ".env" (
    if exist "env" (
        echo 3. Creando archivo .env desde plantilla...
        copy env .env
        echo [!] Recorda editar el .env con tus credenciales de BD.
    ) else (
        echo [!] No se encontro el archivo 'env' para crear el '.env'.
    )
) else (
    echo 3. El archivo .env ya existe.
)

echo.
echo ==========================================
echo CONFIGURACION COMPLETADA!
echo ==========================================
pause
