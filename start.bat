@echo off
REM Navegar para o diretorio do script
cd /d "%~dp0"

echo ========================================
echo   PEI - Prontuario Eletronico
echo   Iniciando servidor PHP...
echo ========================================
echo.

REM Atualizar PATH para incluir PHP
set "PATH=%PATH%;%ProgramFiles%\PHP"

REM Verificar se PHP está instalado
php -v >nul 2>&1
if errorlevel 1 (
    echo ERRO: PHP nao encontrado!
    echo.
    echo Por favor, instale o PHP ou adicione-o ao PATH do sistema.
    echo Veja o arquivo INSTALACAO.md para mais informacoes.
    echo.
    pause
    exit /b 1
)

echo PHP encontrado!
echo.
echo Diretorio: %CD%
echo.
echo Verificando arquivos...
if not exist "public\index.php" (
    echo ERRO: Arquivo public\index.php nao encontrado!
    echo.
    echo Certifique-se de que esta executando o script na raiz do projeto.
    echo.
    pause
    exit /b 1
)

echo Arquivos encontrados!
echo.
echo ========================================
echo   Servidor iniciando...
echo ========================================
echo.
echo Acesse: http://localhost:8000
echo.
echo Pressione Ctrl+C para parar o servidor
echo.
php -S localhost:8000 -t public
pause

