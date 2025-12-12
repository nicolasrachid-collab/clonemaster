<?php
/**
 * PEI - Prontuário Eletrônico Inteligente (Demo)
 * Router principal
 */
session_start();

// Headers para prevenir cache do navegador
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('ETag: "' . md5(time()) . '"');

// #region agent log
$logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.cursor';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logPath = $logDir . DIRECTORY_SEPARATOR . 'debug.log';
$logData = [
    'sessionId' => session_id(),
    'runId' => 'debug-name-change',
    'hypothesisId' => 'A',
    'location' => 'index.php:12',
    'message' => 'Session started, checking user data',
    'data' => [
        'sessionExists' => isset($_SESSION['user']),
        'sessionName' => $_SESSION['user']['name'] ?? 'NOT_SET',
        'sessionId' => session_id(),
        'logPath' => $logPath,
        'logPathExists' => file_exists($logPath),
        'logDirExists' => is_dir($logDir),
        'cwd' => getcwd(),
        '__DIR__' => __DIR__,
        '__FILE__' => __FILE__
    ],
    'timestamp' => time() * 1000
];
$writeResult = @file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
$logData['writeResult'] = $writeResult;
$logData['writeError'] = error_get_last();
@file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
// #endregion

// Carregar dados mockados
require_once __DIR__ . '/../src/mock_data.php';

// #region agent log
$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.cursor' . DIRECTORY_SEPARATOR . 'debug.log';
$logData = [
    'sessionId' => session_id(),
    'runId' => 'debug-name-change',
    'hypothesisId' => 'B',
    'location' => 'index.php:28',
    'message' => 'Mock data loaded, checking MOCK_USERS',
    'data' => [
        'mockUserName' => $MOCK_USERS[0]['name'] ?? 'NOT_FOUND',
        'mockUserId' => $MOCK_USERS[0]['id'] ?? 'NOT_FOUND'
    ],
    'timestamp' => time() * 1000
];
@file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
// #endregion

// Simular usuário logado para demo - SEMPRE atualizar para garantir nome correto
$correctUser = [
    'id' => 1,
    'name' => 'Dr. Evandro Ribeiro',
    'email' => 'carlos@clinica.com',
    'role' => 'medico',
    'specialty' => 'Clínico Geral'
];

// SEMPRE definir o usuário correto, sobrescrevendo qualquer valor antigo
$_SESSION['user'] = $correctUser;

// #region agent log
$logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.cursor' . DIRECTORY_SEPARATOR . 'debug.log';
$logData = [
    'sessionId' => session_id(),
    'runId' => 'debug-name-change-v2',
    'hypothesisId' => 'A',
    'location' => 'index.php:74',
    'message' => 'User session ALWAYS set to correct name (v2 code)',
    'data' => [
        'userName' => $correctUser['name'],
        'userId' => $correctUser['id'],
        'sessionUpdated' => true,
        'codeVersion' => 'v2-simplified',
        'sessionBefore' => isset($_SESSION['user']) ? ($_SESSION['user']['name'] ?? 'NOT_SET') : 'NO_SESSION'
    ],
    'timestamp' => time() * 1000
];
@file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
// #endregion

// Helper functions
function current_user() {
    // #region agent log
    $user = $_SESSION['user'] ?? null;
    $logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.cursor' . DIRECTORY_SEPARATOR . 'debug.log';
    $logData = [
        'sessionId' => session_id(),
        'runId' => 'debug-name-change',
        'hypothesisId' => 'C',
        'location' => 'index.php:current_user',
        'message' => 'current_user() called',
        'data' => [
            'userExists' => $user !== null,
            'userName' => $user['name'] ?? 'NULL',
            'userId' => $user['id'] ?? 'NULL'
        ],
        'timestamp' => time() * 1000
    ];
    @file_put_contents($logPath, json_encode($logData) . "\n", FILE_APPEND);
    // #endregion
    return $user;
}

function is_active($page) {
    $current = $_GET['page'] ?? 'dashboard';
    return $current === $page ? 'active' : '';
}

function format_date($date) {
    return date('d/m/Y', strtotime($date));
}

function format_time($time) {
    return substr($time, 0, 5);
}

function calculate_age($birth_date) {
    $birth = new DateTime($birth_date);
    $today = new DateTime();
    return $birth->diff($today)->y;
}

function status_badge($status) {
    $badges = [
        'scheduled' => '<span class="badge bg-secondary">Agendado</span>',
        'confirmed' => '<span class="badge bg-primary">Confirmado</span>',
        'waiting' => '<span class="badge bg-warning text-dark">Aguardando</span>',
        'in_progress' => '<span class="badge bg-info">Em Atendimento</span>',
        'completed' => '<span class="badge bg-success">Finalizado</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

// Router
$page = $_GET['page'] ?? 'dashboard';
$allowed_pages = ['dashboard', 'agenda', 'patients', 'prontuario', 'atendimento', 'forms', 'reports', 'telemedicina', 'settings'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Render content
ob_start();
$view_file = __DIR__ . '/../views/' . $page . '.php';
if (file_exists($view_file)) {
    include $view_file;
} else {
    echo '<div class="alert alert-warning">Página em desenvolvimento.</div>';
}
$content = ob_get_clean();

// Render layout
include __DIR__ . '/../views/layout.php';
