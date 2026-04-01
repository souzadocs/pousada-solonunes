<?php
session_start();
if (!isset($_SESSION['logado'])) { exit; }
include 'db.php';

// --- INÍCIO DA TRAVA DE SEGURANÇA ---
$SENHA_MASTER_CORRETA = "nunes2026"; 

// 1. Pega a senha da URL e decodifica (Vem do JavaScript)
$senha_recebida = isset($_GET['master_key']) ? base64_decode($_GET['master_key']) : '';

// 2. Compara com a senha oficial
if ($senha_recebida !== $SENHA_MASTER_CORRETA) {
    echo "<script>
            alert('Atenção: Senha de Segurança incorreta! O desbloqueio foi cancelado.'); 
            window.history.back();
          </script>";
    exit();
}
// --- FIM DA TRAVA DE SEGURANÇA ---

if (isset($_GET['id'])) {
    // 3. Proteção contra SQL Injection no ID
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 4. Deleta apenas se for status 'bloqueado' (Segurança extra para não apagar reserva real)
    $sql = "DELETE FROM reservas WHERE id = '$id' AND status = 'bloqueado'";
    $conn->query($sql);
}

header("Location: ../admin/quartos.php");
exit();
?>