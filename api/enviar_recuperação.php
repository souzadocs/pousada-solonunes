<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    
    // 1. Gera um token único e define expiração para 1 hora
    $token = bin2hex(random_bytes(32));
    $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // 2. Salva o token no banco para este usuário
    $sql = "UPDATE usuarios SET recuperacao_token = '$token', recuperacao_expira = '$expira' WHERE usuario = '$usuario'";
    
    if ($conn->query($sql) && $conn->affected_rows > 0) {
        // 3. Monta o link de recuperação
        $link = "http://localhost/pousadaa/admin/resetar_senha.php?token=" . $token;

        // 4. Envio do E-mail (Usando a mesma lógica das reservas)
        $para = "luanpsiquiatra2@proton.me";
        $assunto = "Recuperação de Senha - Solo Nunes";
        $mensagem = "Olá! Clique no link abaixo para criar uma nova senha. Este link expira em 1 hora.\n\n" . $link;
        $headers = "From: no-reply@solonunes.com.br";

        mail($para, $assunto, $mensagem, $headers);
        
        echo "<script>alert('Se o usuário existir, um link foi enviado para o e-mail cadastrado.'); window.location.href='../admin/login.php';</script>";
    } else {
        echo "<script>alert('Usuário não encontrado.'); window.history.back();</script>";
    }
}
?>