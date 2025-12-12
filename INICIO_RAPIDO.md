# 🚀 Início Rápido - PEI

## ✅ Instalação Concluída!

O PHP 8.3.28 foi instalado com sucesso no seu sistema!

## Como Executar o Projeto

### Método 1: Script Automático (Recomendado)
1. Dê duplo clique no arquivo **`start.bat`**
2. O servidor será iniciado automaticamente
3. Acesse: **http://localhost:8000**

### Método 2: Manual (PowerShell/Terminal)
1. Abra o PowerShell ou Terminal na raiz do projeto
2. Execute o comando:
```powershell
php -S localhost:8000 -t public
```
3. Acesse: **http://localhost:8000**

## 📋 Verificação

Para verificar se o PHP está instalado corretamente, execute:
```powershell
php -v
```

Você deve ver:
```
PHP 8.3.28 (cli) (built: ...)
```

## 🌐 Acessando o Sistema

Após iniciar o servidor, acesse no navegador:
- **URL Principal:** http://localhost:8000
- **Dashboard:** http://localhost:8000?page=dashboard
- **Agenda:** http://localhost:8000?page=agenda
- **Pacientes:** http://localhost:8000?page=patients

## ⚠️ Notas Importantes

1. **Porta 8000:** Se a porta 8000 estiver em uso, você pode usar outra porta:
   ```powershell
   php -S localhost:8080 -t public
   ```

2. **Parar o Servidor:** Pressione `Ctrl+C` no terminal onde o servidor está rodando

3. **Dados Mockados:** O sistema utiliza dados de demonstração. Não há banco de dados configurado.

4. **Usuário Padrão:** O sistema já vem com um usuário logado automaticamente:
   - Nome: Dr. Evandro Ribeiro
   - Email: carlos@clinica.com
   - Especialidade: Clínico Geral

## 🆘 Solução de Problemas

### PHP não encontrado
Se aparecer erro "PHP não encontrado":
1. Feche e reabra o terminal
2. Ou reinicie o computador para atualizar o PATH

### Porta já em uso
Se a porta 8000 estiver ocupada:
- Use outra porta (8080, 3000, etc.)
- Ou feche o programa que está usando a porta

### Erro ao acessar
- Verifique se o servidor está rodando
- Verifique se digitou a URL correta
- Verifique se não há firewall bloqueando

## 📚 Documentação Adicional

- **README.md** - Documentação completa do projeto
- **INSTALACAO.md** - Guia detalhado de instalação

## 🎉 Pronto para Usar!

O sistema está configurado e pronto para uso. Basta executar o `start.bat` ou o comando manual e começar a usar!

