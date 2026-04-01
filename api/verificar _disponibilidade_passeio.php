<?php
// api/verificar_disponibilidade_passeio.php

// 1. Limpa a memória e desliga mensagens de aviso que quebram o Javascript
error_reporting(0);
ini_set('display_errors', 0);

// 2. A MÁGICA: Obriga o navegador a ler do banco de dados NA HORA (Mata o Cache)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/plain");

include 'db.php';

// 3. Limpa espaços em branco ou quebras de linha acidentais
ob_clean();

$passeio_id = isset($_GET['passeio_id']) ? (int)$_GET['passeio_id'] : 0;
$data_passeio = isset($_GET['data']) ? trim($_GET['data']) : '';

if ($passeio_id === 0 || empty($data_passeio)) {
    echo "disponivel";
    exit;
}

// 4. Checa o Banco de Dados (Exatamente no período)
$sql = "SELECT COUNT(*) as ocupados FROM reservas_passeios 
        WHERE passeio_id = ? 
        AND status IN ('pago', 'pendente', 'bloqueado') 
        AND (? >= DATE(checkin) AND ? <= DATE(checkout))";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iss", $passeio_id, $data_passeio, $data_passeio);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res) {
        $row = $res->fetch_assoc();
        if ($row['ocupados'] > 0) {
            echo "indisponivel";
            exit;
        }
    }
}

echo "disponivel";
?>