<?php
global $MOCK_APPOINTMENTS, $MOCK_PATIENTS, $MOCK_USERS;
$today = date('Y-m-d');
$today_appointments = array_filter($MOCK_APPOINTMENTS, fn($a) => $a['date'] === $today);
$waiting_count = count(array_filter($today_appointments, fn($a) => $a['status'] === 'waiting'));
$in_progress = array_filter($today_appointments, fn($a) => $a['status'] === 'in_progress');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Bem-vindo, <?= htmlspecialchars(current_user()['name']) ?>!</h4>
        <p class="text-muted mb-0"><?= date('l, d \d\e F \d\e Y') ?></p>
    </div>
    <a href="/?page=atendimento" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Novo Atendimento
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p>Consultas Hoje</p>
                    <h3><?= count($today_appointments) ?></h3>
                </div>
                <div class="icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p>Aguardando</p>
                    <h3><?= $waiting_count ?></h3>
                </div>
                <div class="icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p>Pacientes Cadastrados</p>
                    <h3><?= count($MOCK_PATIENTS) ?></h3>
                </div>
                <div class="icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p>Telemedicina</p>
                    <h3><?= count(array_filter($today_appointments, fn($a) => $a['type'] === 'Telemedicina')) ?></h3>
                </div>
                <div class="icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-camera-video"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Próximas Consultas -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Próximas Consultas de Hoje</h6>
                <a href="/?page=agenda" class="btn btn-sm btn-outline-primary">Ver Agenda</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Horário</th>
                            <th>Paciente</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($today_appointments as $apt): 
                            $patient = get_patient($apt['patient_id']);
                        ?>
                        <tr>
                            <td><strong><?= format_time($apt['time']) ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="patient-avatar"><?= substr($patient['name'], 0, 2) ?></div>
                                    <div>
                                        <div class="fw-medium"><?= htmlspecialchars($patient['name']) ?></div>
                                        <small class="text-muted"><?= calculate_age($patient['birth_date']) ?> anos</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($apt['type'] === 'Telemedicina'): ?>
                                    <span class="badge bg-info"><i class="bi bi-camera-video me-1"></i><?= $apt['type'] ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($apt['type']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= status_badge($apt['status']) ?></td>
                            <td>
                                <a href="/?page=atendimento&patient_id=<?= $patient['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-play-fill"></i> Iniciar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Atalhos Rápidos -->
    <div class="col-lg-4">
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Ações Rápidas</h6>
            <div class="d-grid gap-2">
                <a href="/?page=patients" class="btn btn-outline-primary text-start">
                    <i class="bi bi-person-plus me-2"></i>Cadastrar Paciente
                </a>
                <a href="/?page=agenda" class="btn btn-outline-primary text-start">
                    <i class="bi bi-calendar-plus me-2"></i>Agendar Consulta
                </a>
                <a href="/?page=telemedicina" class="btn btn-outline-info text-start">
                    <i class="bi bi-camera-video me-2"></i>Iniciar Teleconsulta
                </a>
                <a href="/?page=reports" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-file-earmark-text me-2"></i>Gerar Relatório
                </a>
            </div>
        </div>

        <!-- Em Atendimento -->
        <?php if (!empty($in_progress)): $current = reset($in_progress); $p = get_patient($current['patient_id']); ?>
        <div class="stat-card border-start border-4 border-info">
            <h6 class="fw-semibold mb-3"><i class="bi bi-activity text-info me-2"></i>Em Atendimento</h6>
            <div class="d-flex align-items-center gap-3">
                <div class="patient-avatar bg-info" style="width:48px;height:48px;font-size:1rem;">
                    <?= substr($p['name'], 0, 2) ?>
                </div>
                <div>
                    <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
                    <small class="text-muted"><?= $current['notes'] ?></small>
                </div>
            </div>
            <a href="/?page=atendimento&patient_id=<?= $p['id'] ?>" class="btn btn-info w-100 mt-3">
                Continuar Atendimento
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
