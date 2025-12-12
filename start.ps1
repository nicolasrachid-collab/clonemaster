# PEI - Prontuário Eletrônico Inteligente
# Script PowerShell para iniciar o servidor

# Navegar para o diretório do script
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $scriptPath

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  PEI - Prontuario Eletronico" -ForegroundColor Cyan
Write-Host "  Iniciando servidor PHP..." -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Atualizar PATH
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

# Verificar PHP
try {
    $phpVersion = php -v 2>&1 | Select-Object -First 1
    Write-Host "PHP encontrado: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "ERRO: PHP nao encontrado!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Por favor, instale o PHP ou adicione-o ao PATH do sistema."
    Write-Host "Veja o arquivo INSTALACAO.md para mais informacoes."
    Write-Host ""
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host ""
Write-Host "Diretorio: $scriptPath" -ForegroundColor Gray
Write-Host ""

# Verificar arquivos
if (-not (Test-Path "public\index.php")) {
    Write-Host "ERRO: Arquivo public\index.php nao encontrado!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Certifique-se de que esta executando o script na raiz do projeto."
    Write-Host ""
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "Arquivos encontrados!" -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Servidor iniciando..." -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Acesse: http://localhost:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "Pressione Ctrl+C para parar o servidor" -ForegroundColor Gray
Write-Host ""

# Iniciar servidor
php -S localhost:8000 -t public

