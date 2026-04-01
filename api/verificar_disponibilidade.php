<?php
// api/verificar_disponibilidade.php
include 'db.php';

$quarto_id = isset($_GET['quarto_id']) ? (int)$_GET['quarto_id'] : 0;
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';

if ($quarto_id === 0 || empty($checkin) || empty($checkout)) {
    echo "erro";
    exit;
}

// 1. Pega a quantidade TOTAL de unidades desse quarto (ex: 3 Suítes Master)
$stmt_q = $conn->prepare("SELECT quantidade FROM quartos WHERE id = ?");
$stmt_q->bind_param("i", $quarto_id);
$stmt_q->execute();
$res_q = $stmt_q->get_result();
$quarto = $res_q->fetch_assoc();
$quantidade_total = $quarto ? (int)$quarto['quantidade'] : 1;
$stmt_q->close();

// 2. Conta quantas reservas (pagas, pendentes ou BLOQUEADAS) existem nesse período
// A regra de sobreposição de datas: (CheckinNovo < CheckoutAntigo) E (CheckoutNovo > CheckinAntigo)
$sql = "SELECT COUNT(*) as ocupados 
        FROM reservas 
        WHERE quarto_id = ? 
        AND status IN ('pago', 'pendente', 'bloqueado')
        AND (checkin < ? AND checkout > ?)";

$stmt_r = $conn->prepare($sql);
$stmt_r->bind_param("iss", $quarto_id, $checkout, $checkin);
$stmt_r->execute();
$res_r = $stmt_r->get_result();
$row = $res_r->fetch_assoc();
$quartos_ocupados = (int)$row['ocupados'];
$stmt_r->close();

// 3. Responde pro site
if ($quartos_ocupados >= $quantidade_total) {
    echo "indisponivel";
} else {
    echo "disponivel";
}
?>