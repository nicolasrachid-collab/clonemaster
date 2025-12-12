# Script para configurar Git no projeto PEI
# Execute este script no diretório do projeto

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Configurando Git para PEI" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se estamos no diretório correto
if (-not (Test-Path "public\index.php")) {
    Write-Host "ERRO: Execute este script no diretório do projeto!" -ForegroundColor Red
    Write-Host "O arquivo public\index.php nao foi encontrado." -ForegroundColor Red
    exit 1
}

Write-Host "Diretorio do projeto encontrado!" -ForegroundColor Green
Write-Host ""

# Remover repositório Git antigo se existir
if (Test-Path ".git") {
    Remove-Item -Recurse -Force ".git"
    Write-Host "Repositorio Git antigo removido" -ForegroundColor Yellow
}

# Inicializar novo repositório
git init
Write-Host "Repositorio Git inicializado" -ForegroundColor Green
Write-Host ""

# Adicionar arquivos
Write-Host "Adicionando arquivos..." -ForegroundColor Cyan
git add .
Write-Host "Arquivos adicionados" -ForegroundColor Green
Write-Host ""

# Criar commit inicial
Write-Host "Criando commit inicial..." -ForegroundColor Cyan
git commit -m "Initial commit: PEI - Prontuário Eletrônico Inteligente

- Sistema de prontuário eletrônico em PHP
- Dashboard, agenda, pacientes, prontuário
- Dados mockados para demonstração
- Documentação completa incluída"
Write-Host "Commit criado com sucesso!" -ForegroundColor Green
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Próximos Passos:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Crie um repositório no GitHub:" -ForegroundColor Yellow
Write-Host "   https://github.com/new" -ForegroundColor White
Write-Host ""
Write-Host "2. Execute os comandos:" -ForegroundColor Yellow
Write-Host "   git remote add origin https://github.com/SEU_USUARIO/prontuario-eletronico.git" -ForegroundColor White
Write-Host "   git branch -M main" -ForegroundColor White
Write-Host "   git push -u origin main" -ForegroundColor White
Write-Host ""
Write-Host "Ou veja o arquivo GITHUB_SETUP.md para instruções detalhadas" -ForegroundColor Cyan
Write-Host ""

