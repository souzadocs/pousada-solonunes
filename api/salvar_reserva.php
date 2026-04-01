<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Captura de dados
    $nome = trim($_POST['nome']);
    $whatsapp = trim($_POST['whatsapp']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $quarto_id = (int)$_POST['quarto_id'];
    $hospedes = (int)$_POST['hospedes']; 
    $obs = trim($_POST['obs']);
    
    $data_in = strtotime($checkin);
    $data_out = strtotime($checkout);
    $hoje = strtotime(date('Y-m-d'));

    if ($data_out <= $data_in) {
        die("Erro: A data de saída não pode ser anterior ou igual à data de entrada. <a href='../php/reserva.php'>Voltar</a>");
    }

    if ($data_in < $hoje) {
        die("Erro: Você não pode reservar uma data no passado. <a href='../php/reserva.php'>Voltar</a>");
    }

    // =========================================================================
    // LÓGICA NORMAL (QUARTOS PADRÕES)
    // =========================================================================
    
    // Busca info do quarto
    $stmt_quarto = $conn->prepare("SELECT nome, preco_noite FROM quartos WHERE id = ?");
    $stmt_quarto->bind_param("i", $quarto_id);
    $stmt_quarto->execute();
    $res_quarto_info = $stmt_quarto->get_result();
    
    if ($res_quarto_info->num_rows === 0) {
        die("Erro: Quarto não encontrado.");
    }
    
    $quarto_info = $res_quarto_info->fetch_assoc();
    $nome_quarto = $quarto_info['nome'];
    $preco_diaria = $quarto_info['preco_noite'];
    $stmt_quarto->close();

    $noites = ($data_out - $data_in) / 86400;
    
    // O TOTAL AQUI É SOMENTE O QUARTO (Passeios não entram nessa conta)
    $valor_total_quarto = $noites * $preco_diaria;

    // Passeios Opcionais (Apenas para registrar o que ele quer)
    $passeios_selecionados = $_POST['passeios_add'] ?? [];
    $data_passeio_opcional = trim($_POST['data_passeio_opcional'] ?? '');
    $tipo_veiculo_opcional = trim($_POST['tipo_veiculo_opcional'] ?? '');
    $nomes_passeios = [];
    $valor_passeios_total = 0;

    if (!empty($passeios_selecionados)) {
        $stmt_passeio = $conn->prepare("SELECT nome, preco FROM passeios WHERE id = ?");
        
        foreach ($passeios_selecionados as $id_passeio) {
            $id_pass_int = (int)$id_passeio;
            $stmt_passeio->bind_param("i", $id_pass_int);
            $stmt_passeio->execute();
            $res_passeio = $stmt_passeio->get_result();
            
            if ($p = $res_passeio->fetch_assoc()) {
                $nomes_passeios[] = $p['nome'];
                $valor_passeios_total += ($p['preco'] * $hospedes);
            }
        }
        $stmt_passeio->close();
    }

    if (!empty($nomes_passeios)) {
        $texto_passeios = implode(", ", $nomes_passeios);
        if(!empty($data_passeio_opcional)) {
            $texto_passeios .= " (Data: " . date('d/m/Y', strtotime($data_passeio_opcional)) . " - " . $tipo_veiculo_opcional . ")";
        }
        $texto_passeios_bd = $texto_passeios; // Para salvar no banco
    } else {
        $texto_passeios = "Nenhum passeio adicional.";
        $texto_passeios_bd = "Nenhum passeio adicional."; // Se não tiver, manda texto padrao
    }

    // Checa disponibilidade
    $stmt_checar = $conn->prepare("
        SELECT 
            (SELECT quantidade FROM quartos WHERE id = ?) as total_vagas,
            (SELECT COUNT(id) FROM reservas WHERE quarto_id = ? AND status != 'cancelado' AND (? < checkout AND ? > checkin)) as ocupados
    ");
    $stmt_checar->bind_param("iiss", $quarto_id, $quarto_id, $checkin, $checkout);
    $stmt_checar->execute();
    $resultado_checa = $stmt_checar->get_result()->fetch_assoc();

    // Bloqueia se não tiver vaga
    if ($resultado_checa['ocupados'] >= $resultado_checa['total_vagas']) {
        $stmt_checar->close();
        echo "<script>
                alert('Atenção: Este quarto esgotou para as datas selecionadas. Por favor, tente outra acomodação.');
                window.history.back();
              </script>";
        exit();
    }
    $stmt_checar->close();

    // 🚨 CORREÇÃO AQUI: Removi a coluna 'obs' para não dar conflito com seu banco de dados
    $status_inicial = 'pendente';
    $stmt_insert = $conn->prepare("INSERT INTO reservas (quarto_id, nome_cliente, whatsapp, checkin, checkout, status, valor_total, passeios_inclusos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Tirei o último 's' e a variável $obs do bind_param
    $stmt_insert->bind_param("isssssds", $quarto_id, $nome, $whatsapp, $checkin, $checkout, $status_inicial, $valor_total_quarto, $texto_passeios_bd);

    if ($stmt_insert->execute()) {
        $insert_id = $stmt_insert->insert_id;
        $stmt_insert->close();
        
        // Envio de E-mail de notificação para o Admin
        $para = "luanpsiquiatra2@proton.me"; 
        $assunto = "Nova Reserva: $nome - Pousada Solo Nunes";
        $mensagem = "
        <html><body style='font-family: Arial, sans-serif;'>
            <h2>Nova Reserva de Hospedagem!</h2>
            <p><strong>Cliente:</strong> " . htmlspecialchars($nome) . "</p>
            <p><strong>WhatsApp:</strong> " . htmlspecialchars($whatsapp) . "</p>
            <p><strong>Quarto:</strong> " . htmlspecialchars($nome_quarto) . "</p>
            <p><strong>Datas:</strong> " . date('d/m/Y', strtotime($checkin)) . " até " . date('d/m/Y', strtotime($checkout)) . " ($noites noites)</p>
            <p><strong>Hóspedes:</strong> $hospedes</p>
            <p><strong>Total Quarto:</strong> R$ " . number_format($valor_total_quarto, 2, ',', '.') . "</p>";
            
        if (!empty($nomes_passeios)) {
            $mensagem .= "<hr><h3>Passeios Desejados (Cobrar via WhatsApp):</h3>";
            $mensagem .= "<p><strong>Roteiro:</strong> " . htmlspecialchars($texto_passeios) . "</p>";
            $mensagem .= "<p><strong>Total Passeios:</strong> R$ " . number_format($valor_passeios_total, 2, ',', '.') . "</p>";
        }

        // A observação continua indo pro e-mail normalmente!
        $mensagem .= "<p><strong>Observações:</strong> " . htmlspecialchars($obs) . "</p></body></html>";
        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: luanpsiquiatra2@proton.me\r\n";
        mail($para, $assunto, $mensagem, $headers);

        // Prepara o link do WhatsApp para o cliente
        $numero_whats = "5592993138119";
        $texto_whats = "Olá! Fiz uma solicitação de Hospedagem na Pousada Solo Nunes.%0A%0A" .
                       "*Nome:* $nome%0A" .
                       "*Quarto:* $nome_quarto%0A" .
                       "*Período:* " . date('d/m/Y', strtotime($checkin)) . " a " . date('d/m/Y', strtotime($checkout)) . "%0A" .
                       "*Hóspedes:* $hospedes%0A";
                       
        if (!empty($nomes_passeios)) {
            $texto_whats .= "%0A*Quero os Seguintes Passeios:*%0A$texto_passeios%0A";
        }
        
        // A observação também vai na mensagem do WhatsApp
        if (!empty($obs)) {
            $texto_whats .= "%0A*Observação:* $obs%0A";
        }
        
        $texto_whats .= "%0A*Quero concluir minha reserva!*";
        $link_whatsapp = "https://wa.me/$numero_whats?text=" . $texto_whats;

        // Redireciona pra tela de pagamento passando APENAS o valor do quarto
        header("Location: ../php/pagamento.php?id=" . $insert_id . "&nome=" . urlencode($nome) . "&total=" . $valor_total_quarto . "&tipo=quarto&wa=" . urlencode($link_whatsapp));
        exit();

    } else {
        echo "Erro ao processar reserva: " . $stmt_insert->error;
        $stmt_insert->close();
    }
}
?>