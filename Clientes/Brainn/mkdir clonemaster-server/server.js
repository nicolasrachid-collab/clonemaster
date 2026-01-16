/**
 * CLONEMASTER ENGINE V5 - STEALTH ROBOT (ANTI-BLOCKING)
 * 
 * Este servidor utiliza técnicas avançadas para evadir detecção de bots (Cloudflare/Vercel)
 * e interagir com sites Framer para carregar todo o conteúdo dinâmico.
 */

const express = require('express');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
const cors = require('cors');
const UserAgent = require('user-agents');
const fs = require('fs-extra');
const path = require('path');
const https = require('https');
const http = require('http');
const { URL } = require('url');
const cheerio = require('cheerio');
require('dotenv').config();

// Ativa modo furtivo para enganar detectores de bot
puppeteer.use(StealthPlugin());

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors({ origin: '*' }));
app.use(express.json());

// Servir arquivos estáticos da pasta public
app.use(express.static(path.join(__dirname, 'public')));

// Rota básica (mantida para compatibilidade)
app.get('/', (req, res) => {
  res.json({ 
    message: 'Clonemaster Server está rodando!',
    status: 'online',
    endpoint: 'POST /api/render',
    timestamp: new Date().toISOString()
  });
});

// --- ROBOT LOGIC ---

app.post('/api/render', async (req, res) => {
  const { url } = req.body;
  if (!url) return res.status(400).json({ error: 'URL required' });

  console.log(`[STEALTH] Iniciando infiltração: ${url}`);
  let browser = null;

  try {
    // 1. Gera User-Agent realista de Desktop
    const userAgent = new UserAgent({ deviceCategory: 'desktop' });
    
    browser = await puppeteer.launch({
      headless: "new",
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--window-size=1920,1080'
      ]
    });

    const page = await browser.newPage();
    
    // 2. Configurações de Evasão
    await page.setViewport({ width: 1920, height: 1080 });
    await page.setUserAgent(userAgent.toString());
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
      'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
      'Upgrade-Insecure-Requests': '1'
    });

    // 3. Navegação robusta com tratamento de timeout
    console.log('[STEALTH] Navegando...');
    await robustNavigate(page, url);

    // 4. FRAMER BREAKER: Simulação Humana
    // Sites Framer carregam assets baseados no viewport e movimento do mouse
    console.log('[STEALTH] Simulando interação humana...');
    
    // Move o mouse aleatoriamente para ativar hovers/estados
    await page.mouse.move(100, 100);
    await page.mouse.move(200, 200);
    await page.mouse.down();
    await page.mouse.up();

    // 5. Scroll "Orgânico" para carregar imagens Lazy-Load
    await smartScroll(page);

    // 6. Forçar carregamento de imagens de alta resolução (Framer Hack)
    await page.evaluate(() => {
        // Tenta encontrar imagens com srcset e força o browser a reconhecê-las
        const imgs = document.querySelectorAll('img');
        imgs.forEach(img => {
            if (img.loading === 'lazy') img.loading = 'eager';
            // Framer as vezes usa data-src
            if (img.dataset.src) img.src = img.dataset.src;
        });
        
        // Remove overlays de proteção se existirem (ex: modal de login simples)
        // const overlays = document.querySelectorAll('[class*="overlay"], [class*="modal"]');
        // overlays.forEach(o => o.style.display = 'none');
    });

    // Espera final para estabilização
    await new Promise(r => setTimeout(r, 3000));

    const html = await page.content();
    const title = await page.title();

    console.log(`[STEALTH] Sucesso! Capturado: ${title}`);
    res.json({ success: true, html, title });

  } catch (error) {
    console.error('[STEALTH] Falha:', error.message);
    res.status(500).json({ error: 'Falha na renderização furtiva', details: error.message });
  } finally {
    if (browser) await browser.close();
  }
});

// Função auxiliar para navegação robusta com tratamento de timeout
async function robustNavigate(page, url) {
  const maxAttempts = 3;
  const strategies = [
    { waitUntil: 'domcontentloaded', timeout: 60000 },
    { waitUntil: 'load', timeout: 90000 },
    { waitUntil: 'domcontentloaded', timeout: 120000 }
  ];

  for (let attempt = 0; attempt < maxAttempts; attempt++) {
    try {
      console.log(`[NAVIGATE] Tentativa ${attempt + 1}/${maxAttempts}...`);
      
      const strategy = strategies[attempt] || strategies[0];
      
      // Tenta navegar
      await page.goto(url, strategy);
      
      // Se chegou aqui, a navegação foi bem-sucedida
      console.log(`[NAVIGATE] ✅ Página carregada com sucesso (tentativa ${attempt + 1})`);
      
      // Aguarda um pouco para assets carregarem
      await new Promise(r => setTimeout(r, 3000));
      
      return true;
      
    } catch (error) {
      const isTimeout = error.message.includes('timeout') || 
                       error.message.includes('Navigation timeout') ||
                       error.message.includes('60000') ||
                       error.message.includes('exceeded');
      
      if (isTimeout && attempt < maxAttempts - 1) {
        console.warn(`[NAVIGATE] ⚠️ Timeout na tentativa ${attempt + 1}, verificando se página carregou...`);
        
        // Verifica se a página carregou mesmo com timeout
        try {
          const title = await page.title();
          const currentUrl = page.url();
          
          if (title && title !== '' && currentUrl && currentUrl !== 'about:blank') {
            console.log(`[NAVIGATE] ✅ Página carregada parcialmente (título: "${title.substring(0, 50)}..."), continuando...`);
            await new Promise(r => setTimeout(r, 2000));
            return true;
          }
        } catch (checkError) {
          console.log(`[NAVIGATE] Página não carregou, tentando novamente...`);
        }
        
        // Aguarda antes de tentar novamente
        await new Promise(r => setTimeout(r, 2000));
        continue;
      }
      
      // Se é a última tentativa ou erro não é timeout
      if (attempt === maxAttempts - 1) {
        console.warn(`[NAVIGATE] ⚠️ Última tentativa falhou, verificando conteúdo disponível...`);
        
        // Última tentativa: aceita qualquer conteúdo
        try {
          const title = await page.title();
          const currentUrl = page.url();
          const hasContent = await page.evaluate(() => {
            return document.body && document.body.innerHTML.length > 0;
          });
          
          if ((title && title !== '') || (currentUrl && currentUrl !== 'about:blank') || hasContent) {
            console.warn(`[NAVIGATE] ⚠️ Usando conteúdo parcialmente carregado`);
            return true;
          }
        } catch {}
        
        // Se não conseguiu nada, lança erro
        throw new Error(`Não foi possível carregar a página após ${maxAttempts} tentativas: ${error.message}`);
      }
      
      // Se não é timeout, lança o erro original
      if (!isTimeout) {
        throw error;
      }
    }
  }
  
  return false;
}

async function smartScroll(page) {
    await page.evaluate(async () => {
        await new Promise((resolve) => {
            let totalHeight = 0;
            const distance = 150; // Scroll menor para não pular seções
            let lastHeight = document.body.scrollHeight;
            let stableCount = 0;
            
            const timer = setInterval(() => {
                const scrollHeight = document.body.scrollHeight;
                window.scrollBy(0, distance);
                totalHeight += distance;
                
                // Verificar se a altura mudou (novo conteúdo carregado)
                if (scrollHeight === lastHeight) {
                    stableCount++;
                } else {
                    stableCount = 0;
                    lastHeight = scrollHeight;
                }

                // Parar se chegou ao fim E altura está estável por 3 iterações
                if((totalHeight >= scrollHeight - window.innerHeight && stableCount >= 3) || totalHeight > 30000){
                    clearInterval(timer);
                    // Scroll de volta ao topo
                    window.scrollTo(0, 0);
                    resolve();
                }
            }, 150); // Mais lento para dar tempo de carregar
        });
    });
    
    // Aguardar um pouco após scroll para elementos lazy-load carregarem
    await new Promise(r => setTimeout(r, 1000));
}

// ========== FUNÇÕES AUXILIARES PARA DOWNLOAD DE ASSETS ==========

// Função para normalizar URLs (relativas -> absolutas)
function normalizeUrl(url, baseUrl) {
  try {
    return new URL(url, baseUrl).href;
  } catch {
    return null;
  }
}

// Função para extrair extensão do arquivo
function getFileExtension(url) {
  try {
    const pathname = new URL(url).pathname;
    const match = pathname.match(/\.([^.?]+)(\?|$)/);
    return match ? match[1] : 'bin';
  } catch {
    return 'bin';
  }
}

// Função para gerar nome de arquivo seguro
function getSafeFileName(url, defaultExt = 'bin') {
  try {
    const urlObj = new URL(url);
    const pathname = urlObj.pathname;
    const filename = path.basename(pathname) || `file_${Date.now()}`;
    const ext = getFileExtension(url) || defaultExt;
    const name = filename.replace(/\.[^/.]+$/, '') || 'file';
    return `${name.replace(/[^a-z0-9]/gi, '_').substring(0, 100)}.${ext}`;
  } catch {
    return `file_${Date.now()}.${defaultExt}`;
  }
}

// Função para fazer download de arquivo
async function downloadFile(url, destPath) {
  return new Promise((resolve, reject) => {
    try {
      const protocol = url.startsWith('https:') ? https : http;
      
      const request = protocol.get(url, {
        headers: {
          'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
          'Accept': '*/*'
        },
        timeout: 30000
      }, (response) => {
        if (response.statusCode === 301 || response.statusCode === 302) {
          // Seguir redirect
          const redirectUrl = response.headers.location;
          const absoluteRedirect = normalizeUrl(redirectUrl, url);
          if (absoluteRedirect) {
            return downloadFile(absoluteRedirect, destPath)
              .then(resolve)
              .catch(reject);
          }
          return reject(new Error('Redirect inválido'));
        }
        
        if (response.statusCode !== 200) {
          return reject(new Error(`HTTP ${response.statusCode}`));
        }
        
        const fileStream = fs.createWriteStream(destPath);
        response.pipe(fileStream);
        
        fileStream.on('finish', () => {
          fileStream.close();
          resolve(destPath);
        });
        
        fileStream.on('error', (err) => {
          fs.unlink(destPath, () => {}); // Remove arquivo parcial
          reject(err);
        });
      });
      
      request.on('error', (err) => {
        reject(err);
      });
      
      request.on('timeout', () => {
        request.destroy();
        reject(new Error('Timeout após 30s'));
      });
      
      request.setTimeout(30000);
    } catch (error) {
      reject(error);
    }
  });
}

// Função para processar e baixar todos os assets
async function downloadAllAssets(page, baseUrl, cloneDir) {
  const assetsDir = {
    css: path.join(cloneDir, 'assets', 'css'),
    js: path.join(cloneDir, 'assets', 'js'),
    images: path.join(cloneDir, 'assets', 'images'),
    fonts: path.join(cloneDir, 'assets', 'fonts'),
    other: path.join(cloneDir, 'assets', 'other')
  };
  
  // Criar pastas
  for (const dir of Object.values(assetsDir)) {
    await fs.ensureDir(dir);
  }
  
  console.log('[DOWNLOAD] Extraindo assets da página...');
  
  // Extrair todos os assets
  const assets = await page.evaluate(() => {
    const result = {
      css: [],
      js: [],
      images: [],
      fonts: [],
      other: []
    };
    
    // CSS - links e também verificar se há CSS dinâmico
    document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
      if (link.href) result.css.push(link.href);
    });
    
    // Verificar CSS carregado dinamicamente via JavaScript
    // Sites Framer podem carregar CSS assim
    try {
      const sheets = document.styleSheets;
      for (let i = 0; i < sheets.length; i++) {
        try {
          const sheet = sheets[i];
          if (sheet.href && !result.css.includes(sheet.href)) {
            result.css.push(sheet.href);
          }
        } catch (e) {
          // Ignorar erros de CORS
        }
      }
    } catch (e) {
      // Ignorar erros
    }
    
    // Scripts
    document.querySelectorAll('script[src]').forEach(script => {
      if (script.src) result.js.push(script.src);
    });
    
    // Modulepreload links (módulos JavaScript do Framer - críticos!)
    document.querySelectorAll('link[rel="modulepreload"]').forEach(link => {
      if (link.href) result.js.push(link.href);
    });
    
    // Imagens - FORÇAR todas a aparecer primeiro
    // Scroll para carregar lazy images
    window.scrollTo(0, document.body.scrollHeight);
    await new Promise(r => setTimeout(r, 500));
    window.scrollTo(0, 0);
    await new Promise(r => setTimeout(r, 500));
    
    // Aguardar React renderizar novas imagens
    await new Promise(r => setTimeout(r, 1000));
    
    // Agora capturar todas as imagens
    document.querySelectorAll('img').forEach(img => {
      // Forçar carregamento de imagens lazy
      if (img.dataset.src && !img.src) {
        img.src = img.dataset.src;
      }
      // Forçar carregamento se tiver src vazio mas data-src
      if (!img.src && img.dataset.src) {
        img.src = img.dataset.src;
      }
      // Capturar src atual (mesmo que ainda não tenha carregado)
      if (img.src && !img.src.startsWith('data:') && !img.src.startsWith('blob:')) {
        result.images.push(img.src);
      }
      // Capturar srcset
      if (img.srcset) {
        img.srcset.split(',').forEach(src => {
          const url = src.trim().split(' ')[0];
          if (url && !url.startsWith('data:') && !url.startsWith('blob:')) {
            result.images.push(url);
          }
        });
      }
      // Capturar data-src também (pode ser a URL real)
      if (img.dataset.src && !img.dataset.src.startsWith('data:') && !img.dataset.src.startsWith('blob:')) {
        result.images.push(img.dataset.src);
      }
      // Capturar outras variações
      ['data-lazy-src', 'data-original', 'data-srcset'].forEach(attr => {
        if (img.getAttribute(attr)) {
          const url = img.getAttribute(attr);
          if (url && !url.startsWith('data:') && !url.startsWith('blob:')) {
            result.images.push(url);
          }
        }
      });
    });
    
    // Icons e favicons
    document.querySelectorAll('link[rel="icon"], link[rel="apple-touch-icon"]').forEach(link => {
      if (link.href) result.images.push(link.href);
    });
    
    // Background images do CSS inline e computed styles
    document.querySelectorAll('[style*="background-image"]').forEach(el => {
      const match = el.style.backgroundImage.match(/url\(['"]?([^'"]+)['"]?\)/);
      if (match && match[1]) result.images.push(match[1]);
    });
    
    // Background images de computed styles (mais abrangente)
    try {
      document.querySelectorAll('*').forEach(el => {
        try {
          const bg = window.getComputedStyle(el).backgroundImage;
          if (bg && bg !== 'none' && bg.includes('url(')) {
            const match = bg.match(/url\(['"]?([^'"]+)['"]?\)/);
            if (match && match[1] && !match[1].startsWith('data:')) {
              result.images.push(match[1]);
            }
          }
        } catch (e) {
          // Ignorar erros de CORS
        }
      });
    } catch (e) {
      // Ignorar erros
    }
    
    // Fonts
    document.querySelectorAll('link[rel*="font"], link[rel="preload"][as="font"]').forEach(link => {
      if (link.href) result.fonts.push(link.href);
    });
    
    return result;
  });
  
  console.log(`[DOWNLOAD] Assets encontrados: CSS=${assets.css.length}, JS=${assets.js.length}, Images=${assets.images.length}, Fonts=${assets.fonts.length}`);
  
  const urlMap = new Map(); // Mapeia URL original -> caminho local
  const stats = { css: 0, js: 0, images: 0, fonts: 0, other: 0, errors: 0 };
  
  // Função auxiliar para baixar um asset
  async function downloadAsset(url, category) {
    try {
      const normalizedUrl = normalizeUrl(url, baseUrl);
      if (!normalizedUrl) {
        console.log(`[DOWNLOAD] URL inválida ignorada: ${url.substring(0, 80)}`);
        return null;
      }
      
      if (normalizedUrl.startsWith('data:') || normalizedUrl.startsWith('blob:')) {
        return null;
      }
      
      // Evitar duplicatas
      if (urlMap.has(normalizedUrl)) {
        return urlMap.get(normalizedUrl);
      }
      
      const ext = getFileExtension(normalizedUrl);
      let destDir = assetsDir.other;
      
      if (category === 'css' || ext === 'css') destDir = assetsDir.css;
      else if (category === 'js' || ext === 'js' || ext === 'mjs') destDir = assetsDir.js;
      else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'avif'].includes(ext)) destDir = assetsDir.images;
      else if (['woff', 'woff2', 'ttf', 'otf', 'eot'].includes(ext)) destDir = assetsDir.fonts;
      
      const filename = getSafeFileName(normalizedUrl, ext);
      const destPath = path.join(destDir, filename);
      
      console.log(`[DOWNLOAD] Baixando ${category}: ${normalizedUrl.substring(0, 80)}...`);
      await downloadFile(normalizedUrl, destPath);
      const relativePath = path.relative(cloneDir, destPath).replace(/\\/g, '/');
      urlMap.set(normalizedUrl, relativePath);
      stats[category]++;
      console.log(`[DOWNLOAD] ✅ ${category} salvo: ${relativePath}`);
      
      return relativePath;
    } catch (error) {
      console.error(`[DOWNLOAD] ❌ Erro ao baixar ${url.substring(0, 80)}:`, error.message);
      stats.errors++;
      return null;
    }
  }
  
  // Baixar todos os assets em paralelo (com limite)
  const downloadPromises = [];
  
  // CSS - filtrar URLs válidas (não incluir marcadores de inline)
  for (const url of [...new Set(assets.css)]) {
    if (url && !url.startsWith('inline:') && !url.startsWith('data:') && !url.startsWith('blob:')) {
      downloadPromises.push(downloadAsset(url, 'css'));
    }
  }
  
  // JS
  for (const url of [...new Set(assets.js)]) {
    downloadPromises.push(downloadAsset(url, 'js'));
  }
  
  // Imagens
  for (const url of [...new Set(assets.images)]) {
    downloadPromises.push(downloadAsset(url, 'images'));
  }
  
  // Fonts
  for (const url of [...new Set(assets.fonts)]) {
    downloadPromises.push(downloadAsset(url, 'fonts'));
  }
  
  console.log(`[DOWNLOAD] Iniciando download de ${downloadPromises.length} assets...`);
  
  // Executar downloads em lotes de 10 para não sobrecarregar
  const batchSize = 10;
  for (let i = 0; i < downloadPromises.length; i += batchSize) {
    const batch = downloadPromises.slice(i, i + batchSize);
    await Promise.all(batch);
    console.log(`[DOWNLOAD] Progresso: ${Math.min(i + batchSize, downloadPromises.length)}/${downloadPromises.length}`);
  }
  
  console.log(`[DOWNLOAD] Concluído! Stats:`, stats);
  
  return { urlMap, stats };
}

// Extrair CSS inline e salvar como arquivo
async function extractInlineCSS(page, cloneDir, urlMap, stats) {
  const inlineCSS = await page.evaluate(() => {
    const styles = [];
    document.querySelectorAll('style').forEach(style => {
      if (style.textContent) {
        styles.push(style.textContent);
      }
    });
    return styles.join('\n\n');
  });
  
  if (inlineCSS.trim()) {
    const cssPath = path.join(cloneDir, 'assets', 'css', 'inline-styles.css');
    await fs.writeFile(cssPath, inlineCSS, 'utf8');
    console.log('[DOWNLOAD] CSS inline extraído e salvo');
    stats.css++; // Contar CSS inline nas estatísticas
  }
}

// Função para extrair URLs de assets de um CSS
function extractAssetsFromCSS(cssContent, cssUrl, baseUrl) {
  const assets = {
    fonts: [],
    images: [],
    cssImports: []
  };
  
  // Extrair @import
  const importMatches = cssContent.matchAll(/@import\s+['"]([^'"]+)['"]/g);
  for (const match of importMatches) {
    const importUrl = match[1];
    const normalizedUrl = normalizeUrl(importUrl, cssUrl || baseUrl);
    if (normalizedUrl && !normalizedUrl.startsWith('data:') && !normalizedUrl.startsWith('blob:')) {
      assets.cssImports.push(normalizedUrl);
    }
  }
  
  // Extrair todos os url() - fonts e images
  const urlMatches = cssContent.matchAll(/url\(['"]?([^'")]+)['"]?\)/g);
  for (const match of urlMatches) {
    const assetUrl = match[1].trim();
    if (assetUrl.startsWith('data:') || assetUrl.startsWith('blob:')) {
      continue;
    }
    
    const normalizedUrl = normalizeUrl(assetUrl, cssUrl || baseUrl);
    if (!normalizedUrl) continue;
    
    const ext = getFileExtension(normalizedUrl);
    
    // Classificar como font ou image
    if (['woff', 'woff2', 'ttf', 'otf', 'eot'].includes(ext)) {
      if (!assets.fonts.includes(normalizedUrl)) {
        assets.fonts.push(normalizedUrl);
      }
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'avif'].includes(ext)) {
      if (!assets.images.includes(normalizedUrl)) {
        assets.images.push(normalizedUrl);
      }
    }
  }
  
  return assets;
}

// Função para processar CSS e baixar assets referenciados
async function processCSS(cssContent, cssUrl, baseUrl, cloneDir, urlMap) {
  let processedCSS = cssContent;
  
  // Processar @import
  processedCSS = processedCSS.replace(/@import\s+['"]([^'"]+)['"]/g, (match, importUrl) => {
    const normalizedUrl = normalizeUrl(importUrl, cssUrl || baseUrl);
    if (normalizedUrl && urlMap.has(normalizedUrl)) {
      return `@import "${urlMap.get(normalizedUrl)}"`;
    }
    return match;
  });
  
  // Processar url() em CSS (fonts, images)
  processedCSS = processedCSS.replace(/url\(['"]?([^'")]+)['"]?\)/g, (match, assetUrl) => {
    if (assetUrl.startsWith('data:') || assetUrl.startsWith('blob:')) {
      return match;
    }
    
    const normalizedUrl = normalizeUrl(assetUrl, cssUrl || baseUrl);
    if (normalizedUrl && urlMap.has(normalizedUrl)) {
      return `url("${urlMap.get(normalizedUrl)}")`;
    }
    return match;
  });
  
  return processedCSS;
}

// Função para baixar assets adicionais encontrados em CSS
async function downloadCSSAssets(cssFiles, baseUrl, cloneDir, urlMap, stats) {
  const assetsDir = {
    css: path.join(cloneDir, 'assets', 'css'),
    images: path.join(cloneDir, 'assets', 'images'),
    fonts: path.join(cloneDir, 'assets', 'fonts')
  };
  
  const allAssets = {
    fonts: new Set(),
    images: new Set(),
    cssImports: new Set()
  };
  
  // Extrair assets de todos os CSS
  for (const [cssUrl, localPath] of cssFiles.entries()) {
    try {
      const cssPath = path.join(cloneDir, localPath);
      if (await fs.pathExists(cssPath)) {
        const cssContent = await fs.readFile(cssPath, 'utf8');
        const extracted = extractAssetsFromCSS(cssContent, cssUrl, baseUrl);
        
        extracted.fonts.forEach(f => allAssets.fonts.add(f));
        extracted.images.forEach(i => allAssets.images.add(i));
        extracted.cssImports.forEach(c => allAssets.cssImports.add(c));
      }
    } catch (error) {
      console.error(`[CSS-ASSETS] Erro ao processar CSS ${localPath}:`, error.message);
    }
  }
  
  // Baixar CSS @import
  for (const importUrl of allAssets.cssImports) {
    if (!urlMap.has(importUrl)) {
      try {
        const ext = getFileExtension(importUrl);
        const destDir = ext === 'css' ? assetsDir.css : path.join(cloneDir, 'assets', 'other');
        const filename = getSafeFileName(importUrl, ext);
        const destPath = path.join(destDir, filename);
        
        console.log(`[CSS-ASSETS] Baixando CSS @import: ${importUrl.substring(0, 80)}...`);
        await downloadFile(importUrl, destPath);
        const relativePath = path.relative(cloneDir, destPath).replace(/\\/g, '/');
        urlMap.set(importUrl, relativePath);
        stats.css++;
        console.log(`[CSS-ASSETS] ✅ CSS import salvo: ${relativePath}`);
      } catch (error) {
        console.error(`[CSS-ASSETS] ❌ Erro ao baixar CSS import:`, error.message);
        stats.errors++;
      }
    }
  }
  
  // Baixar fonts de @font-face
  for (const fontUrl of allAssets.fonts) {
    if (!urlMap.has(fontUrl)) {
      try {
        const filename = getSafeFileName(fontUrl, getFileExtension(fontUrl));
        const destPath = path.join(assetsDir.fonts, filename);
        
        console.log(`[CSS-ASSETS] Baixando font: ${fontUrl.substring(0, 80)}...`);
        await downloadFile(fontUrl, destPath);
        const relativePath = path.relative(cloneDir, destPath).replace(/\\/g, '/');
        urlMap.set(fontUrl, relativePath);
        stats.fonts++;
        console.log(`[CSS-ASSETS] ✅ Font salva: ${relativePath}`);
      } catch (error) {
        console.error(`[CSS-ASSETS] ❌ Erro ao baixar font:`, error.message);
        stats.errors++;
      }
    }
  }
  
  // Baixar imagens de background
  for (const imageUrl of allAssets.images) {
    if (!urlMap.has(imageUrl)) {
      try {
        const filename = getSafeFileName(imageUrl, getFileExtension(imageUrl));
        const destPath = path.join(assetsDir.images, filename);
        
        console.log(`[CSS-ASSETS] Baixando imagem de background: ${imageUrl.substring(0, 80)}...`);
        await downloadFile(imageUrl, destPath);
        const relativePath = path.relative(cloneDir, destPath).replace(/\\/g, '/');
        urlMap.set(imageUrl, relativePath);
        stats.images++;
        console.log(`[CSS-ASSETS] ✅ Imagem salva: ${relativePath}`);
      } catch (error) {
        console.error(`[CSS-ASSETS] ❌ Erro ao baixar imagem:`, error.message);
        stats.errors++;
      }
    }
  }
}

// Função para atualizar HTML com referências locais
function updateHTMLReferences(html, baseUrl, urlMap) {
  const $ = cheerio.load(html);
  
  // Atualizar CSS
  $('link[rel="stylesheet"]').each((i, el) => {
    const href = $(el).attr('href');
    if (href) {
      const normalizedUrl = normalizeUrl(href, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $(el).attr('href', urlMap.get(normalizedUrl));
      }
    }
  });
  
  // Atualizar scripts
  $('script[src]').each((i, el) => {
    const src = $(el).attr('src');
    if (src) {
      const normalizedUrl = normalizeUrl(src, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $(el).attr('src', urlMap.get(normalizedUrl));
      }
    }
  });
  
  // Atualizar modulepreload links (módulos JavaScript do Framer)
  $('link[rel="modulepreload"]').each((i, el) => {
    const href = $(el).attr('href');
    if (href) {
      const normalizedUrl = normalizeUrl(href, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $(el).attr('href', urlMap.get(normalizedUrl));
      }
    }
  });
  
  // Atualizar imagens
  $('img').each((i, el) => {
    const $img = $(el);
    
    // Atualizar src
    const src = $img.attr('src');
    if (src) {
      const normalizedUrl = normalizeUrl(src, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $img.attr('src', urlMap.get(normalizedUrl));
      }
    }
    
    // Atualizar data-src (usado por lazy loading)
    const dataSrc = $img.attr('data-src');
    if (dataSrc) {
      const normalizedUrl = normalizeUrl(dataSrc, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $img.attr('data-src', urlMap.get(normalizedUrl));
        // Se não tiver src, usar data-src como src
        if (!src || !src.trim()) {
          $img.attr('src', urlMap.get(normalizedUrl));
        }
      }
    }
    
    // Atualizar outros atributos de lazy loading
    ['data-lazy-src', 'data-original'].forEach(attr => {
      const attrValue = $img.attr(attr);
      if (attrValue) {
        const normalizedUrl = normalizeUrl(attrValue, baseUrl);
        if (normalizedUrl && urlMap.has(normalizedUrl)) {
          $img.attr(attr, urlMap.get(normalizedUrl));
        }
      }
    });
    
    // Atualizar srcset
    const srcset = $img.attr('srcset');
    if (srcset) {
      const newSrcset = srcset.split(',').map(src => {
        const [url, descriptor] = src.trim().split(' ');
        const normalizedUrl = normalizeUrl(url, baseUrl);
        if (normalizedUrl && urlMap.has(normalizedUrl)) {
          return `${urlMap.get(normalizedUrl)} ${descriptor || ''}`.trim();
        }
        return src.trim();
      }).join(', ');
      $img.attr('srcset', newSrcset);
    }
    
    // Atualizar data-srcset também
    const dataSrcset = $img.attr('data-srcset');
    if (dataSrcset) {
      const newDataSrcset = dataSrcset.split(',').map(src => {
        const [url, descriptor] = src.trim().split(' ');
        const normalizedUrl = normalizeUrl(url, baseUrl);
        if (normalizedUrl && urlMap.has(normalizedUrl)) {
          return `${urlMap.get(normalizedUrl)} ${descriptor || ''}`.trim();
        }
        return src.trim();
      }).join(', ');
      $img.attr('data-srcset', newDataSrcset);
    }
  });
  
  // Atualizar icons e favicons
  $('link[rel="icon"], link[rel="apple-touch-icon"]').each((i, el) => {
    const href = $(el).attr('href');
    if (href) {
      const normalizedUrl = normalizeUrl(href, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $(el).attr('href', urlMap.get(normalizedUrl));
      }
    }
  });
  
  // Atualizar fonts
  $('link[rel*="font"], link[rel="preload"][as="font"]').each((i, el) => {
    const href = $(el).attr('href');
    if (href) {
      const normalizedUrl = normalizeUrl(href, baseUrl);
      if (normalizedUrl && urlMap.has(normalizedUrl)) {
        $(el).attr('href', urlMap.get(normalizedUrl));
      }
    }
  });
  
  // Atualizar background images inline
  $('[style*="background-image"]').each((i, el) => {
    const style = $(el).attr('style');
    if (style) {
      const newStyle = style.replace(/url\(['"]?([^'")]+)['"]?\)/g, (match, assetUrl) => {
        if (assetUrl.startsWith('data:') || assetUrl.startsWith('blob:')) {
          return match;
        }
        const normalizedUrl = normalizeUrl(assetUrl, baseUrl);
        if (normalizedUrl && urlMap.has(normalizedUrl)) {
          return `url("${urlMap.get(normalizedUrl)}")`;
        }
        return match;
      });
      $(el).attr('style', newStyle);
    }
  });
  
  return $.html();
}

// --- ENDPOINTS AVANÇADOS ---

// 1. Salvar HTML em arquivo
app.post('/api/save', async (req, res) => {
  const { url, filename, html: providedHtml } = req.body;
  
  let html, title;
  
  try {
    // Se HTML foi fornecido, usa ele. Senão, renderiza a URL
    if (providedHtml) {
      html = providedHtml;
      title = 'saved_page';
    } else if (url) {
      // Renderiza o site usando a mesma lógica do /api/render
      const userAgent = new UserAgent({ deviceCategory: 'desktop' });
      const browser = await puppeteer.launch({
        headless: "new",
        args: [
          '--no-sandbox',
          '--disable-setuid-sandbox',
          '--disable-dev-shm-usage',
          '--disable-accelerated-2d-canvas',
          '--no-first-run',
          '--no-zygote',
          '--disable-gpu',
          '--window-size=1920,1080'
        ]
      });

      const page = await browser.newPage();
      await page.setViewport({ width: 1920, height: 1080 });
      await page.setUserAgent(userAgent.toString());
      await page.setExtraHTTPHeaders({
        'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Upgrade-Insecure-Requests': '1'
      });

      await robustNavigate(page, url);
      await page.mouse.move(100, 100);
      await page.mouse.move(200, 200);
      await smartScroll(page);
      await new Promise(r => setTimeout(r, 2000));

      html = await page.content();
      title = await page.title();
      await browser.close();
    } else {
      return res.status(400).json({ error: 'URL ou HTML required' });
    }
    
    // Cria pasta de output se não existir
    const outputDir = path.join(__dirname, 'output');
    await fs.ensureDir(outputDir);
    
    // Gera nome do arquivo
    const safeTitle = (filename || title || 'page').replace(/[^a-z0-9]/gi, '_').toLowerCase();
    const filePath = path.join(outputDir, `${safeTitle}.html`);
    
    // Salva o HTML
    await fs.writeFile(filePath, html, 'utf8');
    
    res.json({ 
      success: true, 
      message: 'HTML salvo com sucesso',
      file: filePath,
      size: html.length
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// 2. Extrair assets (CSS, imagens, scripts)
app.post('/api/extract', async (req, res) => {
  const { url } = req.body;
  if (!url) return res.status(400).json({ error: 'URL required' });

  console.log(`[EXTRACT] Extraindo assets de: ${url}`);
  let browser = null;

  try {
    const userAgent = new UserAgent({ deviceCategory: 'desktop' });
    
    browser = await puppeteer.launch({
      headless: "new",
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--window-size=1920,1080'
      ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });
    await page.setUserAgent(userAgent.toString());
    
    await robustNavigate(page, url);
    await smartScroll(page);
    await new Promise(r => setTimeout(r, 2000));

    // Extrai todos os assets
    const assets = await page.evaluate(() => {
      const result = {
        css: [],
        images: [],
        scripts: [],
        fonts: []
      };

      // CSS
      document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
        if (link.href) result.css.push(link.href);
      });

      // Imagens
      document.querySelectorAll('img').forEach(img => {
        if (img.src) result.images.push(img.src);
        if (img.srcset) result.images.push(...img.srcset.split(',').map(s => s.trim().split(' ')[0]));
      });

      // Scripts
      document.querySelectorAll('script[src]').forEach(script => {
        if (script.src) result.scripts.push(script.src);
      });

      // Fonts (de @font-face e link rel="preload")
      document.querySelectorAll('link[rel*="font"], link[rel="preload"][as="font"]').forEach(link => {
        if (link.href) result.fonts.push(link.href);
      });

      return result;
    });

    res.json({ success: true, assets, counts: {
      css: assets.css.length,
      images: assets.images.length,
      scripts: assets.scripts.length,
      fonts: assets.fonts.length
    }});

  } catch (error) {
    res.status(500).json({ error: error.message });
  } finally {
    if (browser) await browser.close();
  }
});

// 3. Clonar site completo (HTML + assets)
app.post('/api/clone', async (req, res) => {
  const { url, folderName } = req.body;
  if (!url) return res.status(400).json({ error: 'URL required' });

  console.log(`[CLONE] Iniciando clone completo de: ${url}`);
  let browser = null;

  try {
    // Renderiza o site
    const userAgent = new UserAgent({ deviceCategory: 'desktop' });
    browser = await puppeteer.launch({
      headless: "new",
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--window-size=1920,1080'
      ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });
    await page.setUserAgent(userAgent.toString());
    
    await robustNavigate(page, url);
    await smartScroll(page);
    await new Promise(r => setTimeout(r, 2000));

    const html = await page.content();
    const title = await page.title();

    // Cria estrutura de pastas
    const safeName = (folderName || title || 'cloned_site').replace(/[^a-z0-9]/gi, '_').toLowerCase();
    const cloneDir = path.join(__dirname, 'clones', safeName);
    await fs.ensureDir(cloneDir);
    await fs.ensureDir(path.join(cloneDir, 'assets'));
    await fs.ensureDir(path.join(cloneDir, 'assets', 'css'));
    await fs.ensureDir(path.join(cloneDir, 'assets', 'images'));
    await fs.ensureDir(path.join(cloneDir, 'assets', 'scripts'));

    // Salva HTML
    await fs.writeFile(path.join(cloneDir, 'index.html'), html, 'utf8');

    // Extrai assets
    const assets = await page.evaluate(() => {
      return {
        css: Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l => l.href).filter(Boolean),
        images: Array.from(document.querySelectorAll('img')).map(i => i.src || i.srcset).filter(Boolean).flat(),
        scripts: Array.from(document.querySelectorAll('script[src]')).map(s => s.src).filter(Boolean)
      };
    });

    res.json({ 
      success: true, 
      message: 'Site clonado com sucesso',
      folder: cloneDir,
      title,
      assets: {
        css: assets.css.length,
        images: assets.images.length,
        scripts: assets.scripts.length
      }
    });

  } catch (error) {
    res.status(500).json({ error: error.message });
  } finally {
    if (browser) await browser.close();
  }
});

// 4. Analisar estrutura do site
app.post('/api/analyze', async (req, res) => {
  const { url } = req.body;
  if (!url) return res.status(400).json({ error: 'URL required' });

  console.log(`[ANALYZE] Analisando estrutura de: ${url}`);
  let browser = null;

  try {
    const userAgent = new UserAgent({ deviceCategory: 'desktop' });
    browser = await puppeteer.launch({
      headless: "new",
      args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });
    await robustNavigate(page, url);
    await smartScroll(page);

    const analysis = await page.evaluate(() => {
      return {
        title: document.title,
        meta: {
          description: document.querySelector('meta[name="description"]')?.content || null,
          keywords: document.querySelector('meta[name="keywords"]')?.content || null,
          viewport: document.querySelector('meta[name="viewport"]')?.content || null
        },
        structure: {
          headings: {
            h1: document.querySelectorAll('h1').length,
            h2: document.querySelectorAll('h2').length,
            h3: document.querySelectorAll('h3').length
          },
          links: document.querySelectorAll('a').length,
          images: document.querySelectorAll('img').length,
          forms: document.querySelectorAll('form').length,
          sections: document.querySelectorAll('section').length
        },
        frameworks: {
          isFramer: !!document.querySelector('[class*="framer"]') || document.documentElement.innerHTML.includes('framer'),
          hasReact: !!window.React || !!document.querySelector('[data-reactroot]'),
          hasVue: !!window.Vue,
          hasJQuery: !!window.jQuery
        },
        performance: {
          scripts: document.querySelectorAll('script').length,
          stylesheets: document.querySelectorAll('link[rel="stylesheet"]').length,
          inlineStyles: document.querySelectorAll('style').length
        }
      };
    });

    res.json({ success: true, analysis });

  } catch (error) {
    res.status(500).json({ error: error.message });
  } finally {
    if (browser) await browser.close();
  }
});

// --- ENDPOINT AUTOMÁTICO COM ZIP ---

app.post('/api/clone-and-zip', async (req, res) => {
  const { url } = req.body;
  if (!url) return res.status(400).json({ error: 'URL required' });

  console.log(`[AUTO-CLONE] Iniciando processo automático: ${url}`);
  let browser = null;

  try {
    const userAgent = new UserAgent({ deviceCategory: 'desktop' });
    browser = await puppeteer.launch({
      headless: "new",
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu',
        '--window-size=1920,1080'
      ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });
    await page.setUserAgent(userAgent.toString());
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
      'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
      'Upgrade-Insecure-Requests': '1'
    });

    await robustNavigate(page, url);
    
    // Aguardar React/Framer carregar e começar a renderizar
    console.log('[AUTO-CLONE] Aguardando React/Framer carregar...');
    await new Promise(r => setTimeout(r, 3000));
    
    // Aguardar que o React termine de renderizar (verificar se há elementos renderizados)
    console.log('[AUTO-CLONE] Aguardando renderização do React...');
    try {
      await page.waitForFunction(() => {
        // Verificar se há conteúdo renderizado (não apenas o body vazio)
        const body = document.body;
        if (!body) return false;
        
        // Verificar se há elementos filhos significativos
        const children = body.children;
        if (children.length === 0) return false;
        
        // Verificar se há imagens ou elementos com conteúdo
        const hasImages = document.querySelectorAll('img').length > 0;
        const hasContent = body.innerText.trim().length > 100;
        const hasVisibleElements = Array.from(body.querySelectorAll('*')).some(el => {
          const style = window.getComputedStyle(el);
          return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
        });
        
        return hasImages || hasContent || hasVisibleElements;
      }, { timeout: 20000 });
    } catch (e) {
      console.log('[AUTO-CLONE] Timeout aguardando React, continuando...');
    }
    
    await page.mouse.move(100, 100);
    await page.mouse.move(200, 200);
    
    // Aguardar elementos críticos aparecerem (produtos, imagens, cards)
    console.log('[AUTO-CLONE] Aguardando elementos críticos...');
    try {
      // Aguardar múltiplos tipos de elementos
      await Promise.race([
        page.waitForSelector('img[src]:not([src=""])', { timeout: 10000 }),
        page.waitForSelector('[class*="product"]', { timeout: 10000 }),
        page.waitForSelector('[class*="card"]', { timeout: 10000 }),
        page.waitForSelector('[class*="hero"]', { timeout: 10000 }),
        page.waitForSelector('main, section, article', { timeout: 10000 })
      ]);
    } catch (e) {
      console.log('[AUTO-CLONE] Alguns elementos podem não ter aparecido, continuando...');
    }
    
    await smartScroll(page);
    
    // Aguardar network estar idle
    console.log('[AUTO-CLONE] Aguardando network idle...');
    try {
      // Monitorar requisições de rede
      let networkIdle = false;
      let pendingRequests = 0;
      
      page.on('request', () => {
        pendingRequests++;
        networkIdle = false;
      });
      
      page.on('response', () => {
        pendingRequests--;
        if (pendingRequests <= 0) {
          setTimeout(() => {
            if (pendingRequests <= 0) {
              networkIdle = true;
            }
          }, 1000);
        }
      });
      
      // Aguardar network idle (sem requisições por 2 segundos)
      await page.evaluate(() => {
        return new Promise((resolve) => {
          let idleCount = 0;
          const checkIdle = () => {
            if (document.readyState === 'complete') {
              idleCount++;
              if (idleCount >= 4) { // 2 segundos (4 x 500ms)
                resolve();
              } else {
                setTimeout(checkIdle, 500);
              }
            } else {
              idleCount = 0;
              setTimeout(checkIdle, 500);
            }
          };
          checkIdle();
        });
      });
    } catch (e) {
      console.log('[AUTO-CLONE] Network idle check falhou, aguardando tempo fixo...');
    }
    
    // Aguardar renderização do React estabilizar (elementos não mudam mais)
    console.log('[AUTO-CLONE] Aguardando renderização final do React...');
    try {
      await page.waitForFunction(() => {
        return new Promise((resolve) => {
          let lastElementCount = document.querySelectorAll('*').length;
          let stableCount = 0;
          
          const checkStable = () => {
            const currentCount = document.querySelectorAll('*').length;
            if (currentCount === lastElementCount) {
              stableCount++;
              if (stableCount >= 6) { // Estável por 3 segundos (6 x 500ms)
                resolve(true);
                return;
              }
            } else {
              stableCount = 0;
              lastElementCount = currentCount;
            }
            setTimeout(checkStable, 500);
          };
          
          checkStable();
          
          // Timeout após 10 segundos
          setTimeout(() => resolve(true), 10000);
        });
      }, { timeout: 15000 });
    } catch (e) {
      console.log('[AUTO-CLONE] Verificação de estabilidade falhou, aguardando tempo fixo...');
    }
    
    // Aguardar mais um pouco para garantir
    await new Promise(r => setTimeout(r, 3000));
    
    // Forçar carregamento de todas as imagens lazy-load
    await page.evaluate(() => {
      // Carregar todas as imagens lazy
      const imgs = document.querySelectorAll('img[loading="lazy"], img[data-src]');
      imgs.forEach(img => {
        if (img.dataset.src) {
          img.src = img.dataset.src;
          img.removeAttribute('loading');
        } else if (img.loading === 'lazy') {
          img.loading = 'eager';
        }
      });
      
      // Forçar renderização de elementos com background-image
      const elementsWithBg = document.querySelectorAll('[style*="background-image"]');
      elementsWithBg.forEach(el => {
        const bg = window.getComputedStyle(el).backgroundImage;
        if (bg && bg !== 'none') {
          // Forçar repaint
          el.style.display = 'none';
          el.offsetHeight; // Trigger reflow
          el.style.display = '';
        }
      });
    });
    
    // Aguardar mais um pouco após forçar carregamento
    await new Promise(r => setTimeout(r, 2000));
    
    // FORÇAR renderização completa do React e aguardar todas as imagens carregarem
    console.log('[AUTO-CLONE] Forçando renderização completa e aguardando imagens...');
    await page.evaluate(async () => {
      // Forçar todas as imagens a carregar
      const allImages = [];
      
      // Coletar todas as imagens (incluindo as que ainda não foram renderizadas)
      const imgElements = document.querySelectorAll('img');
      imgElements.forEach(img => {
        if (img.dataset.src) {
          img.src = img.dataset.src;
        }
        if (img.src && !img.src.startsWith('data:')) {
          allImages.push(img.src);
        }
      });
      
      // Aguardar todas as imagens carregarem
      const imagePromises = Array.from(imgElements).map(img => {
        return new Promise((resolve) => {
          if (img.complete) {
            resolve();
          } else {
            img.onload = () => resolve();
            img.onerror = () => resolve(); // Continuar mesmo se falhar
            // Timeout após 5 segundos
            setTimeout(() => resolve(), 5000);
          }
        });
      });
      
      await Promise.all(imagePromises);
      
      // Forçar scroll para carregar lazy images
      window.scrollTo(0, document.body.scrollHeight);
      await new Promise(r => setTimeout(r, 1000));
      window.scrollTo(0, 0);
      await new Promise(r => setTimeout(r, 500));
      
      // Aguardar mais um pouco para React renderizar
      await new Promise(r => setTimeout(r, 2000));
    });
    
    // Aguardar mais tempo para garantir
    await new Promise(r => setTimeout(r, 3000));

    const html = await page.content();
    const title = await page.title();
    const baseUrl = new URL(url).origin;

    // Cria estrutura de pastas
    const safeName = (title || 'cloned_site').replace(/[^a-z0-9]/gi, '_').toLowerCase();
    const cloneDir = path.join(__dirname, 'clones', safeName);
    await fs.ensureDir(cloneDir);
    
    console.log('[AUTO-CLONE] Baixando assets...');
    const { urlMap, stats } = await downloadAllAssets(page, baseUrl, cloneDir);
    
    console.log(`[AUTO-CLONE] URL Map size: ${urlMap.size}`);
    console.log(`[AUTO-CLONE] Stats:`, stats);
    
    if (urlMap.size === 0) {
      console.warn('[AUTO-CLONE] ⚠️ Nenhum asset foi baixado! Verifique os logs acima.');
    }
    
    console.log('[AUTO-CLONE] Extraindo CSS inline...');
    await extractInlineCSS(page, cloneDir, urlMap, stats);
    
    console.log('[AUTO-CLONE] Extraindo assets de CSS (fonts, imagens, @import)...');
    // Coletar todos os arquivos CSS baixados
    const cssFiles = new Map();
    for (const [originalUrl, localPath] of urlMap.entries()) {
      if (localPath.includes('/css/')) {
        cssFiles.set(originalUrl, localPath);
      }
    }
    
    // Baixar assets adicionais encontrados nos CSS
    await downloadCSSAssets(cssFiles, baseUrl, cloneDir, urlMap, stats);
    
    console.log('[AUTO-CLONE] Processando CSS e atualizando referências...');
    // Processar e salvar CSS com assets referenciados
    for (const [originalUrl, localPath] of urlMap.entries()) {
      if (localPath.includes('/css/')) {
        try {
          const cssPath = path.join(cloneDir, localPath);
          if (await fs.pathExists(cssPath)) {
            const cssContent = await fs.readFile(cssPath, 'utf8');
            const processedCSS = await processCSS(cssContent, originalUrl, baseUrl, cloneDir, urlMap);
            await fs.writeFile(cssPath, processedCSS, 'utf8');
          }
        } catch (error) {
          console.error(`[AUTO-CLONE] Erro ao processar CSS ${localPath}:`, error.message);
        }
      }
    }
    
    // Processar CSS inline também
    const inlineCssPath = path.join(cloneDir, 'assets', 'css', 'inline-styles.css');
    if (await fs.pathExists(inlineCssPath)) {
      try {
        const inlineCSS = await fs.readFile(inlineCssPath, 'utf8');
        const extracted = extractAssetsFromCSS(inlineCSS, baseUrl, baseUrl);
        
        // Baixar assets do CSS inline
        const inlineCssFiles = new Map();
        inlineCssFiles.set(baseUrl, 'assets/css/inline-styles.css');
        await downloadCSSAssets(inlineCssFiles, baseUrl, cloneDir, urlMap, stats);
        
        // Processar e atualizar CSS inline
        const processedInlineCSS = await processCSS(inlineCSS, baseUrl, baseUrl, cloneDir, urlMap);
        await fs.writeFile(inlineCssPath, processedInlineCSS, 'utf8');
      } catch (error) {
        console.error(`[AUTO-CLONE] Erro ao processar CSS inline:`, error.message);
      }
    }
    
    console.log('[AUTO-CLONE] Atualizando referências no HTML...');
    const updatedHTML = updateHTMLReferences(html, baseUrl, urlMap);
    await fs.writeFile(path.join(cloneDir, 'index.html'), updatedHTML, 'utf8');

    // Criar arquivo README com informações
    const readmeContent = `# Clone de ${title}

URL Original: ${url}
Data: ${new Date().toISOString()}

## Estatísticas de Download:
- CSS: ${stats.css} arquivos
- JavaScript: ${stats.js} arquivos
- Imagens: ${stats.images} arquivos
- Fonts: ${stats.fonts} arquivos
- Outros: ${stats.other} arquivos
- Erros: ${stats.errors} arquivos

## Como usar:
1. Extraia este arquivo ZIP
2. Abra o arquivo index.html no navegador
3. O site deve funcionar completamente offline!
`;
    await fs.writeFile(path.join(cloneDir, 'README.txt'), readmeContent, 'utf8');

    // Cria ZIP
    const archiver = require('archiver');
    const zipPath = path.join(__dirname, 'zips', `${safeName}.zip`);
    await fs.ensureDir(path.dirname(zipPath));

    return new Promise((resolve, reject) => {
      const output = fs.createWriteStream(zipPath);
      const archive = archiver('zip', { zlib: { level: 9 } });

      output.on('close', () => {
        console.log(`[AUTO-CLONE] ZIP criado: ${zipPath} (${archive.pointer()} bytes)`);
        res.json({
          success: true,
          message: 'Site clonado e compactado com sucesso',
          zipFile: `/api/download/${safeName}.zip`,
          zipPath: zipPath,
          title: title,
          size: archive.pointer(),
          stats: stats
        });
        resolve();
      });

      archive.on('error', (err) => {
        console.error('[AUTO-CLONE] Erro ao criar ZIP:', err);
        res.status(500).json({ error: 'Erro ao criar ZIP', details: err.message });
        reject(err);
      });

      archive.pipe(output);
      archive.directory(cloneDir, false);
      archive.finalize();
    });

  } catch (error) {
    console.error('[AUTO-CLONE] Erro:', error.message);
    res.status(500).json({ error: error.message });
  } finally {
    if (browser) await browser.close();
  }
});

// Endpoint para download do ZIP
app.get('/api/download/:filename', (req, res) => {
  const filename = req.params.filename;
  const zipPath = path.join(__dirname, 'zips', filename);
  
  if (fs.existsSync(zipPath)) {
    res.download(zipPath, filename, (err) => {
      if (err) {
        console.error('Erro ao fazer download:', err);
        res.status(500).json({ error: 'Erro ao fazer download' });
      }
    });
  } else {
    res.status(404).json({ error: 'Arquivo não encontrado' });
  }
});

// Lista de endpoints disponíveis
app.get('/api/endpoints', (req, res) => {
  res.json({
    endpoints: [
      { method: 'POST', path: '/api/render', description: 'Renderiza HTML completo de um site' },
      { method: 'POST', path: '/api/save', description: 'Salva HTML renderizado em arquivo' },
      { method: 'POST', path: '/api/extract', description: 'Extrai lista de assets (CSS, imagens, scripts)' },
      { method: 'POST', path: '/api/clone', description: 'Clona site completo com estrutura de pastas' },
      { method: 'POST', path: '/api/analyze', description: 'Analisa estrutura e metadados do site' },
      { method: 'POST', path: '/api/clone-and-zip', description: 'Clona site e gera ZIP automaticamente' },
      { method: 'GET', path: '/api/download/:filename', description: 'Download do arquivo ZIP' }
    ]
  });
});

app.listen(PORT, () => console.log(`
🕵️ CLONEMASTER STEALTH ROBOT ONLINE
----------------------------------
Porta: ${PORT}
Interface Web: http://localhost:${PORT}
Endpoints disponíveis:
  POST /api/render       - Renderizar HTML
  POST /api/save         - Salvar HTML em arquivo
  POST /api/extract      - Extrair assets
  POST /api/clone        - Clonar site completo
  POST /api/analyze      - Analisar estrutura
  POST /api/clone-and-zip - Clonar e gerar ZIP (automático)
  GET  /api/download/:filename - Download ZIP
  GET  /api/endpoints    - Listar endpoints
Status: Invisível e Aguardando...
`));