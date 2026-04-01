<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nome_cliente = trim($_POST['nome_cliente']);
    $nome_empresa = trim($_POST['nome_empresa'] ?? '');
    $whatsapp = trim($_POST['whatsapp']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $qnt_pessoas = (int)$_POST['qnt_pessoas'];
    $obs = trim($_POST['obs']);

    $hoje = strtotime(date('Y-m-d'));
    if (strtotime($checkin) < $hoje) {
        die("Erro: Data no passado. <a href='../php/personalizado.php'>Voltar</a>");
    }

    // PREPARED STATEMENT
    $stmt = $conn->prepare("INSERT INTO reservas_personalizadas (nome_cliente, nome_empresa, whatsapp, checkin, checkout, qnt_pessoas, obs) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $nome_cliente, $nome_empresa, $whatsapp, $checkin, $checkout, $qnt_pessoas, $obs);

    if ($stmt->execute()) {
        $stmt->close();
        
        $para = "luanpsiquiatra2@proton.me"; 
        $assunto = "🚨 NOVO GRUPO/DELEGAÇÃO: $qnt_pessoas pessoas - Solo Nunes";
        $mensagem = "
        <html><body style='font-family: Arial;'>
            <h2 style='color: #4f46e5;'>Solicitação de Orçamento Personalizado</h2>
            <p><strong>Responsável:</strong> " . htmlspecialchars($nome_cliente) . "</p>
            <p><strong>Empresa:</strong> " . htmlspecialchars($nome_empresa) . "</p>
            <p><strong>WhatsApp:</strong> " . htmlspecialchars($whatsapp) . "</p>
            <p><strong>Período:</strong> " . date('d/m/Y', strtotime($checkin)) . " a " . date('d/m/Y', strtotime($checkout)) . "</p>
            <p><strong>Total de Pessoas:</strong> $qnt_pessoas</p>
            <p><strong>Observações:</strong> " . htmlspecialchars($obs) . "</p>
            <hr>
            <p>Acesse seu painel para enviar a proposta.</p>
        </body></html>";

        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: luanpsiquiatra2@proton.me\r\n";
        mail($para, $assunto, $mensagem, $headers);

        header("Location: ../php/sucesso_personalizado.php");
        exit();

    } else {
        echo "Erro ao processar: " . $stmt->error;
        $stmt->close();
    }
}
?>