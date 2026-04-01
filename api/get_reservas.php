<?php
error_reporting(0); 
include 'db.php';

header('Content-Type: application/json');
// Força o navegador a nunca fazer cache desse arquivo
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

ob_clean();
$eventos = [];

// 1. BUSCA QUARTOS (Somente pagos ou bloqueados)
$sql_quartos = "SELECT r.id, r.nome_cliente, r.checkin, r.checkout, r.status, q.nome AS nome_quarto 
        FROM reservas r 
        INNER JOIN quartos q ON r.quarto_id = q.id 
        WHERE r.status IN ('pago', 'bloqueado')";

$result = $conn->query($sql_quartos);

if($result) {
    while($row = $result->fetch_assoc()) {
        $status = trim(strtolower($row['status']));

        if ($status == 'bloqueado') {
            $cor = '#6b7280'; 
            $titulo = "🚫 [BLOQ] " . $row['nome_quarto']; 
        } elseif ($status == 'pago') {
            $cor = '#10b981'; // Verde
            $titulo = "🛏️ " . $row['nome_quarto'] . " - " . $row['nome_cliente'];
        }

        $eventos[] = [
            'id' => 'q_'.$row['id'],
            'title' => $titulo,
            'start' => $row['checkin'],
            'end' => date('Y-m-d', strtotime($row['checkout'] . ' +1 day')),
            'color' => $cor ?? '#10b981'
        ];
    }
}

// 2. BUSCA PASSEIOS AVULSOS (Somente pagos ou bloqueados)
$sql_passeios = "SELECT r.id, r.nome_cliente, r.checkin, r.status, p.nome AS nome_passeio 
        FROM reservas_passeios r 
        INNER JOIN passeios p ON r.passeio_id = p.id 
        WHERE r.status IN ('pago', 'bloqueado')";

$result_p = $conn->query($sql_passeios);

if($result_p) {
    while($row = $result_p->fetch_assoc()) {
        $status = trim(strtolower($row['status']));

        if ($status == 'bloqueado') {
            $cor = '#4b5563'; 
            $titulo = "🚫 [BLOQ] " . $row['nome_passeio']; 
        } else {
            $cor = '#3b82f6'; // Azul para destacar passeios avulsos
            $titulo = "🌳 " . $row['nome_passeio'] . " - " . $row['nome_cliente'];
        }

        $eventos[] = [
            'id' => 'p_'.$row['id'],
            'title' => $titulo,
            'start' => $row['checkin'],
            'end' => $row['checkin'], // Passeios acontecem no mesmo dia
            'color' => $cor
        ];
    }
}

echo json_encode($eventos);
exit();
?>