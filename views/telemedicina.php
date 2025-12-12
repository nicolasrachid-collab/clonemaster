<?php
global $MOCK_PATIENTS, $MOCK_APPOINTMENTS;
$today = date('Y-m-d');
$teleconsultas = array_filter($MOCK_APPOINTMENTS, fn($a) => $a['type'] === 'Telemedicina');
$waiting_patient = get_patient(6); // Paciente agendado para telemedicina
?>

<style>
.video-container { background: #1e293b; border-radius: 12px; aspect-ratio: 16/9; position: relative; overflow: hidden; }
.video-main { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #64748b; }
.video-pip { position: absolute; bottom: 1rem; right: 1rem; width: 180px; aspect-ratio: 16/9; background: #334155; border-radius: 8px; border: 2px solid #475569; }
.video-controls { position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; }
.video-controls .btn { width: 48px; height: 48px; border-radius: 50%; }
.call-info { position: absolute; top: 1rem; left: 1rem; background: rgba(0,0,0,0.5); padding: 0.5rem 1rem; border-radius: 8px; color: #fff; }
.call-info .timer { font-size: 1.25rem; font-weight: 600; }
.waiting-room-card { border-left: 4px solid #0dcaf0; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-camera-video-fill me-2"></i>Telemedicina</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTeleconsultaModal">
        <i class="bi bi-plus-lg me-1"></i>Nova Teleconsulta
    </button>
</div>

<div class="row g-4">
    <!-- Área de Vídeo -->
    <div class="col-lg-8">
        <div class="stat-card p-2">
            <div class="video-container">
                <div class="video-main">
                    <div class="text-center">
                        <i class="bi bi-camera-video-off" style="font-size: 4rem;"></i>
                        <p class="mt-2">Nenhuma chamada ativa</p>
                        <button class="btn btn-success btn-lg mt-2" onclick="startCall()">
                            <i class="bi bi-telephone-fill me-2"></i>Iniciar Chamada
                        </button>
                    </div>
                </div>
                <div class="video-pip" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <i class="bi bi-person-video2"></i>
                    </div>
                </div>
                <div class="call-info" style="display: none;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-danger"><i class="bi bi-record-fill me-1"></i>AO VIVO</span>
                        <span class="timer">00:00:00</span>
                    </div>
                </div>
                <div class="video-controls" style="display: none;">
                    <button class="btn btn-light"><i class="bi bi-mic-fill"></i></button>
                    <button class="btn btn-light"><i class="bi bi-camera-video-fill"></i></button>
                    <button class="btn btn-light"><i class="bi bi-display"></i></button>
                    <button class="btn btn-light"><i class="bi bi-chat-dots-fill"></i></button>
                    <button class="btn btn-danger" onclick="endCall()"><i class="bi bi-telephone-x-fill"></i></button>
                </div>
            </div>
        </div>

        <!-- Chat durante chamada -->
        <div class="stat-card mt-3" id="chatBox" style="display: none;">
            <h6 class="fw-semibold mb-3"><i class="bi bi-chat-dots me-2"></i>Chat</h6>
            <div style="height: 150px; overflow-y: auto; background: #f8fafc; border-radius: 8px; padding: 1rem;" id="chatMessages">
                <div class="mb-2"><strong class="text-primary">Você:</strong> Boa tarde! Como está se sentindo?</div>
                <div class="mb-2"><strong class="text-secondary">Paciente:</strong> Boa tarde doutor. Estou melhor, mas ainda com dores.</div>
            </div>
            <div class="input-group mt-2">
                <input type="text" class="form-control" placeholder="Digite sua mensagem...">
                <button class="btn btn-primary"><i class="bi bi-send"></i></button>
            </div>
        </div>
    </div>

    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Sala de Espera -->
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-hourglass-split me-2"></i>Sala de Espera Virtual</h6>
            
            <?php if ($waiting_patient): ?>
            <div class="card waiting-room-card mb-2">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="patient-avatar bg-info" style="width:36px;height:36px;">
                                <?= substr($waiting_patient['name'], 0, 2) ?>
                            </div>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($waiting_patient['name']) ?></div>
                                <small class="text-muted">Aguardando há 5 min</small>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="startCall()">
                            <i class="bi bi-telephone"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <p class="text-muted mb-0">Nenhum paciente aguardando.</p>
            <?php endif; ?>
        </div>

        <!-- Próximas Teleconsultas -->
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-calendar-check me-2"></i>Próximas Teleconsultas</h6>
            <?php foreach ($teleconsultas as $tc): 
                $p = get_patient($tc['patient_id']);
            ?>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <div class="fw-medium"><?= htmlspecialchars($p['name']) ?></div>
                    <small class="text-muted"><?= format_date($tc['date']) ?> às <?= $tc['time'] ?></small>
                </div>
                <?= status_badge($tc['status']) ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Acesso ao Prontuário -->
        <?php if ($waiting_patient): ?>
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-file-medical me-2"></i>Prontuário Rápido</h6>
            <div class="small">
                <div class="row mb-1">
                    <div class="col-5 text-muted">Paciente:</div>
                    <div class="col-7 fw-medium"><?= $waiting_patient['name'] ?></div>
                </div>
                <div class="row mb-1">
                    <div class="col-5 text-muted">Idade:</div>
                    <div class="col-7"><?= calculate_age($waiting_patient['birth_date']) ?> anos</div>
                </div>
                <div class="row mb-1">
                    <div class="col-5 text-muted">Alergias:</div>
                    <div class="col-7"><?= $waiting_patient['allergies'] ?></div>
                </div>
                <div class="row mb-1">
                    <div class="col-5 text-muted">Convênio:</div>
                    <div class="col-7"><?= $waiting_patient['insurance'] ?></div>
                </div>
            </div>
            <a href="/?page=prontuario&id=<?= $waiting_patient['id'] ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">
                Ver Prontuário Completo
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function startCall() {
    document.querySelector('.video-main').innerHTML = '<div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a5f,#0d1b2a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:3rem;"><i class="bi bi-person-video3"></i></div>';
    document.querySelector('.video-pip').style.display = 'block';
    document.querySelector('.call-info').style.display = 'block';
    document.querySelector('.video-controls').style.display = 'flex';
    document.getElementById('chatBox').style.display = 'block';
    // Timer simulado
    let s = 0;
    setInterval(() => { s++; document.querySelector('.timer').textContent = new Date(s*1000).toISOString().substr(11,8); }, 1000);
}
function endCall() { location.reload(); }
</script>
