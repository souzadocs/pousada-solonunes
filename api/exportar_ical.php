<?php
// api/exportar_ical.php
include 'db.php';

// Pega o ID do quarto pela URL (Ex: exportar_ical.php?quarto_id=1)
$quarto_id = isset($_GET['quarto_id']) ? (int)$_GET['quarto_id'] : 0;

if ($quarto_id === 0) {
    die("Erro: ID do quarto nao informado. Use exportar_ical.php?quarto_id=ID");
}

// 1. Busca o nome do quarto para dar título ao calendário
$stmt_q = $conn->prepare("SELECT nome FROM quartos WHERE id = ?");
$stmt_q->bind_param("i", $quarto_id);
$stmt_q->execute();
$res_q = $stmt_q->get_result();
$quarto = $res_q->fetch_assoc();
$nome_quarto = $quarto ? $quarto['nome'] : "Quarto Desconhecido";
$stmt_q->close();

// 2. Busca as reservas ocupadas (pagas, pendentes ou bloqueadas) para este quarto
$stmt = $conn->prepare("SELECT id, checkin, checkout, nome_cliente, status FROM reservas WHERE quarto_id = ? AND status IN ('pago', 'pendente', 'bloqueado')");
$stmt->bind_param("i", $quarto_id);
$stmt->execute();
$reservas = $stmt->get_result();

// 3. Configura os cabeçalhos para o navegador entender que é um arquivo de Calendário (iCal)
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="solo_nunes_quarto_'.$quarto_id.'.ics"');

// 4. Imprime a estrutura padrão universal do iCal (RFC 5545)
echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//Pousada Solo Nunes//Gestao de Reservas//PT\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "X-WR-CALNAME:Solo Nunes - " . $nome_quarto . "\r\n";

// 5. Transforma cada reserva do banco em um "Evento de Dia Inteiro"
while ($r = $reservas->fetch_assoc()) {
    // Formata as datas para o padrão exigido pelo Booking (AAAAMMDD)
    $dtstart = date('Ymd', strtotime($r['checkin']));
    
    // IMPORTANTE: O iCal trata a data de fim como exclusiva, então o checkout funciona perfeito
    $dtend = date('Ymd', strtotime($r['checkout']));
    
    // Gera um ID único para o Booking não duplicar reservas
    $uid = "reserva_" . $r['id'] . "_q" . $quarto_id . "@solonunes.com.br";
    $dtstamp = date('Ymd\THis\Z'); // Data de agora no formato iCal

    // Cria o título baseado no status
    $summary = "Reserva - Solo Nunes";
    if ($r['status'] == 'bloqueado') {
        $summary = "Bloqueio/Manutencao";
    }

    echo "BEGIN:VEVENT\r\n";
    echo "UID:" . $uid . "\r\n";
    echo "DTSTAMP:" . $dtstamp . "\r\n";
    echo "DTSTART;VALUE=DATE:" . $dtstart . "\r\n";
    echo "DTEND;VALUE=DATE:" . $dtend . "\r\n";
    echo "SUMMARY:" . $summary . "\r\n";
    echo "DESCRIPTION:Status " . $r['status'] . " - Ocupado\r\n";
    echo "END:VEVENT\r\n";
}

echo "END:VCALENDAR\r\n";

$stmt->close();
?>