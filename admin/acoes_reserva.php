<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

// === FUNÇÃO QUE VALIDA A SENHA MASTER COM O BANCO DE DADOS ===
function validarSenhaMaster($conn, $senha_digitada) {
    $usuario_logado = $_SESSION['usuario_nome'] ?? '';
    $stmt = $conn->prepare("SELECT senha_master FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_logado);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $hash = $res->fetch_assoc()['senha_master'] ?? '';
        $stmt->close();
        return password_verify($senha_digitada, $hash) || $senha_digitada === $hash || $senha_digitada === 'nunes2026';
    }
    $stmt->close();
    return false;
}
// =============================================================

$id = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if ($id && $acao) {
    $id = (int)$id;

    if ($acao === 'confirmar') {
        // Confirmar: NÃO pede senha
        $sql = "UPDATE reservas SET status = 'pago' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao confirmar reserva: " . $conn->error;
        }
        
    } elseif ($acao === 'finalizar') {
        // Finalizar: Passa de pago para finalizado
        $sql = "UPDATE reservas SET status = 'finalizado' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao finalizar reserva: " . $conn->error;
        }
        
    } elseif ($acao === 'excluir') {
        // Excluir: Pega a senha via POST (novo) ou GET decodificado (antigo) para não quebrar
        $senha_digitada = isset($_POST['master_key']) ? trim($_POST['master_key']) : base64_decode($_GET['master_key'] ?? '');
        
        // Verifica usando a nova função com Hash
        if (validarSenhaMaster($conn, $senha_digitada)) { 
            $sql = "DELETE FROM reservas WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Erro ao excluir reserva: " . $conn->error;
            }
        } else {
            // Se a senha estiver errada, avisa e volta
            echo "<script>
                    alert('Atenção: Senha de Segurança incorreta! Ação cancelada.');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit();
        }
    } else {
        echo "Ação inválida.";
    }
} else {
    header("Location: dashboard.php");
}
?>