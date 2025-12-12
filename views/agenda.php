<?php
global $MOCK_APPOINTMENTS, $MOCK_USERS, $MOCK_SPECIALTIES;
$today = date('Y-m-d');
$selected_date = $_GET['date'] ?? $today;
$selected_doctor = $_GET['doctor'] ?? '';

// Filtrar agendamentos
$appointments = $MOCK_APPOINTMENTS;
if ($selected_date) {
    $appointments = array_filter($appointments, fn($a) => $a['date'] === $selected_date);
}
if ($selected_doctor) {
    $appointments = array_filter($appointments, fn($a) => $a['doctor_id'] == $selected_doctor);
}

// Gerar horários do dia (8h às 18h)
$time_slots = [];
for ($h = 8; $h <= 17; $h++) {
    $time_slots[] = sprintf('%02d:00', $h);
    $time_slots[] = sprintf('%02d:30', $h);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Agenda Médica</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
        <i class="bi bi-plus-lg me-2"></i>Novo Agendamento
    </button>
</div>

<!-- Filtros -->
<div class="stat-card mb-4">
    <form class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Data</label>
            <input type="date" name="date" class="form-control" value="<?= $selected_date ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Médico</label>
            <select name="doctor" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($MOCK_USERS as $doc): if ($doc['role'] !== 'medico') continue; ?>
                    <option value="<?= $doc['id'] ?>" <?= $selected_doctor == $doc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doc['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select">
                <option value="">Todos</option>
                <option>Confirmado</option>
                <option>Aguardando</option>
                <option>Em Atendimento</option>
                <option>Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-outline-primary w-100">
                <i class="bi bi-funnel me-2"></i>Filtrar
            </button>
        </div>
    </form>
</div>

<div class="row g-4">
    <!-- Visão de Lista/Timeline -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar-date me-2"></i>
                        <?= date('d/m/Y', strtotime($selected_date)) ?>
                        <?= $selected_date === $today ? '<span class="badge bg-primary ms-2">Hoje</span>' : '' ?>
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <a href="?page=agenda&date=<?= date('Y-m-d', strtotime($selected_date . ' -1 day')) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <a href="?page=agenda&date=<?= $today ?>" class="btn btn-outline-secondary">Hoje</a>
                        <a href="?page=agenda&date=<?= date('Y-m-d', strtotime($selected_date . ' +1 day')) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="p-3" style="max-height: 600px; overflow-y: auto;">
                <?php foreach ($time_slots as $slot): 
                    $slot_appointments = array_filter($appointments, fn($a) => $a['time'] === $slot);
                ?>
                <div class="d-flex gap-3 py-2 border-bottom align-items-start">
                    <div class="text-muted" style="width:60px; flex-shrink:0;">
                        <strong><?= $slot ?></strong>
                    </div>
                    <div class="flex-grow-1">
                        <?php if (empty($slot_appointments)): ?>
                            <div class="text-muted fst-italic py-2" style="border-left: 2px dashed #e2e8f0; padding-left: 1rem;">
                                Horário disponível
                            </div>
                        <?php else: foreach ($slot_appointments as $apt): 
                            $patient = get_patient($apt['patient_id']);
                            $doctor = get_doctor($apt['doctor_id']);
                            $bg_class = match($apt['status']) {
                                'in_progress' => 'border-info bg-info bg-opacity-10',
                                'waiting' => 'border-warning bg-warning bg-opacity-10',
                                'confirmed' => 'border-primary',
                                default => 'border-secondary'
                            };
                        ?>
                        <div class="card border-start border-3 <?= $bg_class ?> p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex gap-3">
                                    <div class="patient-avatar"><?= substr($patient['name'], 0, 2) ?></div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($patient['name']) ?></div>
                                        <small class="text-muted">
                                            <?= $apt['type'] ?> • <?= $doctor['name'] ?>
                                        </small>
                                        <div class="mt-1"><?= status_badge($apt['status']) ?></div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/?page=atendimento&patient_id=<?= $patient['id'] ?>">
                                            <i class="bi bi-play-fill me-2"></i>Iniciar Atendimento</a></li>
                                        <li><a class="dropdown-item" href="/?page=prontuario&id=<?= $patient['id'] ?>">
                                            <i class="bi bi-file-medical me-2"></i>Ver Prontuário</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#">
                                            <i class="bi bi-x-circle me-2"></i>Cancelar</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Painel Lateral -->
    <div class="col-lg-4">
        <!-- Mini Calendário -->
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-calendar-week me-2"></i>Calendário</h6>
            <div class="text-center text-muted mb-2">
                <strong><?= strftime('%B %Y', strtotime($selected_date)) ?></strong>
            </div>
            <div class="d-grid" style="grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center;">
                <?php foreach (['D','S','T','Q','Q','S','S'] as $d): ?>
                    <small class="text-muted fw-bold"><?= $d ?></small>
                <?php endforeach; ?>
                <?php 
                $first_day = date('w', strtotime(date('Y-m-01', strtotime($selected_date))));
                $days_in_month = date('t', strtotime($selected_date));
                for ($i = 0; $i < $first_day; $i++) echo '<span></span>';
                for ($d = 1; $d <= $days_in_month; $d++): 
                    $this_date = date('Y-m-', strtotime($selected_date)) . sprintf('%02d', $d);
                    $is_today = $this_date === $today;
                    $is_selected = $this_date === $selected_date;
                ?>
                    <a href="?page=agenda&date=<?= $this_date ?>" 
                       class="btn btn-sm <?= $is_selected ? 'btn-primary' : ($is_today ? 'btn-outline-primary' : 'btn-light') ?>">
                        <?= $d ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Resumo do Dia -->
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2"></i>Resumo do Dia</h6>
            <?php
            $stats = [
                'Confirmados' => count(array_filter($appointments, fn($a) => $a['status'] === 'confirmed')),
                'Aguardando' => count(array_filter($appointments, fn($a) => $a['status'] === 'waiting')),
                'Em Atendimento' => count(array_filter($appointments, fn($a) => $a['status'] === 'in_progress')),
                'Telemedicina' => count(array_filter($appointments, fn($a) => $a['type'] === 'Telemedicina')),
            ];
            foreach ($stats as $label => $count): ?>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span><?= $label ?></span>
                <strong><?= $count ?></strong>
            </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between py-2 fw-bold text-primary">
                <span>Total</span>
                <span><?= count($appointments) ?></span>
            </div>
        </div>
    </div>
</div>
