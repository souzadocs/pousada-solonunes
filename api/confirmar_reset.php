<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

    // Atualiza a senha e limpa os campos de recuperação por segurança
    $sql = "UPDATE usuarios SET senha = '$nova_senha', recuperacao_token = NULL, recuperacao_expira = NULL 
            WHERE recuperacao_token = '$token'";

    if ($conn->query($sql)) {
        echo "<script>alert('Senha atualizada com sucesso!'); window.location.href='../admin/login.php';</script>";
    } else {
        echo "Erro ao atualizar senha.";
    }
}
?>