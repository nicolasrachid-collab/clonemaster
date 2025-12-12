<?php
global $MOCK_PATIENTS, $MOCK_ENCOUNTERS;
$search = $_GET['search'] ?? '';
$patients = $MOCK_PATIENTS;
if ($search) {
    $patients = array_filter($patients, fn($p) => 
        stripos($p['name'], $search) !== false || 
        stripos($p['cpf'], $search) !== false
    );
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Pacientes</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPatientModal">
        <i class="bi bi-person-plus me-2"></i>Novo Paciente
    </button>
</div>

<!-- Busca e Filtros -->
<div class="stat-card mb-4">
    <form class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar Paciente</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nome ou CPF..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Convênio</label>
            <select class="form-select">
                <option value="">Todos</option>
                <option>Unimed</option>
                <option>Bradesco Saúde</option>
                <option>SulAmérica</option>
                <option>Amil</option>
                <option>Particular</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-outline-primary w-100">
                <i class="bi bi-funnel me-2"></i>Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Lista de Pacientes -->
<div class="table-card">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><?= count($patients) ?> pacientes encontrados</h6>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary active"><i class="bi bi-list"></i></button>
            <button class="btn btn-outline-secondary"><i class="bi bi-grid-3x3"></i></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>CPF</th>
                    <th>Idade</th>
                    <th>Contato</th>
                    <th>Convênio</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $p): 
                    $encounters_count = count(array_filter($MOCK_ENCOUNTERS, fn($e) => $e['patient_id'] === $p['id']));
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="patient-avatar <?= $p['gender'] === 'M' ? 'bg-primary' : 'bg-danger' ?>">
                                <?= substr($p['name'], 0, 2) ?>
                            </div>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($p['name']) ?></div>
                                <small class="text-muted">
                                    <i class="bi bi-file-medical me-1"></i><?= $encounters_count ?> atendimentos
                                </small>
                            </div>
                        </div>
                    </td>
                    <td><code><?= $p['cpf'] ?></code></td>
                    <td>
                        <?= calculate_age($p['birth_date']) ?> anos
                        <div><small class="text-muted"><?= format_date($p['birth_date']) ?></small></div>
                    </td>
                    <td>
                        <div><i class="bi bi-telephone me-1"></i><?= $p['phone'] ?></div>
                        <small class="text-muted"><?= $p['email'] ?></small>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?= $p['insurance'] ?></span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="/?page=prontuario&id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Prontuário">
                                <i class="bi bi-file-medical"></i>
                            </a>
                            <a href="/?page=atendimento&patient_id=<?= $p['id'] ?>" class="btn btn-outline-success" title="Novo Atendimento">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                            <button class="btn btn-outline-secondary" title="Editar" data-bs-toggle="modal" data-bs-target="#patientModal<?= $p['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Novo Paciente -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Novo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CPF *</label>
                            <input type="text" class="form-control" placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data de Nascimento *</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sexo</label>
                            <select class="form-select">
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo Sanguíneo</label>
                            <select class="form-select">
                                <option>A+</option><option>A-</option>
                                <option>B+</option><option>B-</option>
                                <option>AB+</option><option>AB-</option>
                                <option>O+</option><option>O-</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alergias</label>
                            <input type="text" class="form-control" placeholder="Informe alergias conhecidas...">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Endereço</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Convênio</label>
                            <select class="form-select">
                                <option>Particular</option>
                                <option>Unimed</option>
                                <option>Bradesco Saúde</option>
                                <option>SulAmérica</option>
                                <option>Amil</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salvar Paciente</button>
            </div>
        </div>
    </div>
</div>
