<?php
// api/importar_ical.php
include 'db.php';

// =========================================================================
// 1. CONFIGURAÇÃO DE SEGURANÇA E LINKS
// =========================================================================

// Uma senha secreta para ninguém rodar esse robô de sacanagem
$token_seguranca = "nunes_sync_2026"; 

if (!isset($_GET['token']) || $_GET['token'] !== $token_seguranca) {
    die("Acesso negado. Token de segurança inválido.");
}

// 🚨 MAPEAMENTO: Aqui você vai colar os links que o Booking.com te der.
// O número na esquerda é o ID do quarto no seu sistema. O link na direita é o do Booking.
$quartos_links = [
    // Exemplo: 1 => "https://admin.booking.com/hotel/hoteladmin/ical.html?t=.....",
    // Exemplo: 2 => "https://admin.booking.com/hotel/hoteladmin/ical.html?t=.....",
    
    // Deixe vazio se ainda não tiver o link do quarto
    15 => "https://ical.booking.com/v1/export/t/7b4e7549-9ae1-4075-985b-fcfbe5e67031.ics", 
    17 => "https://ical.booking.com/v1/export/t/0819e6cc-e6aa-46b8-bbc4-189bdcbf2727.ics",
    20 => "https://ical.booking.com/v1/export/t/62904161-c889-4459-809f-9e1655768364.ics", 
    22 => "https://ical.booking.com/v1/export/t/b066a28d-04cc-4dda-bfa7-1c4c5b291084.ics",
    30 => "https://ical.booking.com/v1/export/t/86fddf68-7a99-4cc3-ad48-22a3d57c52bf.ics", 
    31 => "https://ical.booking.com/v1/export/t/44d08987-a5d2-4e54-b011-946988529665.ics",
    18 => "https://ical.booking.com/v1/export/t/823bb698-89a6-4a60-9218-d300aa5b43ff.ics", 
    27 => "https://ical.booking.com/v1/export/t/c0e5f92a-84d9-4483-a441-1730af976e1d.ics",
    29 => "https://ical.booking.com/v1/export/t/e84ec4a5-5d0b-48b8-9e4d-4e6a9f227622.ics",
    32 => "https://ical.booking.com/v1/export/t/7adaacfe-c8d6-4bac-9330-eb34b3c7a43c.ics", 
    33 => "https://ical.booking.com/v1/export/t/fe7e8339-ad02-46ef-94f8-876bc9a93b48.ics",
];

// =========================================================================
// 2. MOTOR DE SINCRONIZAÇÃO
// =========================================================================

echo "<h2>Iniciando Sincronização Pousada Solo Nunes...</h2>";
$hoje = date('Y-m-d');

foreach ($quartos_links as $quarto_id => $url_ical) {
    if (empty($url_ical)) {
        continue; // Pula os quartos que ainda não tem link configurado
    }

    echo "<b>Verificando Quarto ID: $quarto_id...</b><br>";

    // Pega as reservas do Booking que já estão salvas no nosso banco para esse quarto
    $stmt_banco = $conn->prepare("SELECT id, nome_cliente, checkin, checkout FROM reservas WHERE quarto_id = ? AND nome_cliente LIKE '🚫 BLOQUEIO BOOKING - %'");
    $stmt_banco->bind_param("i", $quarto_id);
    $stmt_banco->execute();
    $res_banco = $stmt_banco->get_result();
    
    $reservas_db = [];
    while ($row = $res_banco->fetch_assoc()) {
        // Extrai o ID único do Booking que salvamos no nome
        $partes = explode(" - ", $row['nome_cliente']);
        $uid = end($partes);
        $reservas_db[$uid] = $row;
    }
    $stmt_banco->close();

    // Lê o calendário atualizado direto do Booking.com
    $ical_content = @file_get_contents($url_ical);
    
    if ($ical_content === false) {
        echo "<span style='color:red;'>Erro: Não foi possível ler o link do quarto $quarto_id. Verifique se a URL está correta.</span><br>";
        continue;
    }

    // Isola os eventos (reservas) dentro do arquivo
    preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/is', $ical_content, $matches);
    $eventos = $matches[1];
    $uids_no_booking_agora = [];

    foreach ($eventos as $evento) {
        // Extrai as datas e o código de identificação do Booking
        preg_match('/DTSTART.*?:([0-9]{8})/', $evento, $start_match);
        preg_match('/DTEND.*?:([0-9]{8})/', $evento, $end_match);
        preg_match('/UID:(.*?)\r?\n/', $evento, $uid_match);

        if (!empty($start_match[1]) && !empty($end_match[1]) && !empty($uid_match[1])) {
            $checkin = date('Y-m-d', strtotime($start_match[1]));
            $checkout = date('Y-m-d', strtotime($end_match[1]));
            $uid = trim($uid_match[1]);

            // Ignora reservas que já passaram
            if ($checkout <= $hoje) continue;

            $uids_no_booking_agora[] = $uid;
            $nome_bloqueio = "🚫 BLOQUEIO BOOKING - " . $uid;

            if (isset($reservas_db[$uid])) {
                // A reserva já existe no nosso banco. Vamos ver se o cliente mudou a data lá no Booking?
                $reserva_atual = $reservas_db[$uid];
                if ($reserva_atual['checkin'] != $checkin || $reserva_atual['checkout'] != $checkout) {
                    $stmt_up = $conn->prepare("UPDATE reservas SET checkin = ?, checkout = ? WHERE id = ?");
                    $stmt_up->bind_param("ssi", $checkin, $checkout, $reserva_atual['id']);
                    $stmt_up->execute();
                    $stmt_up->close();
                    echo "<span style='color:blue;'>-> Reserva atualizada (Mudança de Data): $checkin a $checkout</span><br>";
                }
            } else {
                // É uma reserva NOVA! Vamos bloquear a vaga.
                $status = 'bloqueado';
                $whatsapp = 'Booking.com';
                $valor = 0;
                
                $stmt_insert = $conn->prepare("INSERT INTO reservas (quarto_id, nome_cliente, whatsapp, checkin, checkout, status, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("isssssd", $quarto_id, $nome_bloqueio, $whatsapp, $checkin, $checkout, $status, $valor);
                $stmt_insert->execute();
                $stmt_insert->close();
                echo "<span style='color:green;'>-> NOVA reserva bloqueada: $checkin a $checkout</span><br>";
            }
        }
    }

    // MÁGICA DOS CANCELAMENTOS: Verifica se tem algo no nosso banco que NÃO ESTÁ MAIS no calendário do Booking (Cancelamento)
    foreach ($reservas_db as $uid_db => $dados_db) {
        if (!in_array($uid_db, $uids_no_booking_agora)) {
            $stmt_del = $conn->prepare("DELETE FROM reservas WHERE id = ?");
            $stmt_del->bind_param("i", $dados_db['id']);
            $stmt_del->execute();
            $stmt_del->close();
            echo "<span style='color:orange;'>-> Reserva Cancelada no Booking. Vaga liberada no site!</span><br>";
        }
    }
    echo "<hr>";
}

echo "<br><h3>Sincronização Finalizada com Sucesso! ✅</h3>";
?>