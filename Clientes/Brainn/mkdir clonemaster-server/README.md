# Clonemaster Server 🕵️

Servidor Node.js avançado para renderização e clonagem de sites Framer com técnicas anti-detecção.

## 🚀 Instalação

1. Instale as dependências:
```bash
npm install
```

## ▶️ Execução

### Modo desenvolvimento (com auto-reload):
```bash
npm run dev
```

### Modo produção:
```bash
npm start
```

O servidor estará disponível em: `http://localhost:3000`

## 📡 Endpoints Disponíveis

### 1. **POST /api/render** - Renderizar HTML
Renderiza o HTML completo de um site (incluindo conteúdo dinâmico).

```bash
POST http://localhost:3000/api/render
Content-Type: application/json

{
  "url": "https://exemplo.framer.website/"
}
```

**Resposta:**
```json
{
  "success": true,
  "title": "Título da Página",
  "html": "<!DOCTYPE html>..."
}
```

### 2. **POST /api/save** - Salvar HTML em arquivo
Renderiza e salva o HTML em um arquivo na pasta `output/`.

```bash
POST http://localhost:3000/api/save
Content-Type: application/json

{
  "url": "https://exemplo.framer.website/",
  "filename": "meu_site"  // opcional
}
```

### 3. **POST /api/extract** - Extrair Assets
Extrai lista de todos os assets (CSS, imagens, scripts, fonts).

```bash
POST http://localhost:3000/api/extract
Content-Type: application/json

{
  "url": "https://exemplo.framer.website/"
}
```

**Resposta:**
```json
{
  "success": true,
  "assets": {
    "css": ["url1", "url2"],
    "images": ["url1", "url2"],
    "scripts": ["url1"],
    "fonts": ["url1"]
  },
  "counts": {
    "css": 5,
    "images": 20,
    "scripts": 3,
    "fonts": 2
  }
}
```

### 4. **POST /api/clone** - Clonar Site Completo
Cria uma cópia completa do site com estrutura de pastas organizada.

```bash
POST http://localhost:3000/api/clone
Content-Type: application/json

{
  "url": "https://exemplo.framer.website/",
  "folderName": "meu_clone"  // opcional
}
```

**Estrutura criada:**
```
clones/
└── meu_clone/
    ├── index.html
    └── assets/
        ├── css/
        ├── images/
        └── scripts/
```

### 5. **POST /api/analyze** - Analisar Estrutura
Analisa a estrutura, metadados e frameworks do site.

```bash
POST http://localhost:3000/api/analyze
Content-Type: application/json

{
  "url": "https://exemplo.framer.website/"
}
```

**Resposta inclui:**
- Metadados (title, description, keywords)
- Estrutura (headings, links, imagens, forms)
- Frameworks detectados (Framer, React, Vue, jQuery)
- Performance (scripts, stylesheets)

### 6. **GET /api/endpoints** - Listar Endpoints
Retorna lista de todos os endpoints disponíveis.

## 🎯 Exemplos de Uso

### Renderizar e salvar um site:
```bash
curl -X POST http://localhost:3000/api/render \
  -H "Content-Type: application/json" \
  -d '{"url": "https://tatstudio.framer.website/"}'
```

### Clonar site completo:
```bash
curl -X POST http://localhost:3000/api/clone \
  -H "Content-Type: application/json" \
  -d '{"url": "https://tatstudio.framer.website/", "folderName": "tat_studio"}'
```

## 📁 Estrutura do Projeto

```
clonemaster-server/
├── server.js          # Servidor principal
├── package.json       # Dependências
├── .env              # Variáveis de ambiente
├── output/           # HTMLs salvos (gerado)
├── clones/           # Sites clonados (gerado)
└── README.md         # Este arquivo
```

## 🔧 Tecnologias

- **Express** - Servidor web
- **Puppeteer** - Automação de navegador
- **Puppeteer Stealth** - Evasão de detecção de bots
- **User-Agents** - Geração de user-agents realistas
- **fs-extra** - Manipulação de arquivos

## ⚠️ Notas

- O servidor usa técnicas avançadas para evadir detecção de bots
- Sites Framer são totalmente renderizados (incluindo conteúdo dinâmico)
- Todos os assets são extraídos e organizados automaticamente
