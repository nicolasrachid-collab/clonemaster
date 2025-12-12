<?php
global $MOCK_FORMS, $MOCK_SPECIALTIES;
?>

<style>
.form-builder { display: flex; gap: 1rem; height: calc(100vh - 200px); }
.field-palette { width: 250px; background: #fff; border-radius: 12px; padding: 1rem; overflow-y: auto; }
.form-canvas { flex: 1; background: #fff; border-radius: 12px; padding: 1rem; overflow-y: auto; }
.palette-item { background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.5rem; cursor: grab; transition: all 0.2s; }
.palette-item:hover { border-color: #0d6efd; background: #e0f2fe; }
.palette-item i { width: 24px; }
.canvas-field { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; position: relative; }
.canvas-field:hover { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,0.1); }
.canvas-field .field-actions { position: absolute; top: 0.5rem; right: 0.5rem; opacity: 0; transition: opacity 0.2s; }
.canvas-field:hover .field-actions { opacity: 1; }
.drop-zone { border: 2px dashed #cbd5e1; border-radius: 8px; padding: 2rem; text-align: center; color: #64748b; }
.drop-zone.drag-over { border-color: #0d6efd; background: #e0f2fe; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-ui-checks-grid me-2"></i>Construtor de Formulários</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>Pré-visualizar</button>
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar Formulário</button>
    </div>
</div>

<!-- Formulários Existentes -->
<div class="stat-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-semibold mb-0">Formulários Salvos</h6>
        <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('formBuilder').style.display='flex'">
            <i class="bi bi-plus me-1"></i>Novo Formulário
        </button>
    </div>
    <div class="row g-3">
        <?php foreach ($MOCK_FORMS as $form): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title"><?= htmlspecialchars($form['name']) ?></h6>
                    <p class="card-text small text-muted">
                        <i class="bi bi-tag me-1"></i><?= $form['specialty'] ?><br>
                        <i class="bi bi-list-check me-1"></i><?= $form['fields_count'] ?> campos
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-copy"></i></button>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Form Builder -->
<div id="formBuilder" class="form-builder" style="display: flex;">
    <!-- Paleta de Campos -->
    <div class="field-palette">
        <h6 class="fw-semibold mb-3"><i class="bi bi-tools me-2"></i>Campos Disponíveis</h6>
        
        <div class="mb-3">
            <label class="form-label small">Nome do Formulário</label>
            <input type="text" class="form-control form-control-sm" value="Anamnese Cardiológica">
        </div>
        <div class="mb-3">
            <label class="form-label small">Especialidade</label>
            <select class="form-select form-select-sm">
                <?php foreach ($MOCK_SPECIALTIES as $spec): ?>
                    <option><?= $spec ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <hr>
        <p class="small text-muted mb-2">Arraste para o formulário:</p>
        
        <div class="palette-item" draggable="true"><i class="bi bi-input-cursor-text me-2"></i>Texto Curto</div>
        <div class="palette-item" draggable="true"><i class="bi bi-textarea-t me-2"></i>Texto Longo</div>
        <div class="palette-item" draggable="true"><i class="bi bi-123 me-2"></i>Número</div>
        <div class="palette-item" draggable="true"><i class="bi bi-calendar me-2"></i>Data</div>
        <div class="palette-item" draggable="true"><i class="bi bi-ui-radios me-2"></i>Múltipla Escolha</div>
        <div class="palette-item" draggable="true"><i class="bi bi-check2-square me-2"></i>Checkbox</div>
        <div class="palette-item" draggable="true"><i class="bi bi-menu-button-wide me-2"></i>Seleção</div>
        <div class="palette-item" draggable="true"><i class="bi bi-sliders me-2"></i>Escala (1-10)</div>
        <div class="palette-item" draggable="true"><i class="bi bi-upload me-2"></i>Upload Arquivo</div>
        <div class="palette-item" draggable="true"><i class="bi bi-hr me-2"></i>Separador</div>
    </div>

    <!-- Canvas do Formulário -->
    <div class="form-canvas">
        <h6 class="fw-semibold mb-3"><i class="bi bi-file-earmark-text me-2"></i>Estrutura do Formulário</h6>
        
        <!-- Campos já adicionados (simulados) -->
        <div class="canvas-field">
            <div class="field-actions">
                <button class="btn btn-sm btn-light"><i class="bi bi-arrows-move"></i></button>
                <button class="btn btn-sm btn-light"><i class="bi bi-gear"></i></button>
                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
            </div>
            <label class="form-label fw-medium">Queixa Principal <span class="text-danger">*</span></label>
            <textarea class="form-control" rows="2" disabled placeholder="Campo de texto longo"></textarea>
            <small class="text-muted">Tipo: Texto Longo | Obrigatório</small>
        </div>

        <div class="canvas-field">
            <div class="field-actions">
                <button class="btn btn-sm btn-light"><i class="bi bi-arrows-move"></i></button>
                <button class="btn btn-sm btn-light"><i class="bi bi-gear"></i></button>
                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
            </div>
            <label class="form-label fw-medium">Histórico de Doenças Cardíacas na Família?</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" disabled><label class="form-check-label">Sim</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" disabled><label class="form-check-label">Não</label>
                </div>
            </div>
            <small class="text-muted">Tipo: Múltipla Escolha</small>
        </div>

        <div class="canvas-field">
            <div class="field-actions">
                <button class="btn btn-sm btn-light"><i class="bi bi-arrows-move"></i></button>
                <button class="btn btn-sm btn-light"><i class="bi bi-gear"></i></button>
                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
            </div>
            <label class="form-label fw-medium">Nível de Dor (1-10)</label>
            <input type="range" class="form-range" min="1" max="10" disabled>
            <small class="text-muted">Tipo: Escala</small>
        </div>

        <div class="canvas-field">
            <div class="field-actions">
                <button class="btn btn-sm btn-light"><i class="bi bi-arrows-move"></i></button>
                <button class="btn btn-sm btn-light"><i class="bi bi-gear"></i></button>
                <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
            </div>
            <label class="form-label fw-medium">Medicamentos em Uso</label>
            <div class="form-check"><input class="form-check-input" type="checkbox" disabled><label class="form-check-label">AAS</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" disabled><label class="form-check-label">Losartana</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" disabled><label class="form-check-label">Sinvastatina</label></div>
            <small class="text-muted">Tipo: Checkbox</small>
        </div>

        <!-- Drop Zone -->
        <div class="drop-zone" id="dropZone">
            <i class="bi bi-plus-circle fs-1 mb-2 d-block"></i>
            Arraste um campo aqui para adicionar
        </div>
    </div>
</div>

<script>
// Simular drag and drop
document.querySelectorAll('.palette-item').forEach(item => {
    item.addEventListener('dragstart', e => e.dataTransfer.setData('text', e.target.innerText));
});
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('drag-over'); alert('Campo adicionado: ' + e.dataTransfer.getData('text')); });
</script>
