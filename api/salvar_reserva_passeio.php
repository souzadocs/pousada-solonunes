<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Captura de dados
    $nome = trim($_POST['nome_cliente'] ?? '');
    $whatsapp = trim($_POST['whatsapp_cliente'] ?? '');
    $passeio_id = (int)($_POST['passeio_id'] ?? 0);
    $data_passeio = $_POST['data_passeio'] ?? '';
    $qnt_pessoas = (int)($_POST['qnt_pessoas'] ?? 1);
    $tipo_veiculo = trim($_POST['tipo_veiculo'] ?? 'Não especificado');

    // Validação básica
    if (empty($nome) || empty($whatsapp) || empty($data_passeio) || $passeio_id === 0) {
        die("Erro: Por favor, preencha todos os campos obrigatórios. <a href='../php/reserva.php'>Voltar</a>");
    }

    $data_in = strtotime($data_passeio);
    $hoje = strtotime(date('Y-m-d'));

    if ($data_in < $hoje) {
        die("Erro: Você não pode agendar um passeio em uma data que já passou. <a href='../php/reserva.php'>Voltar</a>");
    }

    // Busca detalhes do passeio
    $stmt_passeio = $conn->prepare("SELECT nome, preco FROM passeios WHERE id = ?");
    $stmt_passeio->bind_param("i", $passeio_id);
    $stmt_passeio->execute();
    $res_passeio = $stmt_passeio->get_result();
    
    if ($res_passeio->num_rows === 0) {
        die("Erro: Passeio não encontrado no sistema.");
    }
    
    $passeio_info = $res_passeio->fetch_assoc();
    $nome_passeio = $passeio_info['nome'];
    $preco_pessoa = (float)$passeio_info['preco'];
    $stmt_passeio->close();

    // Calcula Total do Passeio
    $valor_total_passeio = $preco_pessoa * $qnt_pessoas;
    
    // Formata info da embarcação
    $veiculo_escolhido = "";
    if (!empty($tipo_veiculo) && $tipo_veiculo !== 'Não especificado') {
        $veiculo_escolhido = " (Embarcação: " . $tipo_veiculo . ")";
    }

    // 🚨 AQUI ESTÁ A CORREÇÃO: Removi a coluna 'capacidade' para não dar erro no seu banco
    $status_inicial = 'pendente';
    $stmt_insert = $conn->prepare("INSERT INTO reservas_passeios (passeio_id, nome_cliente, whatsapp, checkin, status, valor_total) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Ajustei os parâmetros (agora são 6 valores)
    $stmt_insert->bind_param("issssd", $passeio_id, $nome, $whatsapp, $data_passeio, $status_inicial, $valor_total_passeio);

    if ($stmt_insert->execute()) {
        $insert_id = $stmt_insert->insert_id;
        $stmt_insert->close();
        
        // Manda E-mail pro Admin
        $para = "luanpsiquiatra2@proton.me"; 
        $assunto = "Novo Agendamento de Passeio: $nome - Pousada Solo Nunes";
        $mensagem = "
        <html><body style='font-family: Arial, sans-serif;'>
            <h2>Novo Passeio Agendado!</h2>
            <p><strong>Cliente:</strong> " . htmlspecialchars($nome) . "</p>
            <p><strong>WhatsApp:</strong> " . htmlspecialchars($whatsapp) . "</p>
            <p><strong>Roteiro:</strong> " . htmlspecialchars($nome_passeio) . htmlspecialchars($veiculo_escolhido) . "</p>
            <p><strong>Data:</strong> " . date('d/m/Y', strtotime($data_passeio)) . "</p>
            <p><strong>Participantes:</strong> $qnt_pessoas pessoa(s)</p>
            <p><strong>Total (Cobrar no Zap):</strong> R$ " . number_format($valor_total_passeio, 2, ',', '.') . "</p>
        </body></html>";
        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: luanpsiquiatra2@proton.me\r\n";
        mail($para, $assunto, $mensagem, $headers);

        // Prepara o link do Zap para o cliente
        $numero_whats = "5592993138119";
        $texto_whats = "Olá! Agendei um Passeio pelo site da Solo Nunes.%0A%0A" .
                       "*Nome:* $nome%0A" .
                       "*Passeio:* $nome_passeio" . $veiculo_escolhido . "%0A" .
                       "*Data:* " . date('d/m/Y', strtotime($data_passeio)) . "%0A" .
                       "*Quantidade:* $qnt_pessoas pessoa(s)%0A" .
                       "*Valor Estimado:* R$ " . number_format($valor_total_passeio, 2, ',', '.') . "%0A%0A" .
                       "Gostaria de confirmar meu agendamento!";
        $link_whatsapp = "https://wa.me/$numero_whats?text=" . $texto_whats;

        // Redireciona pra tela de pagamento passando o tipo=passeio
        header("Location: ../php/pagamento.php?id=" . $insert_id . "&nome=" . urlencode($nome) . "&total=" . $valor_total_passeio . "&tipo=passeio&wa=" . urlencode($link_whatsapp));
        exit();

    } else {
        echo "Erro ao salvar agendamento do passeio: " . $stmt_insert->error;
        $stmt_insert->close();
    }
}
?>