const form = document.getElementById('cloneForm');
const submitBtn = document.getElementById('submitBtn');
const progressContainer = document.getElementById('progress');
const progressFill = document.getElementById('progressFill');
const progressText = document.getElementById('progressText');
const resultContainer = document.getElementById('result');
const errorContainer = document.getElementById('error');
const downloadBtn = document.getElementById('downloadBtn');
const resultMessage = document.getElementById('resultMessage');
const errorMessage = document.getElementById('errorMessage');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const url = document.getElementById('url').value;
    
    // Reset UI
    hideAll();
    showProgress();
    setProgress(10, 'Conectando ao servidor...');
    
    // Disable form
    submitBtn.disabled = true;
    submitBtn.querySelector('.btn-text').style.display = 'none';
    submitBtn.querySelector('.btn-loader').style.display = 'flex';
    
    try {
        setProgress(20, 'Renderizando site...');
        
        // Criar AbortController para timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 600000); // 10 minutos
        
        const response = await fetch('/api/clone-and-zip', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: 'Erro desconhecido' }));
            throw new Error(errorData.error || `Erro ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        setProgress(80, 'Criando arquivo ZIP...');
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        setProgress(100, 'Concluído!');
        
        setTimeout(() => {
            showResult(data);
        }, 500);
        
    } catch (error) {
        let errorMsg = 'Erro desconhecido';
        
        if (error.name === 'AbortError') {
            errorMsg = '⏱️ Tempo de espera esgotado (10 minutos). O site pode ser muito grande ou o servidor está lento. Tente novamente ou use um site menor.';
        } else if (error.message === 'Failed to fetch' || error.message.includes('fetch')) {
            errorMsg = '❌ Não foi possível conectar ao servidor. Verifique se o servidor está rodando em http://localhost:3000';
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        showError(errorMsg);
    } finally {
        submitBtn.disabled = false;
        submitBtn.querySelector('.btn-text').style.display = 'block';
        submitBtn.querySelector('.btn-loader').style.display = 'none';
    }
});

function setProgress(percent, text) {
    progressFill.style.width = percent + '%';
    progressText.textContent = text;
}

function showProgress() {
    hideAll();
    progressContainer.style.display = 'block';
}

function showResult(data) {
    hideAll();
    resultContainer.style.display = 'block';
    
    let message = `Site "${data.title}" clonado com sucesso!<br>Tamanho: ${formatBytes(data.size)}`;
    
    if (data.stats) {
        message += `<br><br><strong>Assets baixados:</strong><br>`;
        message += `📄 CSS: ${data.stats.css || 0} | `;
        message += `📜 JS: ${data.stats.js || 0} | `;
        message += `🖼️ Imagens: ${data.stats.images || 0} | `;
        message += `🔤 Fonts: ${data.stats.fonts || 0}`;
        if (data.stats.errors > 0) {
            message += `<br>⚠️ Erros: ${data.stats.errors}`;
        }
    }
    
    resultMessage.innerHTML = message;
    downloadBtn.href = data.zipFile;
    downloadBtn.download = `${data.title.replace(/[^a-z0-9]/gi, '_')}.zip`;
}

function showError(message) {
    hideAll();
    errorContainer.style.display = 'block';
    errorMessage.textContent = message;
}

function hideAll() {
    progressContainer.style.display = 'none';
    resultContainer.style.display = 'none';
    errorContainer.style.display = 'none';
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
