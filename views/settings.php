<?php
global $MOCK_SPECIALTIES;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-gear-fill me-2"></i>Configurações</h4>
</div>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="stat-card">
            <nav class="nav flex-column nav-pills">
                <a class="nav-link active" data-bs-toggle="pill" href="#perfil">
                    <i class="bi bi-person me-2"></i>Meu Perfil
                </a>
                <a class="nav-link" data-bs-toggle="pill" href="#clinica">
                    <i class="bi bi-building me-2"></i>Clínica
                </a>
                <a class="nav-link" data-bs-toggle="pill" href="#usuarios">
                    <i class="bi bi-people me-2"></i>Usuários
                </a>
                <a class="nav-link" data-bs-toggle="pill" href="#notificacoes">
                    <i class="bi bi-bell me-2"></i>Notificações
                </a>
                <a class="nav-link" data-bs-toggle="pill" href="#seguranca">
                    <i class="bi bi-shield-lock me-2"></i>Segurança
                </a>
                <a class="nav-link" data-bs-toggle="pill" href="#integracoes">
                    <i class="bi bi-plug me-2"></i>Integrações
                </a>
            </nav>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="tab-content">
            <!-- Perfil -->
            <div class="tab-pane fade show active" id="perfil">
                <div class="stat-card">
                    <h5 class="mb-4">Meu Perfil</h5>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars(current_user()['name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars(current_user()['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Especialidade</label>
                                <select class="form-select">
                                    <?php foreach ($MOCK_SPECIALTIES as $s): ?>
                                        <option <?= current_user()['specialty'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CRM</label>
                                <input type="text" class="form-control" value="CRM-SP 123456">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control" value="(11) 99999-0000">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary"><i class="bi bi-check me-2"></i>Salvar Alterações</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Clínica -->
            <div class="tab-pane fade" id="clinica">
                <div class="stat-card">
                    <h5 class="mb-4">Dados da Clínica</h5>
                    <form class="row g-3">
                        <div class="col-md-8"><label class="form-label">Nome da Clínica</label><input class="form-control" value="Clínica Saúde Total"></div>
                        <div class="col-md-4"><label class="form-label">CNPJ</label><input class="form-control" value="12.345.678/0001-90"></div>
                        <div class="col-12"><label class="form-label">Endereço</label><input class="form-control" value="Av. Paulista, 1000 - São Paulo/SP"></div>
                        <div class="col-md-6"><label class="form-label">Telefone</label><input class="form-control" value="(11) 3000-0000"></div>
                        <div class="col-md-6"><label class="form-label">E-mail</label><input class="form-control" value="contato@clinicasaudetotal.com.br"></div>
                        <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check me-2"></i>Salvar</button></div>
                    </form>
                </div>
            </div>

            <!-- Usuários -->
            <div class="tab-pane fade" id="usuarios">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="mb-0">Gerenciar Usuários</h5>
                        <button class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Novo Usuário</button>
                    </div>
                    <table class="table">
                        <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ações</th></tr></thead>
                        <tbody>
                            <tr><td>Dr. Evandro Ribeiro</td><td>carlos@clinica.com</td><td><span class="badge bg-primary">Médico</span></td><td><span class="badge bg-success">Ativo</span></td><td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button></td></tr>
                            <tr><td>Dra. Ana Beatriz</td><td>ana@clinica.com</td><td><span class="badge bg-primary">Médico</span></td><td><span class="badge bg-success">Ativo</span></td><td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button></td></tr>
                            <tr><td>Admin Sistema</td><td>admin@clinica.com</td><td><span class="badge bg-danger">Admin</span></td><td><span class="badge bg-success">Ativo</span></td><td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notificações -->
            <div class="tab-pane fade" id="notificacoes">
                <div class="stat-card">
                    <h5 class="mb-4">Preferências de Notificação</h5>
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Notificações por e-mail</label></div>
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Lembretes de consultas</label></div>
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" checked><label class="form-check-label">Alertas de cancelamento</label></div>
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox"><label class="form-check-label">Notificações push no navegador</label></div>
                    <button class="btn btn-primary"><i class="bi bi-check me-2"></i>Salvar</button>
                </div>
            </div>

            <!-- Segurança -->
            <div class="tab-pane fade" id="seguranca">
                <div class="stat-card">
                    <h5 class="mb-4">Segurança da Conta</h5>
                    <div class="mb-4">
                        <h6>Alterar Senha</h6>
                        <div class="row g-3">
                            <div class="col-md-4"><input type="password" class="form-control" placeholder="Senha atual"></div>
                            <div class="col-md-4"><input type="password" class="form-control" placeholder="Nova senha"></div>
                            <div class="col-md-4"><input type="password" class="form-control" placeholder="Confirmar nova senha"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6>Autenticação em Dois Fatores (MFA)</h6>
                        <p class="text-muted small">Adicione uma camada extra de segurança à sua conta.</p>
                        <button class="btn btn-outline-primary"><i class="bi bi-shield-check me-2"></i>Ativar MFA</button>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-check me-2"></i>Salvar Alterações</button>
                </div>
            </div>

            <!-- Integrações -->
            <div class="tab-pane fade" id="integracoes">
                <div class="stat-card">
                    <h5 class="mb-4">Integrações</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-envelope text-primary fs-4 me-3"></i><strong>E-mail (SMTP)</strong></div>
                                    <span class="badge bg-success">Conectado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-whatsapp text-success fs-4 me-3"></i><strong>WhatsApp API</strong></div>
                                    <button class="btn btn-sm btn-outline-primary">Configurar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-cloud text-info fs-4 me-3"></i><strong>AWS S3 Storage</strong></div>
                                    <button class="btn btn-sm btn-outline-primary">Configurar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-camera-video text-danger fs-4 me-3"></i><strong>Jitsi Meet</strong></div>
                                    <span class="badge bg-success">Conectado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
