<?php
/**
 * Dados mockados para demo do PEI - Prontuário Eletrônico Inteligente
 */

// Usuários (médicos e admin)
$MOCK_USERS = [
    ['id' => 1, 'name' => 'Dr. Evandro Ribeiro', 'email' => 'carlos@clinica.com', 'role' => 'medico', 'specialty' => 'Clínico Geral', 'crm' => 'CRM-SP 123456'],
    ['id' => 2, 'name' => 'Dra. Ana Beatriz', 'email' => 'ana@clinica.com', 'role' => 'medico', 'specialty' => 'Cardiologia', 'crm' => 'CRM-SP 789012'],
    ['id' => 3, 'name' => 'Dr. Roberto Lima', 'email' => 'roberto@clinica.com', 'role' => 'medico', 'specialty' => 'Dermatologia', 'crm' => 'CRM-SP 345678'],
    ['id' => 4, 'name' => 'Dra. Mariana Costa', 'email' => 'mariana@clinica.com', 'role' => 'medico', 'specialty' => 'Ginecologia', 'crm' => 'CRM-SP 901234'],
    ['id' => 5, 'name' => 'Admin Sistema', 'email' => 'admin@clinica.com', 'role' => 'admin', 'specialty' => null, 'crm' => null],
];

// Pacientes
$MOCK_PATIENTS = [
    ['id' => 1, 'name' => 'João Silva Santos', 'cpf' => '123.456.789-00', 'birth_date' => '1985-03-15', 'gender' => 'M', 'phone' => '(11) 99999-1234', 'email' => 'joao.silva@email.com', 'address' => 'Rua das Flores, 123 - São Paulo/SP', 'blood_type' => 'O+', 'allergies' => 'Dipirona', 'insurance' => 'Unimed'],
    ['id' => 2, 'name' => 'Maria Oliveira', 'cpf' => '987.654.321-00', 'birth_date' => '1990-07-22', 'gender' => 'F', 'phone' => '(11) 98888-5678', 'email' => 'maria.oliveira@email.com', 'address' => 'Av. Paulista, 1000 - São Paulo/SP', 'blood_type' => 'A+', 'allergies' => 'Nenhuma', 'insurance' => 'Bradesco Saúde'],
    ['id' => 3, 'name' => 'Pedro Henrique Costa', 'cpf' => '456.789.123-00', 'birth_date' => '1978-11-08', 'gender' => 'M', 'phone' => '(11) 97777-9012', 'email' => 'pedro.costa@email.com', 'address' => 'Rua Augusta, 500 - São Paulo/SP', 'blood_type' => 'B-', 'allergies' => 'Penicilina, Látex', 'insurance' => 'SulAmérica'],
    ['id' => 4, 'name' => 'Ana Clara Ferreira', 'cpf' => '321.654.987-00', 'birth_date' => '1995-01-30', 'gender' => 'F', 'phone' => '(11) 96666-3456', 'email' => 'ana.ferreira@email.com', 'address' => 'Rua Oscar Freire, 200 - São Paulo/SP', 'blood_type' => 'AB+', 'allergies' => 'Nenhuma', 'insurance' => 'Amil'],
    ['id' => 5, 'name' => 'Carlos Eduardo Mendes', 'cpf' => '654.321.987-00', 'birth_date' => '1982-09-12', 'gender' => 'M', 'phone' => '(11) 95555-7890', 'email' => 'carlos.mendes@email.com', 'address' => 'Alameda Santos, 800 - São Paulo/SP', 'blood_type' => 'O-', 'allergies' => 'AAS', 'insurance' => 'Particular'],
    ['id' => 6, 'name' => 'Juliana Rodrigues', 'cpf' => '789.123.456-00', 'birth_date' => '1988-05-25', 'gender' => 'F', 'phone' => '(11) 94444-1234', 'email' => 'juliana.rodrigues@email.com', 'address' => 'Rua Haddock Lobo, 350 - São Paulo/SP', 'blood_type' => 'A-', 'allergies' => 'Nenhuma', 'insurance' => 'Porto Seguro'],
];

// Agendamentos de hoje e próximos dias
$today = date('Y-m-d');
$MOCK_APPOINTMENTS = [
    ['id' => 1, 'patient_id' => 1, 'doctor_id' => 1, 'date' => $today, 'time' => '08:00', 'end_time' => '08:30', 'type' => 'Consulta', 'status' => 'confirmed', 'notes' => 'Retorno - Hipertensão'],
    ['id' => 2, 'patient_id' => 2, 'doctor_id' => 2, 'date' => $today, 'time' => '08:30', 'end_time' => '09:00', 'type' => 'Consulta', 'status' => 'waiting', 'notes' => 'Primeira consulta'],
    ['id' => 3, 'patient_id' => 3, 'doctor_id' => 1, 'date' => $today, 'time' => '09:00', 'end_time' => '09:30', 'type' => 'Retorno', 'status' => 'in_progress', 'notes' => 'Acompanhamento diabetes'],
    ['id' => 4, 'patient_id' => 4, 'doctor_id' => 3, 'date' => $today, 'time' => '09:30', 'end_time' => '10:00', 'type' => 'Consulta', 'status' => 'confirmed', 'notes' => 'Avaliação dermatológica'],
    ['id' => 5, 'patient_id' => 5, 'doctor_id' => 2, 'date' => $today, 'time' => '10:00', 'end_time' => '10:30', 'type' => 'Exame', 'status' => 'confirmed', 'notes' => 'Eletrocardiograma'],
    ['id' => 6, 'patient_id' => 6, 'doctor_id' => 4, 'date' => $today, 'time' => '10:30', 'end_time' => '11:00', 'type' => 'Telemedicina', 'status' => 'confirmed', 'notes' => 'Consulta online'],
    ['id' => 7, 'patient_id' => 1, 'doctor_id' => 1, 'date' => date('Y-m-d', strtotime('+1 day')), 'time' => '14:00', 'end_time' => '14:30', 'type' => 'Retorno', 'status' => 'scheduled', 'notes' => 'Verificar exames'],
    ['id' => 8, 'patient_id' => 2, 'doctor_id' => 4, 'date' => date('Y-m-d', strtotime('+1 day')), 'time' => '15:00', 'end_time' => '15:30', 'type' => 'Consulta', 'status' => 'scheduled', 'notes' => 'Check-up anual'],
    ['id' => 9, 'patient_id' => 3, 'doctor_id' => 2, 'date' => date('Y-m-d', strtotime('+2 days')), 'time' => '09:00', 'end_time' => '09:30', 'type' => 'Telemedicina', 'status' => 'scheduled', 'notes' => 'Acompanhamento cardíaco'],
];

// Histórico de atendimentos (encounters)
$MOCK_ENCOUNTERS = [
    ['id' => 1, 'patient_id' => 1, 'doctor_id' => 1, 'date' => '2025-11-15', 'chief_complaint' => 'Dor de cabeça frequente', 'diagnosis' => 'Cefaleia tensional (G44.2)', 'prescription' => 'Paracetamol 750mg - 1 comp. 8/8h por 5 dias', 'notes' => 'Orientado sobre técnicas de relaxamento. Retorno em 15 dias.'],
    ['id' => 2, 'patient_id' => 1, 'doctor_id' => 1, 'date' => '2025-10-20', 'chief_complaint' => 'Pressão alta', 'diagnosis' => 'Hipertensão arterial (I10)', 'prescription' => 'Losartana 50mg - 1 comp. manhã', 'notes' => 'PA: 150x95. Iniciado tratamento. Orientado dieta hipossódica.'],
    ['id' => 3, 'patient_id' => 2, 'doctor_id' => 2, 'date' => '2025-11-28', 'chief_complaint' => 'Palpitações', 'diagnosis' => 'Taquicardia sinusal (R00.0)', 'prescription' => 'Propranolol 40mg - 1 comp. 12/12h', 'notes' => 'ECG normal. Solicitado Holter 24h.'],
    ['id' => 4, 'patient_id' => 3, 'doctor_id' => 1, 'date' => '2025-12-01', 'chief_complaint' => 'Glicemia elevada', 'diagnosis' => 'Diabetes mellitus tipo 2 (E11)', 'prescription' => 'Metformina 850mg - 1 comp. após almoço e jantar', 'notes' => 'HbA1c: 7.8%. Encaminhado para nutricionista.'],
];

// Especialidades disponíveis
$MOCK_SPECIALTIES = [
    'Clínico Geral', 'Cardiologia', 'Dermatologia', 'Ginecologia', 'Ortopedia',
    'Pediatria', 'Psiquiatria', 'Neurologia', 'Oftalmologia', 'Urologia'
];

// Formulários por especialidade
$MOCK_FORMS = [
    ['id' => 1, 'name' => 'Anamnese Geral', 'specialty' => 'Clínico Geral', 'fields_count' => 15],
    ['id' => 2, 'name' => 'Avaliação Cardiológica', 'specialty' => 'Cardiologia', 'fields_count' => 20],
    ['id' => 3, 'name' => 'Exame Dermatológico', 'specialty' => 'Dermatologia', 'fields_count' => 12],
    ['id' => 4, 'name' => 'Consulta Ginecológica', 'specialty' => 'Ginecologia', 'fields_count' => 18],
    ['id' => 5, 'name' => 'Pré-Natal', 'specialty' => 'Ginecologia', 'fields_count' => 25],
];

// Medicamentos comuns (para autocomplete)
$MOCK_MEDICATIONS = [
    'Paracetamol 750mg', 'Dipirona 500mg', 'Ibuprofeno 600mg', 'Amoxicilina 500mg',
    'Azitromicina 500mg', 'Losartana 50mg', 'Metformina 850mg', 'Omeprazol 20mg',
    'Atorvastatina 20mg', 'Sinvastatina 40mg', 'Propranolol 40mg', 'Captopril 25mg'
];

// CIDs comuns
$MOCK_CIDS = [
    'I10' => 'Hipertensão essencial (primária)',
    'E11' => 'Diabetes mellitus tipo 2',
    'J06' => 'Infecções agudas das vias aéreas superiores',
    'M54' => 'Dorsalgia',
    'G44' => 'Outras síndromes de cefaleia',
    'K21' => 'Doença de refluxo gastroesofágico',
    'F41' => 'Outros transtornos ansiosos',
    'J45' => 'Asma',
];

// Função helper para buscar dados
function get_patient($id) {
    global $MOCK_PATIENTS;
    foreach ($MOCK_PATIENTS as $p) {
        if ($p['id'] == $id) return $p;
    }
    return null;
}

function get_doctor($id) {
    global $MOCK_USERS;
    foreach ($MOCK_USERS as $u) {
        if ($u['id'] == $id) return $u;
    }
    return null;
}

function get_patient_encounters($patient_id) {
    global $MOCK_ENCOUNTERS;
    return array_filter($MOCK_ENCOUNTERS, fn($e) => $e['patient_id'] == $patient_id);
}

