<?php
global $MOCK_USERS, $MOCK_SPECIALTIES;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Relatórios e Analytics</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="bi bi-filetype-pdf me-1"></i>Exportar PDF</button>
        <button class="btn btn-outline-success"><i class="bi bi-filetype-xlsx me-1"></i>Exportar Excel</button>
    </div>
</div>

<!-- Filtros -->
<div class="stat-card mb-4">
    <form class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Período</label>
            <select class="form-select">
                <option>Últimos 7 dias</option>
                <option selected>Últimos 30 dias</option>
                <option>Últimos 90 dias</option>
                <option>Este ano</option>
                <option>Personalizado</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Médico</label>
            <select class="form-select">
                <option value="">Todos</option>
                <?php foreach ($MOCK_USERS as $u): if ($u['role'] !== 'medico') continue; ?>
                    <option><?= $u['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Especialidade</label>
            <select class="form-select">
                <option value="">Todas</option>
                <?php foreach ($MOCK_SPECIALTIES as $s): ?>
                    <option><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel me-1"></i>Aplicar Filtros
            </button>
        </div>
    </form>
</div>

<!-- KPIs -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Atendimentos</p>
                    <h3 class="mb-0">342</h3>
                    <small class="text-success"><i class="bi bi-arrow-up"></i> 12% vs mês anterior</small>
                </div>
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-clipboard2-pulse"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Novos Pacientes</p>
                    <h3 class="mb-0">48</h3>
                    <small class="text-success"><i class="bi bi-arrow-up"></i> 8% vs mês anterior</small>
                </div>
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-person-plus"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Teleconsultas</p>
                    <h3 class="mb-0">67</h3>
                    <small class="text-success"><i class="bi bi-arrow-up"></i> 25% vs mês anterior</small>
                </div>
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-camera-video"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Taxa Retorno</p>
                    <h3 class="mb-0">78%</h3>
                    <small class="text-warning"><i class="bi bi-dash"></i> Estável</small>
                </div>
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-arrow-repeat"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Gráfico de Atendimentos -->
    <div class="col-lg-8">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3">Atendimentos por Período</h6>
            <canvas id="attendanceChart" height="120"></canvas>
        </div>
    </div>

    <!-- Top Diagnósticos -->
    <div class="col-lg-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3">Diagnósticos Mais Frequentes</h6>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1"><span>Hipertensão (I10)</span><span class="fw-bold">23%</span></div>
                <div class="progress" style="height:8px;"><div class="progress-bar" style="width:23%"></div></div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1"><span>Diabetes (E11)</span><span class="fw-bold">18%</span></div>
                <div class="progress" style="height:8px;"><div class="progress-bar bg-success" style="width:18%"></div></div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1"><span>IVAS (J06)</span><span class="fw-bold">15%</span></div>
                <div class="progress" style="height:8px;"><div class="progress-bar bg-info" style="width:15%"></div></div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1"><span>Dorsalgia (M54)</span><span class="fw-bold">12%</span></div>
                <div class="progress" style="height:8px;"><div class="progress-bar bg-warning" style="width:12%"></div></div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1"><span>Ansiedade (F41)</span><span class="fw-bold">9%</span></div>
                <div class="progress" style="height:8px;"><div class="progress-bar bg-danger" style="width:9%"></div></div>
            </div>
        </div>
    </div>

    <!-- Performance por Médico -->
    <div class="col-lg-6">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3">Performance por Médico</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Médico</th><th>Atend.</th><th>Média/dia</th><th>Tempo Médio</th></tr></thead>
                    <tbody>
                        <tr><td>Dr. Evandro Ribeiro</td><td>98</td><td>4.2</td><td>25 min</td></tr>
                        <tr><td>Dra. Ana Beatriz</td><td>87</td><td>3.8</td><td>30 min</td></tr>
                        <tr><td>Dr. Roberto Lima</td><td>82</td><td>3.5</td><td>22 min</td></tr>
                        <tr><td>Dra. Mariana Costa</td><td>75</td><td>3.2</td><td>28 min</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico por Especialidade -->
    <div class="col-lg-6">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3">Atendimentos por Especialidade</h6>
            <canvas id="specialtyChart" height="150"></canvas>
        </div>
    </div>
</div>

<script>
// Gráfico de Atendimentos
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
        datasets: [{
            label: 'Presencial',
            data: [65, 72, 68, 85],
            borderColor: '#0d6efd',
            tension: 0.3,
            fill: false
        }, {
            label: 'Telemedicina',
            data: [12, 18, 22, 28],
            borderColor: '#0dcaf0',
            tension: 0.3,
            fill: false
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Gráfico por Especialidade
new Chart(document.getElementById('specialtyChart'), {
    type: 'doughnut',
    data: {
        labels: ['Clínico Geral', 'Cardiologia', 'Dermatologia', 'Ginecologia', 'Outros'],
        datasets: [{ data: [35, 25, 18, 15, 7], backgroundColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#6c757d'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
