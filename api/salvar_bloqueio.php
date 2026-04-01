<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: ../admin/login.php"); exit(); }
include 'db.php';

// Validação de Segurança: Senha Master
$senha_digitada = trim($_POST['master_key'] ?? '');
$usuario_logado = $_SESSION['usuario_nome'] ?? '';

$stmt_user = $conn->prepare("SELECT senha_master FROM usuarios WHERE usuario = ?");
$stmt_user->bind_param("s", $usuario_logado);
$stmt_user->execute();
$res_user = $stmt_user->get_result();

$senha_valida = false;
if ($res_user->num_rows > 0) {
    $hash = $res_user->fetch_assoc()['senha_master'] ?? '';
    // Verifica se bate com o hash novo, se está em texto puro (antigo) ou a de emergência
    if (password_verify($senha_digitada, $hash) || $senha_digitada === $hash || $senha_digitada === 'nunes2026') {
        $senha_valida = true;
    }
}
$stmt_user->close();

if (!$senha_valida) {
    die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
}

// ---------------------------------------------------------
// Captura os dados do NOVO formulário unificado
// ---------------------------------------------------------
$tipo = $_POST['tipo_bloqueio'] ?? ''; // Pode ser 'quarto' ou 'passeio'
$item_id = (int)($_POST['item_id'] ?? 0);
$checkin = $_POST['data_inicio'] ?? '';
$checkout = $_POST['data_fim'] ?? '';
$motivo_digitado = trim($_POST['motivo'] ?? '');
$motivo = "🚫 BLOQUEIO: " . ($motivo_digitado ?: 'Manutenção/Limpeza');

$status = 'bloqueado';
$whatsapp = 'Sistema';
$valor_zero = 0;

if ($item_id === 0 || empty($checkin) || empty($checkout)) {
    die("<script>alert('Erro: Faltam informações de data.'); window.location.href='../admin/quartos.php';</script>");
}

// Verifica se é Quarto ou Passeio e salva na tabela correta
if ($tipo === 'quarto') {
    $stmt = $conn->prepare("INSERT INTO reservas (quarto_id, nome_cliente, whatsapp, checkin, checkout, status, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssd", $item_id, $motivo, $whatsapp, $checkin, $checkout, $status, $valor_zero);
    $stmt->execute();
    $stmt->close();
} elseif ($tipo === 'passeio') {
    $stmt = $conn->prepare("INSERT INTO reservas_passeios (passeio_id, nome_cliente, whatsapp, checkin, checkout, status, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssd", $item_id, $motivo, $whatsapp, $checkin, $checkout, $status, $valor_zero);
    $stmt->execute();
    $stmt->close();
}

// Volta para a página de quartos
header("Location: ../admin/quartos.php");
exit();
?>