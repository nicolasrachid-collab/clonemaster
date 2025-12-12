# PEI - Prontuário Eletrônico Inteligente

Sistema de Prontuário Eletrônico desenvolvido em PHP para gestão de pacientes, agendamentos e atendimentos médicos.

## 📋 Requisitos

- PHP 7.4 ou superior
- Servidor web (Apache, Nginx) ou PHP Built-in Server
- Navegador web moderno

## 🚀 Como Executar

### Opção 1: PHP Built-in Server (Recomendado para desenvolvimento)

**Windows:**
- Execute o arquivo `start.bat` ou
- Abra o PowerShell/Terminal na raiz do projeto e execute:
```bash
php -S localhost:8000 -t public
```

**Linux/Mac:**
- Execute o arquivo `start.sh` ou
- Abra o terminal na raiz do projeto e execute:
```bash
php -S localhost:8000 -t public
```

3. Acesse no navegador: `http://localhost:8000`

### Opção 2: Apache/Nginx

1. Configure o servidor web para apontar o DocumentRoot para a pasta `public`
2. Certifique-se de que o módulo `mod_rewrite` está habilitado (Apache)
3. Acesse através do domínio configurado

## 📁 Estrutura do Projeto

```
.
├── public/          # Arquivos públicos (ponto de entrada)
│   └── index.php   # Router principal
├── src/            # Código fonte
│   └── mock_data.php
└── views/          # Templates/Páginas
    ├── layout.php
    ├── dashboard.php
    ├── agenda.php
    ├── patients.php
    └── ...
```

## 🎯 Funcionalidades

- ✅ Dashboard com estatísticas
- ✅ Gestão de pacientes
- ✅ Agenda de consultas
- ✅ Prontuário eletrônico
- ✅ Atendimentos
- ✅ Formulários personalizados
- ✅ Relatórios
- ✅ Telemedicina
- ✅ Configurações

## 🔧 Configuração

O projeto utiliza dados mockados para demonstração. Os dados estão em `src/mock_data.php`.

## 📝 Notas

- Este é um sistema de demonstração com dados mockados
- Não há banco de dados configurado
- Todas as sessões são iniciadas automaticamente
- Usuário padrão: Dr. Evandro Ribeiro (logado automaticamente)

## 🌐 Navegação

- Dashboard: `?page=dashboard`
- Agenda: `?page=agenda`
- Pacientes: `?page=patients`
- Prontuário: `?page=prontuario`
- Atendimento: `?page=atendimento`
- Formulários: `?page=forms`
- Relatórios: `?page=reports`
- Telemedicina: `?page=telemedicina`
- Configurações: `?page=settings`

## 📄 Licença

Este projeto é uma demonstração do sistema PEI.

