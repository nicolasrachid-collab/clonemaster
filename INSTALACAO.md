# 📦 Guia de Instalação - PEI

## Instalação do PHP no Windows

### Opção 1: XAMPP (Recomendado para iniciantes)

1. **Baixe o XAMPP:**
   - Acesse: https://www.apachefriends.org/download.html
   - Baixe a versão mais recente do XAMPP para Windows
   - Execute o instalador e siga as instruções

2. **Inicie o Apache:**
   - Abra o XAMPP Control Panel
   - Clique em "Start" ao lado de "Apache"

3. **Configure o projeto:**
   - Copie a pasta do projeto para: `C:\xampp\htdocs\`
   - Renomeie a pasta para: `pei` (ou outro nome de sua preferência)
   - Acesse: `http://localhost/pei/public/`

### Opção 2: PHP Standalone

1. **Baixe o PHP:**
   - Acesse: https://windows.php.net/download/
   - Baixe a versão "Thread Safe" em ZIP
   - Extraia para: `C:\php\`

2. **Configure o PATH:**
   - Abra "Variáveis de Ambiente" do Windows
   - Adicione `C:\php\` ao PATH do sistema
   - Reinicie o terminal

3. **Execute o projeto:**
   - Abra o terminal na raiz do projeto
   - Execute: `php -S localhost:8000 -t public`
   - Acesse: `http://localhost:8000`

### Opção 3: Laragon (Recomendado para desenvolvimento)

1. **Baixe o Laragon:**
   - Acesse: https://laragon.org/download/
   - Instale o Laragon (inclui PHP, Apache, MySQL)

2. **Configure o projeto:**
   - Copie a pasta do projeto para: `C:\laragon\www\`
   - Clique com botão direito no Laragon e escolha "Start All"
   - Acesse: `http://pei.test/public/` (ou o domínio configurado)

## Verificação da Instalação

Após instalar o PHP, verifique se está funcionando:

```bash
php -v
```

Você deve ver algo como:
```
PHP 8.x.x (cli) (built: ...)
```

## Executando o Projeto

### Método 1: Script Automático (Windows)
- Dê duplo clique em `start.bat`

### Método 2: Manual
- Abra o PowerShell/Terminal na raiz do projeto
- Execute: `php -S localhost:8000 -t public`
- Acesse: `http://localhost:8000`

## Solução de Problemas

### PHP não encontrado
- Verifique se o PHP está instalado
- Verifique se o PHP está no PATH do sistema
- Reinicie o terminal após adicionar ao PATH

### Porta 8000 já em uso
- Use outra porta: `php -S localhost:8080 -t public`
- Ou feche o programa que está usando a porta 8000

### Erro de permissão
- Execute o terminal como Administrador
- Verifique as permissões da pasta do projeto

## Próximos Passos

Após conseguir executar o projeto:
1. Acesse `http://localhost:8000`
2. Você verá o Dashboard do sistema
3. Navegue pelas diferentes páginas usando o menu lateral

