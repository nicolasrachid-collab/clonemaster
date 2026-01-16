/**
 * Servidor simples para servir clones localmente
 * Resolve problemas de CORS com módulos ES6 ao abrir arquivos via file://
 * 
 * Uso: node serve-clone.js [caminho_do_clone]
 * Exemplo: node serve-clone.js clones/velaa___ecommerce_template
 */

const express = require('express');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = 8080;

// Obter caminho do clone (argumento ou padrão)
const clonePath = process.argv[2] || path.join(__dirname, 'clones', 'velaa___ecommerce_template');

if (!fs.existsSync(clonePath)) {
  console.error(`❌ Erro: Pasta não encontrada: ${clonePath}`);
  process.exit(1);
}

// Servir arquivos estáticos
app.use(express.static(clonePath));

// Rota para index.html
app.get('/', (req, res) => {
  const indexPath = path.join(clonePath, 'index.html');
  if (fs.existsSync(indexPath)) {
    res.sendFile(indexPath);
  } else {
    res.status(404).send('index.html não encontrado');
  }
});

app.listen(PORT, () => {
  console.log(`
🚀 Servidor local iniciado!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📁 Servindo: ${clonePath}
🌐 URL: http://localhost:${PORT}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Pressione Ctrl+C para parar o servidor
  `);
});
