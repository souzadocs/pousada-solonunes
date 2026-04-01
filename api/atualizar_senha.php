<?php
session_start();
if (!isset($_SESSION['logado'])) { exit; }
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $tipo = $_POST['tipo_senha'] ?? 'login';
    $nova_senha = trim($_POST['nova_senha']);
    $usuario = $_SESSION['usuario_nome']; 
    
    // Gera o hash seguro da nova senha
    $hash_novo = password_hash($nova_senha, PASSWORD_DEFAULT);
    
    // Define se vai atualizar a senha de login ou a senha master
    if ($tipo === 'master') {
        $stmt = $conn->prepare("UPDATE usuarios SET senha_master = ? WHERE usuario = ?");
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE usuario = ?");
    }
    
    $stmt->bind_param("ss", $hash_novo, $usuario);

    if ($stmt->execute()) {
        echo "<script>alert('Senha alterada com sucesso! Você já pode usar a nova senha.'); window.location.href='../admin/dashboard.php';</script>";
    } else {
        echo "Erro ao atualizar a senha: " . $stmt->error;
    }
    
    $stmt->close();
}
?>