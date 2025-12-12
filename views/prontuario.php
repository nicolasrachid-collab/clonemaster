<?php
global $MOCK_PATIENTS, $MOCK_ENCOUNTERS, $MOCK_APPOINTMENTS;
$patient_id = $_GET['id'] ?? 1;
$patient = get_patient($patient_id);
$encounters = array_filter($MOCK_ENCOUNTERS, fn($e) => $e['patient_id'] == $patient_id);
$appointments = array_filter($MOCK_APPOINTMENTS, fn($a) => $a['patient_id'] == $patient_id);

if (!$patient) {
    echo '<div class="alert alert-danger">Paciente não encontrado.</div>';
    return;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/?page=patients" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0"><?= htmlspecialchars($patient['name']) ?></h4>
            <small class="text-muted">Prontuário Eletrônico</small>
        </div>
    </div>
    <a href="/?page=atendimento&patient_id=<?= $patient['id'] ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Novo Atendimento
    </a>
</div>

<div class="row g-4">
    <!-- Informações do Paciente -->
    <div class="col-lg-4">
        <div class="stat-card mb-3">
            <div class="text-center mb-3">
                <div class="patient-avatar mx-auto mb-2 <?= $patient['gender'] === 'M' ? 'bg-primary' : 'bg-danger' ?>" 
                     style="width:80px;height:80px;font-size:1.5rem;">
                    <?= substr($patient['name'], 0, 2) ?>
                </div>
                <h5 class="mb-1"><?= htmlspecialchars($patient['name']) ?></h5>
                <span class="badge bg-secondary"><?= $patient['insurance'] ?></span>
            </div>
            <hr>
            <div class="row g-2 small">
                <div class="col-6"><strong>CPF:</strong></div>
                <div class="col-6"><?= $patient['cpf'] ?></div>
                <div class="col-6"><strong>Nascimento:</strong></div>
                <div class="col-6"><?= format_date($patient['birth_date']) ?></div>
                <div class="col-6"><strong>Idade:</strong></div>
                <div class="col-6"><?= calculate_age($patient['birth_date']) ?> anos</div>
                <div class="col-6"><strong>Sexo:</strong></div>
                <div class="col-6"><?= $patient['gender'] === 'M' ? 'Masculino' : 'Feminino' ?></div>
                <div class="col-6"><strong>Tipo Sang.:</strong></div>
                <div class="col-6"><?= $patient['blood_type'] ?></div>
                <div class="col-6"><strong>Telefone:</strong></div>
                <div class="col-6"><?= $patient['phone'] ?></div>
            </div>
        </div>

        <!-- Alertas Médicos -->
        <div class="stat-card border-start border-4 border-danger mb-3">
            <h6 class="fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Alertas</h6>
            <div class="mt-2">
                <span class="badge bg-danger me-1"><i class="bi bi-capsule me-1"></i><?= $patient['allergies'] ?></span>
            </div>
        </div>

        <!-- Próximas Consultas -->
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-calendar-check me-2"></i>Próximos Agendamentos</h6>
            <?php 
            $future = array_filter($appointments, fn($a) => $a['date'] >= date('Y-m-d'));
            if (empty($future)): ?>
                <p class="text-muted mb-0">Nenhum agendamento futuro.</p>
            <?php else: foreach (array_slice($future, 0, 3) as $apt): ?>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-medium"><?= format_date($apt['date']) ?></div>
                        <small class="text-muted"><?= $apt['time'] ?> - <?= $apt['type'] ?></small>
                    </div>
                    <?= status_badge($apt['status']) ?>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Histórico de Atendimentos -->
    <div class="col-lg-8">
        <div class="stat-card">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#historico">
                        <i class="bi bi-clock-history me-1"></i>Histórico
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#prescricoes">
                        <i class="bi bi-capsule me-1"></i>Prescrições
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#exames">
                        <i class="bi bi-file-earmark-medical me-1"></i>Exames
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#anexos">
                        <i class="bi bi-paperclip me-1"></i>Anexos
                    </button>
                </li>
            </ul>
            <div class="tab-content p-3">
                <!-- Tab Histórico -->
                <div class="tab-pane fade show active" id="historico">
                    <?php if (empty($encounters)): ?>
                        <p class="text-muted">Nenhum atendimento registrado.</p>
                    <?php else: foreach ($encounters as $enc): 
                        $doctor = get_doctor($enc['doctor_id']);
                    ?>
                    <div class="card mb-3 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary"><?= format_date($enc['date']) ?></span>
                                    <small class="text-muted ms-2"><?= $doctor['name'] ?></small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                            <h6 class="fw-semibold"><?= htmlspecialchars($enc['chief_complaint']) ?></h6>
                            <p class="mb-2"><strong>Diagnóstico:</strong> <?= htmlspecialchars($enc['diagnosis']) ?></p>
                            <p class="mb-2"><strong>Prescrição:</strong> <?= htmlspecialchars($enc['prescription']) ?></p>
                            <p class="mb-0 text-muted small"><i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($enc['notes']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
                
                <!-- Tab Prescrições -->
                <div class="tab-pane fade" id="prescricoes">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>Data</th><th>Medicamento</th><th>Posologia</th><th>Ações</th></tr></thead>
                            <tbody>
                                <?php foreach ($encounters as $enc): ?>
                                <tr>
                                    <td><?= format_date($enc['date']) ?></td>
                                    <td><?= htmlspecialchars($enc['prescription']) ?></td>
                                    <td>Conforme prescrição</td>
                                    <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Exames -->
                <div class="tab-pane fade" id="exames">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                        <div>
                                            <div class="fw-medium">Hemograma Completo</div>
                                            <small class="text-muted">10/11/2025 - Lab. São Paulo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                        <div>
                                            <div class="fw-medium">Eletrocardiograma</div>
                                            <small class="text-muted">28/11/2025 - Cardiomed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Anexos -->
                <div class="tab-pane fade" id="anexos">
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cloud-upload fs-1"></i>
                        <p class="mt-2">Arraste arquivos aqui ou clique para enviar</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-upload me-2"></i>Selecionar Arquivos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
