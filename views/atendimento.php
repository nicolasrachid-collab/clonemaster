<?php
global $MOCK_PATIENTS, $MOCK_CIDS, $MOCK_MEDICATIONS, $MOCK_ENCOUNTERS;
$patient_id = $_GET['patient_id'] ?? 3; // Default para paciente em atendimento
$patient = get_patient($patient_id);
$encounters = array_values(array_filter($MOCK_ENCOUNTERS, fn($e) => $e['patient_id'] == $patient_id));

if (!$patient) {
    echo '<div class="alert alert-danger">Selecione um paciente para iniciar o atendimento.</div>';
    return;
}
?>

<style>
.encounter-sidebar { background: #fff; border-radius: 12px; padding: 1rem; height: calc(100vh - 180px); overflow-y: auto; }
.encounter-main { background: #fff; border-radius: 12px; height: calc(100vh - 180px); overflow-y: auto; }
.section-card { background: #f8fafc; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
.section-card h6 { color: #1e293b; font-weight: 600; margin-bottom: 1rem; }
.quick-btn { padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 20px; }
.history-item { border-left: 3px solid #0d6efd; background: #fff; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 0 8px 8px 0; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <a href="/?page=agenda" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0">Atendimento em Andamento</h5>
            <small class="text-muted">Iniciado às <?= date('H:i') ?></small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Finalizar Atendimento</button>
    </div>
</div>

<div class="row g-3">
    <!-- Sidebar - Info Paciente -->
    <div class="col-lg-3">
        <div class="encounter-sidebar">
            <!-- Dados do Paciente -->
            <div class="text-center mb-3">
                <div class="patient-avatar mx-auto mb-2 <?= $patient['gender'] === 'M' ? 'bg-primary' : 'bg-danger' ?>" 
                     style="width:60px;height:60px;font-size:1.25rem;">
                    <?= substr($patient['name'], 0, 2) ?>
                </div>
                <h6 class="mb-0"><?= htmlspecialchars($patient['name']) ?></h6>
                <small class="text-muted"><?= calculate_age($patient['birth_date']) ?> anos • <?= $patient['gender'] ?></small>
            </div>
            
            <?php if ($patient['allergies'] !== 'Nenhuma'): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle me-1"></i><strong>Alergia:</strong> <?= $patient['allergies'] ?>
            </div>
            <?php endif; ?>

            <div class="small mb-3">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Convênio</span>
                    <span><?= $patient['insurance'] ?></span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Tipo Sang.</span>
                    <span><?= $patient['blood_type'] ?></span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Telefone</span>
                    <span><?= $patient['phone'] ?></span>
                </div>
            </div>

            <a href="/?page=prontuario&id=<?= $patient['id'] ?>" class="btn btn-outline-primary btn-sm w-100 mb-3">
                <i class="bi bi-folder2-open me-1"></i>Ver Prontuário Completo
            </a>

            <!-- Histórico Resumido -->
            <h6 class="fw-semibold mb-2"><i class="bi bi-clock-history me-1"></i>Últimos Atendimentos</h6>
            <?php foreach (array_slice($encounters, 0, 3) as $enc): ?>
            <div class="history-item small">
                <div class="fw-medium"><?= format_date($enc['date']) ?></div>
                <div class="text-muted"><?= htmlspecialchars(substr($enc['chief_complaint'], 0, 40)) ?>...</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main - Formulário de Atendimento -->
    <div class="col-lg-9">
        <div class="encounter-main p-3">
            <!-- Ações Rápidas -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="quick-btn btn btn-outline-primary"><i class="bi bi-chat-text me-1"></i>Anamnese</button>
                <button class="quick-btn btn btn-outline-primary"><i class="bi bi-heart-pulse me-1"></i>Sinais Vitais</button>
                <button class="quick-btn btn btn-outline-primary"><i class="bi bi-capsule me-1"></i>Prescrição</button>
                <button class="quick-btn btn btn-outline-primary"><i class="bi bi-file-medical me-1"></i>Exames</button>
                <button class="quick-btn btn btn-outline-primary"><i class="bi bi-file-text me-1"></i>Atestado</button>
                <button class="quick-btn btn btn-outline-info"><i class="bi bi-robot me-1"></i>IA Assistente</button>
            </div>

            <!-- Queixa Principal -->
            <div class="section-card">
                <h6><i class="bi bi-chat-square-text me-2"></i>Queixa Principal</h6>
                <textarea class="form-control" rows="2" placeholder="Descreva o motivo da consulta...">Paciente relata dores de cabeça frequentes há aproximadamente 2 semanas, com intensidade moderada a forte, principalmente no período da tarde.</textarea>
            </div>

            <div class="row g-3">
                <!-- Sinais Vitais -->
                <div class="col-md-6">
                    <div class="section-card">
                        <h6><i class="bi bi-heart-pulse me-2"></i>Sinais Vitais</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Pressão Arterial</label>
                                <input type="text" class="form-control form-control-sm" value="120/80 mmHg">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Freq. Cardíaca</label>
                                <input type="text" class="form-control form-control-sm" value="72 bpm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Temperatura</label>
                                <input type="text" class="form-control form-control-sm" value="36.5 °C">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Saturação</label>
                                <input type="text" class="form-control form-control-sm" value="98%">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exame Físico -->
                <div class="col-md-6">
                    <div class="section-card">
                        <h6><i class="bi bi-person-check me-2"></i>Exame Físico</h6>
                        <textarea class="form-control form-control-sm" rows="4" placeholder="Achados do exame físico...">Paciente em bom estado geral, lúcido e orientado. Ausculta cardíaca normal, sem sopros. Ausculta pulmonar limpa bilateralmente.</textarea>
                    </div>
                </div>
            </div>

            <!-- Diagnóstico -->
            <div class="section-card">
                <h6><i class="bi bi-search me-2"></i>Hipótese Diagnóstica (CID-10)</h6>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Buscar CID..." list="cidList" value="G44.2 - Cefaleia tensional">
                </div>
                <datalist id="cidList">
                    <?php foreach ($MOCK_CIDS as $code => $desc): ?>
                        <option value="<?= $code ?> - <?= $desc ?>">
                    <?php endforeach; ?>
                </datalist>
                <div class="mt-2">
                    <span class="badge bg-primary me-1">G44.2 - Cefaleia tensional <i class="bi bi-x"></i></span>
                </div>
            </div>

            <!-- Prescrição -->
            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="bi bi-capsule me-2"></i>Prescrição Médica</h6>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> Adicionar</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Medicamento</th><th>Posologia</th><th>Duração</th><th></th></tr></thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" value="Paracetamol 750mg" list="medList"></td>
                                <td><input type="text" class="form-control form-control-sm" value="1 comp. 8/8h"></td>
                                <td><input type="text" class="form-control form-control-sm" value="5 dias"></td>
                                <td><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <datalist id="medList">
                    <?php foreach ($MOCK_MEDICATIONS as $med): ?>
                        <option value="<?= $med ?>">
                    <?php endforeach; ?>
                </datalist>
                <div class="alert alert-info mt-2 py-2 small mb-0">
                    <i class="bi bi-lightbulb me-1"></i><strong>IA:</strong> Nenhuma interação medicamentosa detectada.
                </div>
            </div>

            <!-- Conduta -->
            <div class="section-card">
                <h6><i class="bi bi-clipboard-check me-2"></i>Conduta e Orientações</h6>
                <textarea class="form-control" rows="3" placeholder="Orientações ao paciente...">Orientado sobre técnicas de relaxamento e controle do estresse. Manter hidratação adequada. Retorno em 15 dias para reavaliação ou se piora do quadro.</textarea>
            </div>
        </div>
    </div>
</div>
